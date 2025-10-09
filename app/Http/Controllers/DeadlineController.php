<?php

namespace App\Http\Controllers;

use App\Enums\DeadlineStatusEnum;
use App\Enums\SubtaskStatusEnum;
use App\Http\Requests\Deadline\StoreRequest;
use App\Http\Requests\Deadline\UpdateRequest;
use App\Models\Deadline;
use App\Models\Subtask;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeadlineController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $deadlines = Deadline::query()
            ->with('topic:id,name', 'subtasks')
            ->select('*')
            ->selectSub(function ($query) {
                $query->from('subtasks')
                    ->selectRaw('MIN(due_date)')
                    ->whereColumn('subtasks.deadline_id', 'deadlines.id');
            }, 'subtasks_min_due_date')
            ->orderByRaw("
                CASE
                    WHEN status = 4 THEN 0   -- Overdue
                    WHEN status = 1 THEN 1   -- In progress
                    WHEN status = 2 THEN 2   -- Completed
                    WHEN status = 3 THEN 3   -- Cancelled
                    ELSE 4
                END ASC,
                subtasks_min_due_date ASC
            ")
            ->get();



        return $this->successResponse($deadlines);
    }

    public function show($deadlineid)
    {
        $deadline = Deadline::query()
            ->with(['topic:id,name', 'subtasks'])
            ->select('*')
            ->selectSub(function ($query) {
                $query->from('subtasks')
                    ->selectRaw('MIN(due_date)')
                    ->whereColumn('subtasks.deadline_id', 'deadlines.id');
            }, 'subtasks_min_due_date')
            ->where('id', $deadlineid)
            ->firstOrFail();

        return $this->successResponse($deadline);
    }

    public function store(StoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $deadline = Deadline::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'status' => DeadlineStatusEnum::IN_PROGRESS,
                'priority' => $validated['priority'],
                'topic_id' => $validated['topic_id'] ?? null,
            ]);

            foreach ($validated['subtasks'] as $subtask) {
                Subtask::create([
                    'deadline_id' => $deadline->id,
                    'title' => $subtask['title'],
                    'content' => $subtask['content'],
                    'due_date' => $subtask['due_date'],
                    'status' => $subtask['status'] ?? SubtaskStatusEnum::IN_PROGRESS,
                ]);
            }

            DB::commit();
            return $this->successResponse($deadline);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create note', 500);
        }
    }

    public function update(Deadline $deadline, UpdateRequest $request)
    {
        DB::beginTransaction();

        try {
            // Cập nhật thông tin deadline chính
            $deadline->update([
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'topic_id' => $request->topic_id,
            ]);

            $existingSubtasks = $deadline->subtasks()->get();
            $newSubtasks = collect($request->input('subtasks', []));

            $subtasksWithId = $newSubtasks->whereNotNull('id');
            $subtasksWithoutId = $newSubtasks->whereNull('id');

            // Cập nhật các subtasks đã có ID
            $updatedSubtaskIds = [];
            foreach ($subtasksWithId as $subtaskData) {
                $subtask = $existingSubtasks->firstWhere('id', $subtaskData['id']);
                if ($subtask) {
                    $subtask->update([
                        'title' => $subtaskData['title'],
                        'content' => $subtaskData['content'] ?? null,
                        'due_date' => $subtaskData['due_date'],
                        'status' => $subtaskData['status'] ?? SubtaskStatusEnum::IN_PROGRESS,
                    ]);
                    $updatedSubtaskIds[] = $subtask->id; // lưu lại id
                }
            }

            // Thêm subtasks mới
            foreach ($subtasksWithoutId as $subtaskData) {
                $new = $deadline->subtasks()->create([
                    'title' => $subtaskData['title'],
                    'content' => $subtaskData['content'] ?? null,
                    'due_date' => $subtaskData['due_date'],
                    'status' => $subtaskData['status'] ?? SubtaskStatusEnum::IN_PROGRESS,
                ]);
                $updatedSubtaskIds[] = $new->id; // lưu id thực tế vừa tạo
            }

            // 🔹 Sau khi cập nhật & thêm mới xong, lấy danh sách ID thực sự có trong request
            $subtaskIdsInRequest = $newSubtasks
                ->pluck('id')
                ->filter() // loại null
                ->values()
                ->all();

            // 🔹 Xóa các subtasks không còn trong request
            $deadline->subtasks()
                ->whereNotIn('id', $updatedSubtaskIds)
                ->delete();

            // Cập nhật trạng thái tổng thể của deadline
            $deadline->refresh();
            $subtasks = $deadline->subtasks;

            if ($subtasks->every(fn($s) => $s->status == SubtaskStatusEnum::COMPLETED)) {
                $deadline->status = DeadlineStatusEnum::COMPLETED;
            } elseif ($subtasks->every(fn($s) => $s->status == SubtaskStatusEnum::CANCELLED)) {
                $deadline->status = DeadlineStatusEnum::CANCELLED;
            } elseif ($subtasks->contains(fn($s) => $s->status == SubtaskStatusEnum::OVERDUE)) {
                $deadline->status = DeadlineStatusEnum::OVERDUE;
            } else {
                $deadline->status = DeadlineStatusEnum::IN_PROGRESS;
            }

            $deadline->save();

            DB::commit();
            return $this->successResponse($deadline->load('subtasks'));

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update deadline: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Deadline $deadline)
    {
        try {
            $deadline->subtasks()->delete();
            $deadline->delete();
            return $this->successResponse($deadline, 'Deadline deleted successfully');
        }
        catch (\Exception $e) {
            return $this->errorResponse('Failed to delete note', 500);
        }
    }

    public function updateOverdue()
    {
        $today = Carbon::today();

        $count = Subtask::where('status', SubtaskStatusEnum::IN_PROGRESS)
            ->whereDate('due_date', '<', $today)
            ->update(['status' => SubtaskStatusEnum::OVERDUE]);

        $deadlineIds = Subtask::where('status', SubtaskStatusEnum::OVERDUE)
            ->whereDate('due_date', '<', $today)
            ->pluck('deadline_id')
            ->unique();

        foreach ($deadlineIds as $id) {
            $deadline = Deadline::with('subtasks')->find($id);

            if (!$deadline) {
                continue;
            }

            $subtasks = $deadline->subtasks;

            if ($subtasks->every(fn($s) => $s->status == SubtaskStatusEnum::COMPLETED)) {
                $deadline->status = DeadlineStatusEnum::COMPLETED;
            } elseif ($subtasks->every(fn($s) => $s->status == SubtaskStatusEnum::CANCELLED)) {
                $deadline->status = DeadlineStatusEnum::CANCELLED;
            } elseif ($subtasks->contains(fn($s) => $s->status == SubtaskStatusEnum::OVERDUE)) {
                $deadline->status = DeadlineStatusEnum::OVERDUE;
            } else {
                $deadline->status = DeadlineStatusEnum::IN_PROGRESS;
            }

            $deadline->save();
        }

        return response()->json([
            'message' => "Đã cập nhật {$count} subtasks quá hạn và đồng bộ trạng thái deadlines.",
            'updated_subtasks' => $count,
            'updated_deadlines' => $deadlineIds->count(),
        ]);
    }
}

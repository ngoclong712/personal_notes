<?php

namespace App\Http\Controllers;

use App\Enums\DeadlineStatusEnum;
use App\Enums\SubtaskStatusEnum;
use App\Http\Requests\Deadline\StoreRequest;
use App\Models\Deadline;
use App\Models\Subtask;
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
            ->findOrFail($deadlineid);

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
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Note\StoreRequest;
use App\Http\Requests\Note\UpdateRequest;
use App\Models\Note;
use App\Models\NoteAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        $notes = Note::query()
            ->with('topic:id,name')
            ->orderByDesc('id')
            ->get(['id','title','status','updated_at','topic_id']);

        return $this->successResponse($notes);
    }

    public function show($noteid)
    {
        $note = Note::query()
            ->with([
                'topic:id,name',
                'attachments:id,note_id,file_path,file_type,file_name,created_at'
            ])
            ->findOrFail($noteid);

        return $this->successResponse($note);
    }


    public function store(StoreRequest $request) {
        DB::beginTransaction();
        try {
            $note = Note::create($request->validated());
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public'); // storage/app/public/attachments

                    $note->attachments()->create([
                        'note_id' => $note->id,
                        'file_path' => Storage::url($path),
                        'file_type' => $file->getClientMimeType(),
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
            DB::commit();
            return $this->successResponse($note);
        }
        catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function update(Note $note, UpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // 1. Update thông tin note
            $note->update($request->validated());

            // 2. Xoá file cũ nếu có deleted_ids
            if ($request->filled('deleted_ids')) {
                $ids = $request->input('deleted_ids', []);
                $attachments = $note->attachments()->whereIn('id', $ids)->get();

                foreach ($attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file_path);
                    $attachment->delete();
                }
            }

            // 3. Upload file mới nếu có
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public');

                    $note->attachments()->create([
                        'note_id' => $note->id,
                        'file_path'  => $path,
                        'file_type'  => $file->getClientMimeType(),
                        'file_name'  => $file->getClientOriginalName(),
                    ]);
                }
            }

            DB::commit();
            return $this->successResponse($note->load('attachments'), 'Updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Update failed', 500, $e->getMessage());
        }
    }

    public function destroy(Note $note)
    {
        try {
            $attachments = $note->attachments;

            foreach ($attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }

                $attachment->delete();
            }

            $note->delete();

            return $this->successResponse([], 'Note and attachments deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete note', 500, $e->getMessage());
        }
    }
}

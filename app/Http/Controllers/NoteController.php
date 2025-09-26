<?php

namespace App\Http\Controllers;

use App\Http\Requests\Note\StoreRequest;
use App\Http\Requests\Note\UpdateRequest;
use App\Models\Note;
use Illuminate\Http\Request;

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
            ->with('topic:id,name')
            ->findOrFail($noteid);
        return $this->successResponse($note);
    }

    public function store(StoreRequest $request) {
        $note = Note::create($request->validated());
        return $this->successResponse($note);
    }

    public function update(Note $note, UpdateRequest $request) {
        $note->update($request->validated());
        return $this->successResponse($note, 'Updated');
    }
    public function destroy(Note $note) {
        $note->delete();
        return $this->successResponse([], 'Deleted');
    }
}

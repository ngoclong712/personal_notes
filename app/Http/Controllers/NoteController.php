<?php

namespace App\Http\Controllers;

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
}

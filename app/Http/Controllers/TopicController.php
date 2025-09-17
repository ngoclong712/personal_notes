<?php

namespace App\Http\Controllers;

use App\Http\Requests\Topic\StoreRequest;
use App\Http\Requests\Topic\UpdateRequest;
use App\Models\Topic;

class TopicController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $topics = Topic::query()
            ->orderByDesc('id')
            ->get(['id','name','description','created_at']);

        return $this->successResponse($topics);
    }

    public function show(Topic $topic)
    {
        return $this->successResponse($topic->only(['id','name','description']));
    }

    public function store(StoreRequest $request)
    {
        $topic = Topic::create($request->validated());
        return $this->successResponse($topic->only(['id','name','description']), 'Created');
    }

    public function update(Topic $topic, UpdateRequest $request)
    {
        $topic->update($request->validated());
        return $this->successResponse($topic->only(['id','name','description']), 'Updated');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();
        return $this->successResponse([], 'Deleted');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Topic\StoreRequest;
use App\Http\Requests\Topic\UpdateRequest;
use App\Imports\TopicsImport;
use App\Models\Topic;
use Maatwebsite\Excel\Facades\Excel;

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

    public function import()
    {
        try {
            $import = new TopicsImport();
            Excel::import($import, request()->file('file'));

            return response()->json([
                'message' => 'Đọc file thành công',
                'data' => $import->getData()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}

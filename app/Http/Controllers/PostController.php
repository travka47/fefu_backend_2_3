<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    private const PAGE_SIZE = 5;

    public function index() : JsonResponse
    {
        $posts = Post::with('user', 'comments')->ordered()->paginate(self::PAGE_SIZE);
        return PostResource::collection($posts)->response();
    }


    public function store(Request $request) : JsonResponse
    {
         $validator = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'text' => 'required|max:2000'
        ]);

         if ($validator->fails())
            return response()->json(['errors' => $validator->errors()->all()], 422);

        $post = new Post();
        $post->title = $validator->validated()['title'];
        $post->text = $validator->validated()['text'];
        $post->user_id = User::inRandomOrder()->first()->id;
        $post->save();

        return response()->json(new PostResource($post), 201);
    }


    public function show(Post $post) : JsonResponse
    {
        return response()->json(new PostResource($post));
    }


    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|max:100',
            'text' => 'sometimes|required|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        if (isset($validator->validated()['title']))
            $post->title = $validator->validated()['title'];
        if (isset($validator->validated()['text']))
            $post->text = $validator->validated()['text'];
        $post->save();

        return response()->json(new PostResource($post));
    }


    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['message' => 'Post removed successfully']);
    }
}

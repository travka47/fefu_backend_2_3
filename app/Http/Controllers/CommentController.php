<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    private const PAGE_SIZE = 5;

    public function index(Post $post) : JsonResponse
    {
        $comments = $post->comments()->with('user')->ordered()->paginate(self::PAGE_SIZE);
        return response()->json(CommentResource::collection($comments));

    }


    public function store(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|max:150'
        ]);

         if ($validator->fails())
            return response()->json(['errors' => $validator->errors()->all()], 422);

        $comment = new Comment();
        $comment->text = $validator->validated()['text'];
        $comment->user_id = User::inRandomOrder()->first()->id;
        $comment->post_id = $post->id;
        $comment->save();

        return response()->json(new CommentResource($comment), 201);

    }


    public function show(Comment $comment) : JsonResponse
    {
        return response()->json(new CommentResource($comment));
    }

    public function update(Request $request, Comment $comment)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|max:150'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        $comment->text = $validator->validated()['text'];
        $comment->save();

        return response()->json(new CommentResource($comment));
    }


    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->json(['message' => 'Comment removed successfully']);

    }
}

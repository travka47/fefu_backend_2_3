<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    private const PAGE_SIZE = 5;

    public function __construct() {
        $this->middleware('auth:sanctum', ['only' => ['store', 'update', 'destroy']]);
        $this->authorizeResource(Comment::class, 'comment', [
            'except' => ['index', 'show']
        ]);
    }

    public function index(Post $post) : JsonResponse
    {
        $comments = $post->comments()->with('user')->ordered()->paginate(self::PAGE_SIZE);
        return CommentResource::collection($comments)->response();
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
        $comment->user_id = Auth::user()->id;
        $comment->post_id = $post->id;
        $comment->save();

        return response()->json(new CommentResource($comment), 201);
    }


    public function show(Post $post, Comment $comment) : JsonResponse
    {
        return response()->json(new CommentResource($comment));
    }


    public function update(Request $request, Post $post, Comment $comment)
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


    public function destroy(Post $post, Comment $comment)
    {
        $comment->delete();
        return response()->json(['message' => 'Comment removed successfully']);
    }
}

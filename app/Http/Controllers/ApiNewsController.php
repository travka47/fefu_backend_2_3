<?php
namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psy\Util\Json;
use Illuminate\Support\Facades\Validator;

class ApiNewsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum', ['only' => ['store', 'update', 'destroy']]);
    }

    public function index() : JsonResponse
    {
        $news = News::query()
            ->where('is_published',true)
            ->where('published_at', '<=', 'NOW()')
            ->orderByRaw('published_at DESC, id DESC')
            ->paginate(5);
        return response()->json(NewsResource::collection($news));
    }

    public function store(Request $request) : JsonResponse
    {
        $this->authorize('create-news');
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'text' => 'required|string',
            'is_published' => 'required|boolean',
            'published_at' => 'required|date_format:Y-m-d H:i:s'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            return response()->json(['message' => $messages], 422);
        }

        $validated = $validator->validated();
        $news = new News();
        $news->title = $validated['title'];
        $news->description = $validated['description'];
        $news->text = $validated['text'];
        $news->is_published = $validated['is_published'];
        $news->published_at = $validated['published_at'];
        $news->save();

        return response()->json(new NewsResource($news), 201);
    }

    public function show(News $news) : JsonResponse
    {
        if ($news->is_published === false || $news->pubslshed_at > date("Y-m-d H:i:s")) {
            return response()->json(['message' => 'News not found'], 404);
        }
        return response()->json(new NewsResource($news));
    }

    public function update(Request $request, News $news) : JsonResponse
    {
        $this->authorize('update-news');
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'text' => 'sometimes|required|string',
            'is_published' => 'sometimes|required|boolean',
            'published_at' => 'sometimes|required|date_format:Y-m-d H:i:s'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            return response()->json(['message' => $messages], 422);
        }

        $validated = $validator->validated();
        if (isset($validated['title'])) {
            $news->title = $validated['title'];
        }
        if (isset($validated['description'])) {
            $news->description = $validated['description'];
        }
        if (isset($validated['text'])) {
            $news->text = $validated['text'];
        }
        if (isset($validated['is_published'])) {
            $news->is_published = $validated['is_published'];
        }
        if (isset($validated['published_at'])) {
            $news->published_at = $validated['published_at'];
        }
        $news->save();

        return response()->json(new NewsResource($news));
    }

    public function destroy(News $news) : JsonResponse
    {
        $this->authorize('delete-news');
        $news->delete();
        return response()->json(['message' => 'News removed successfully']);
    }
}

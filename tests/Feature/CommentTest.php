<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\CommentSeeder;
use Database\Seeders\PostSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        $this->seed(UserSeeder::class);
        $this->seed(PostSeeder::class);
        $this->seed(CommentSeeder::class);
    }

    public function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();;
    }

    public function test_index()
    {
        $post = Post::Factory()->has(Comment::Factory(3))->create();
        $response_json = $this->getJson("/api/posts/{$post->slug}/comments");
        $response_json->assertJsonStructure([
            'data' => [
                '*' => [
                    'author' => [
                        'name',
                        'login',
                        'email'
                    ],
                    'text',
                    'created_at',
                    'updated_at'
                ]
            ],
            'links',
            'meta'
        ]);

    }

    public function test_index_with_wrong_slug()
    {
        $response = $this->getJson("/api/posts/wrong_slug/comments");
        $response->assertStatus(404);
    }

    public function test_store_with_user()
    {
        $post = Post::Factory()->create();
        $user = User::Factory()->create();
        $comment = [
            'text' => 'test test'
        ];

        $response = $this->actingAs($user)->postJson("/api/posts/{$post->slug}/comments", $comment);
        $response
            ->assertStatus(201)
            ->assertJson($comment);
    }

    public function test_store_without_user()
    {
        $post = Post::Factory()->create();
        $comment = [
            'text' => 'test test'
        ];
        $response = $this->postJson("/api/posts/{$post->slug}/comments", $comment);
        $response
            ->assertStatus(401)
            ->assertExactJson([
                'message' => "Unauthenticated."
            ]);
    }

    public function test_store_validation()
    {
        $post = Post::Factory()->create();
        $user = User::Factory()->create();
        $comment = [];

        $response = $this->actingAs($user)->postJson("/api/posts/{$post->slug}/comments", $comment);
        $response
            ->assertStatus(422)
            ->assertExactJson([
                'errors' => [
                    'The text field is required.',
                ]
            ]);
    }

    public function test_update_by_moderator()
    {
        $post = Post::factory()->has(Comment::factory(3))->create();
        $comment_for_update = $post->comments()->first();
        $updated_comment = [
            'text' => 'New text for comment',
        ];
        $user = User::Factory()->create();
        $user->role = Role::MODERATOR;

        $response = $this->actingAs($user)->putJson("/api/posts/{$post->slug}/comments/{$comment_for_update->id}", $updated_comment);
        $response
            ->assertStatus(200)
            ->assertJson([
                'text' => 'New text for comment',
            ]);
    }

    public function test_update_by_author()
    {
        $user = User::Factory()->create();
        $post = Post::Factory()->has(Comment::factory(3)->for($user))->create();
        $comment_for_update = $post->comments()->first();
        $updated_comment = [
            'text' => 'New text for comment',
        ];

        $response = $this->actingAs($user)->putJson("/api/posts/{$post->slug}/comments/{$comment_for_update->id}", $updated_comment);
        $response
            ->assertStatus(200)
            ->assertJson([
                'text' => 'New text for comment',
            ]);
    }

    public function test_update_with_wrong_role()
    {
        $author = User::Factory()->create();
        $viewer = User::Factory()->create();
        $post = Post::Factory()->has(Comment::Factory(3)
            ->for($author))->create();
        $comment_for_update = $post->comments()->first();
        $updated_comment = [
            'text' => 'New text for comment',
        ];
        $response = $this->actingAs($viewer)
            ->putJson("/api/posts/{$post->slug}/comments/{$comment_for_update->id}", $updated_comment);
        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);
    }

    public function test_show_with_right_id()
    {
        $post = Post::Factory()->has(Comment::Factory(3))->create();
        $comment = $post->comments()->first();
        $author = User::query()->where('id', $comment->user_id)->first();

        $response = $this->getJson("/api/posts/{$post->slug}/comments/{$comment->id}");
        $response
            ->assertStatus(200)
            ->assertJson([
                'author' => [
                    'name' => $author->name,
                    'login' => $author->login,
                    'email' => $author->email,
                ],
                'text' => $comment->text,
            ]);
    }

    public function test_show_with_wrong_id()
    {
        $post = Post::Factory()->create();
        $response = $this->getJson("/api/posts/{$post->slug}/comments/100");
        $response->assertExactJson([
            'message' =>
                'Comment not found'
        ]);
    }

    public function test_delete_by_moderator()
    {
        $post = Post::Factory()->has(Comment::Factory(3))->create();
        $user = User::Factory()->create();
        $user->role = Role::MODERATOR;
        $comment_for_remove = $post->comments()->first();

        $response = $this->actingAs($user)->deleteJson("/api/posts/{$post->slug}/comments/{$comment_for_remove->id}");
        $response->assertExactJson([
            'message' =>
                'Comment removed successfully',
        ]);
    }

    public function test_delete_with_wrong_role()
    {
        $post = Post::factory()->has(Comment::factory(3))->create();
        $comment_for_delete = $post->comments()->first();
        $wrong_user = User::Factory()->create();
        $response = $this->actingAs($wrong_user)->deleteJson("/api/posts/{$post->slug}/comments/{$comment_for_delete->id}");
        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);
    }
}

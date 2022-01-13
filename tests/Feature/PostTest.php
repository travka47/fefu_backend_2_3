<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\PostSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        $this->seed(UserSeeder::class);
        $this->seed(PostSeeder::class);
    }

    public function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_index()
    {
        $response = $this->getJson('/api/posts');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'title',
                        'slug',
                        'text',
                        'created_at',
                        'updated_at',
                        'author' => [
                            'name',
                            'login',
                            'email'
                        ],
                        'comments' => [
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
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_store_without_user()
    {
        $post = [
            'title' => 'new post',
            'text' => 'this is text of new post'
        ];
        $response = $this->postJson('/api/posts', $post);
        $response
            ->assertStatus(401)
            ->assertExactJson([
                'message' => "Unauthenticated."
            ]);
    }

    public function test_store_with_user()
    {
        $post = [
            'title' => 'new post',
            'text' => 'this is text of new post'
        ];
        $user = User::Factory()->create();

        $response = $this->actingAs($user)->postJson('/api/posts', $post);
        $response
            ->assertStatus(201)
            ->assertJson($post);
    }

    public function test_store_validation()
    {
        $post = [
            'title' => 'Test post title',
        ];
        $user = User::Factory()->create();
        $response = $this->actingAs($user)->postJson('/api/posts', $post);
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
        $post = Post::Factory()->create();
        $updated_post = [
            'title' => 'Update post title',
        ];
        $user = User::Factory()->create();
        $user->role = Role::MODERATOR;

        $response = $this->actingAs($user)->putJson('/api/posts/' . $post->slug, $updated_post);
        $response
            ->assertStatus(200)
            ->assertJson([
                'title' => 'Update post title',
                'text' => $post->text,
            ]);
    }

    public function test_update_by_author()
    {
        $user = User::Factory()->create();
        $post = Post::Factory()->for($user)->create();
        $updated_post = [
            'title' => 'Update post title',
        ];

        $response = $this->actingAs($user)->putJson('/api/posts/' . $post->slug, $updated_post);
        $response
            ->assertStatus(200)
            ->assertJson([
                'title' => 'Update post title',
                'text' => $post->text,
            ]);
    }

    public function test_update_with_wrong_role()
    {
        $author = User::Factory()->create();
        $viewer = User::Factory()->create();
        $post = Post::Factory()->for($author)->create();
        $updated_post = [
            'title' => 'New title',
        ];
        $response = $this->actingAs($viewer)->putJson('/api/posts/' . $post->slug, $updated_post);
        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);
    }

    public function test_show_with_right_slug()
    {
        $post = Post::all()->first();
        $author = User::query()->where('id', $post->user_id)->first();
        $response = $this->getJson('/api/posts/' . $post->slug);
        $response
            ->assertStatus(200)
            ->assertJson([
                'title' => $post->title,
                'slug' => $post->slug,
                'text' => $post->text,
                'author' => [
                    'name' => $author->name,
                    'login' => $author->login,
                    'email' => $author->email,
                ]
            ]);
    }

    public function test_show_with_wrong_slug()
    {
        $response = $this->getJson('/api/posts/wrong_slug');
        $response->assertExactJson([
            'message' => 'Post not found'
        ]);
    }

    public function test_delete_by_moderator()
    {
        $post = Post::Factory()->create();
        $user = User::Factory()->create();
        $user->role = Role::MODERATOR;

        $response = $this->actingAs($user)->deleteJson('/api/posts/' . $post->slug);
        $response->assertExactJson([
            'message' =>
                'Post removed successfully',
        ]);
    }

    public function test_delete_by_author()
    {
        $user = User::Factory()->create();
        $post = Post::Factory()->for($user)->create();

        $response = $this->actingAs($user)->deleteJson('/api/posts/' . $post->slug);
        $response->assertExactJson([
            'message' =>
                'Post removed successfully',
        ]);
    }

    public function test_delete_with_wrong_role()
    {
        $post = Post::all()->first();
        $wrong_user = User::Factory()->create();
        $response = $this->actingAs($wrong_user)->deleteJson('/api/posts/' . $post->slug);
        $response
            ->assertStatus(403)
            ->assertJson([
                'message' =>
                    'This action is unauthorized.'
            ]);
    }
}

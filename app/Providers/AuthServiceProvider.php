<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\News;
use App\Models\Post;
use App\Policies\NewsPolicy;
use App\Policies\PostPolicy;
use App\Models\Comment;
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        News::class => NewsPolicy::class,
        Post::class => PostPolicy::class,
        Comment::class => CommentPolicy::class
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->role == Role::ADMIN){
                return true;
            }
        });
    }
}

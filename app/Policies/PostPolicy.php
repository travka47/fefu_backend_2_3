<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user)
    {
        return true;
    }

    public function view(?User $user)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Post $post) {
        return ($user->role == Role::MODERATOR || $user->id === $post->user_id);
    }

    public function delete(User $user, Post $post)
    {
        if ($user->role == Role::MODERATOR) {
            return true;
        } else if ($user->id === $post->user_id) {
            $comments = $post->comments()
                ->where('user_id','!=', $user->id)
                ->first();
            if ($comments === null) {
                return true;
            }
        }
        return false;
    }
}

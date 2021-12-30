<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        return view('profile', ['user'=>new UserResource($user)]);
    }
}

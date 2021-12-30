<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function __invoke(Request $request)
        {
            if (Auth::check())
                return redirect()->route('profile');

            if ($request->isMethod('post')) {
                $request['login'] = strtolower($request['login']);
                $validated = $request->validate([
                    'login' => 'unique:users|required|between:5, 30|regex: /^[a-z0-9\-._]+$/i',
                    'password' => 'required|between:10, 30|regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&].{10,}$/'
                ]);

                $user = new User();
                $user->login = $validated['login'];
                $user->password = Hash::make($validated['password']);
                $user->save();
                Auth::login($user);
                return redirect()->route('profile');
            }
            return view('registration');
        }
}

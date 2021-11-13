<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Http\Requests\AppealPostRequest;
use App\Models\Appeal;
use App\Sanitizers\PhoneSanitizer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppealController extends Controller
{
    public function __invoke(Request $request)
    {
        $suggestion_shown = false;
        if ($request->input('suggested') !== null) {
            $suggestion_shown = !session('suggestion_shown', false);
            if ($suggestion_shown) {
                session()->put('suggestion_shown', true);
            }
        }

        if ($request->isMethod('post'))
        {
            $validated = $request->validate(AppealPostRequest::rules());

            $appeal = new Appeal();
            $appeal->name = $validated['name'];
            $appeal->surname = $validated['surname'];
            $appeal->patronymic = $validated['patronymic'];
            $appeal->age = $validated['age'];
            $appeal->gender = $validated['gender'];
            $appeal->phone = PhoneSanitizer::sanitize($validated['phone']);
            $appeal->email = $validated['email'];
            $appeal->message = $validated['message'];
            $appeal->save();
            $request->session()->put('appealed', true);

            return redirect()->route('appeal');
        }

        return view('appeal', ['suggestion_shown' => $suggestion_shown]);
    }
}

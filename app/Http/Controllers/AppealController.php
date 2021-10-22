<?php

namespace App\Http\Controllers;

use App\Models\Appeal;
use Illuminate\Http\Request;

class AppealController extends Controller
{
    public function __invoke(Request $request)
    {
        $errors = [];
        $success = $request->session()->get('success', false);

        if ($request->isMethod('post'))
        {
            $name = $request->input('name');
            $phone = $request->input('phone');
            $email = $request->input('email');
            $message = $request->input('message');

            if ($name === null) {
                $errors['name'] = 'Name is empty';
            }
            if (strlen($name) > 20) {
                $errors['nameSize'] = 'Length of name should contain up to 20 symbols';
            }
            if (strlen($phone) > 11) {
                $errors['phoneSize'] = 'Length of phone should contain up to 11 symbols';
            }
            if (strlen($email) > 100) {
                $errors['emailSize'] = 'Length of email should contain up to 100 symbols';
            }
            if ($phone === null && $email === null) {
                $errors['contacts'] = 'Leave contact details';
            }
            if ($message === null) {
                $errors['message'] = 'Message is empty';
            }
            if (strlen($message) > 100) {
                $errors['messageSize'] = 'Length of message should contain up to 100 symbols';
            }

            if (count($errors) > 0) {
                $request->flash();
            }
            else {
                $appeal = new Appeal();
                $appeal->name = $name;
                $appeal->phone = $phone;
                $appeal->email = $email;
                $appeal->message = $message;
                $appeal->save();

                $success = true;

                return redirect()
                    ->route('appeal')
                    ->with('success', $success);

            }
        }
        return view('appeal', ['errors' => $errors, 'success' => $success]);
    }
}

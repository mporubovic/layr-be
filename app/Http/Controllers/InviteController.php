<?php

namespace App\Http\Controllers;

use App\Invite;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class InviteController extends Controller
{
    public function checkToken(Request $request)
    {

        $request->validate([
            'token' => 'required|string|min:32',
        ]);


        $invite = Invite::where('token', $request->token)->first();

        if (!$invite) {
            abort(404);
        }

        return response()->noContent();

    }

    public function acceptToken(Request $request) 
    {
        $request->validate([
            'token' => 'required|string|min:32',
            'password' => 'required|string|min:6',
        ]);


        $invite = Invite::where('token', $request->token)->first();

        if (!$invite) {
            abort(404);
        }

        $student = User::find($invite->user_id);
        $student->email_verified_at = now();
        $student->fill(['password' => Hash::make($request->password)])->save();

        $invite->delete();

        Auth::login($student);

        return;





    }

}

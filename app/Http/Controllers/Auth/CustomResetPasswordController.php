<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

use App\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use App\Mail\PasswordReset;



class CustomResetPasswordController extends Controller
{

    public function sendResetEmail(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);
        
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response(null, 200);
        }

        if (DB::table('password_resets')->where('user_id', $user->id)->first()) {
            return response(null, 200);
        }

        $token = Str::random(32);

        DB::table('password_resets')->insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => now()
        ]);

        Mail::to($user->email)->queue(new PasswordReset($user->name, $token));

    }

    public function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:6',
        ]);
        
        $userId = DB::table('password_resets')->where('token', $request->token)->value('user_id');
        
        if (!$userId) {
            abort(404);
        }
        
        $user = User::find($userId);
        
        $user->fill(['password' => Hash::make($request->password)])->save();
        
        DB::table('password_resets')->where('token', $request->token)->delete();
        
        Auth::login($user);
    }


}

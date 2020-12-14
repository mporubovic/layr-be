<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Subdomain;
use App\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

use Illuminate\Http\Client\RequestException;

class ServiceController extends Controller
{
    public function checkEmail(Request $request) {
        $email = $request->email;

        $user = User::where('email', $email)->first();

        if (! $user) {
            return abort(404, 'User not found');
        }

        $role = $user->roles->first();
        return [ 'role' => $role->name];
    }
}

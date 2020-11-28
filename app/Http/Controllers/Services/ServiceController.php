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
    public function siteTitle(Request $request) {
        $site = $request->site;
        // $encodedUrl = urlencode($site);
        // $response = Http::get($site);
        try {
            // $response = Http::withOptions([
            //     'http_errors' => false,
            // ])->get($site);
            // $response = Http::get($site);
            // $response = Http::get($site);
            $response = Http::timeout(3)->get($site);
            // return $response;

        } catch (\Exception $e) {
            // return $response->serverError();
            // return "ERROR";
            abort(400);
        }
        $pattern = "|<[\s]*title[\s]*>([^<]+)<[\s]*/[\s]*title[\s]*>|Ui";

        // return [$response, $site];
        if (preg_match($pattern, $response, $match)) {
            return html_entity_decode($match[1], ENT_QUOTES); 
        } else {
            // return false;
            abort(422);
        }

        // return $response->serverError();
        // return "h2";
        // return $response;
        // if ($response->serverError()) return abort(400);
        // return [$response, $site];
        // $response = file_get_contents('https://www.bbc.com/');
        

    }

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

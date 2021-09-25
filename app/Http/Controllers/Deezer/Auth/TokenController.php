<?php

namespace App\Http\Controllers\Deezer\Auth;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TokenController extends Controller
{
    public function setAccessToken(Request $request): bool
    {
        try {
            $deezer_app_id = config('deezer.app_id');
            $deezer_secret = config('deezer.secret');

            $client = new Client();
            $response = $client->request('POST', "https://connect.deezer.com/oauth/access_token.php?app_id=${deezer_app_id}&secret=${deezer_secret}&code={$request->code}&output=json");
            Cache::add('deezer_access_token', json_decode($response->getBody()->getContents())->access_token, 3600);
            return true;
        } catch (\Throwable$th) {
            return false;
        }
    }
}

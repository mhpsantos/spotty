<?php

namespace App\Http\Controllers\Spotify\Auth;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TokenController extends Controller
{
    public function setAccessToken(Request $request): bool
    {
        try {
            $client = new Client();

            Cache::add('spotify_access_token', json_decode($client->request('POST', "https://accounts.spotify.com/api/token", [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => config('spotify.redirect_uri'),
                    'client_id' => config('spotify.client_id'),
                    'client_secret' => config('spotify.client_secret'),
                    'code' => $request->code,
                ],
                'allow_redirects' => true,
            ])->getBody()->getContents())->access_token, 3600);

            return true;
        } catch (\Throwable$th) {
            return false;
        }
    }
}

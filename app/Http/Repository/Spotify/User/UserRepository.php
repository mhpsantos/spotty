<?php

namespace App\Http\Repository\Spotify\User;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class UserRepository
{

    public function getUserId()
    {
        $client = new Client();

        $response = $client->request('GET', 'https://api.spotify.com/v1/me', [
            'headers' => [
                "Authorization" => 'Bearer ' . Cache::get('spotify_access_token'),
            ],
        ]);
        return json_decode($response->getBody()->getContents())->id;
    }

    public function isLoggedIn()
    {
        try {
            $client = new Client();

            $response = $client->request('GET', 'https://api.spotify.com/v1/me', [
                'headers' => [
                    "Authorization" => 'Bearer ' . Cache::get('spotify_access_token'),
                ],
            ]);
            if (isset(json_decode($response->getBody()->getContents())->id)) {
                return true;
            }
            return false;
        } catch (\Exception$e) {
            return false;
        }
    }

}

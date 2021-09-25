<?php

namespace App\Http\Repository\Deezer\User;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

/**
 *
 */
class UserRepository
{
    public function getUserId()
    {
        try {
            $deezer_api_token = Cache::get('deezer_access_token', 'default');

            $client = new Client();

            return json_decode($$client->request('GET', "https://api.deezer.com/user/me&access_token=${deezer_api_token}")->getBody()->getContents())->id;
        } catch (\Exception$e) {
            return $e;
        }
    }

    public function isLoggedIn()
    {
        $deezer_api_token = Cache::get('deezer_access_token', 'default');

        $client = new Client();

        if (isset(json_decode($client->request('GET', "https://api.deezer.com/user/me&access_token=${deezer_api_token}")->getBody()->getContents())->firstname)) {
            return true;
        }
        return redirect('deezer/auth');
    }
}

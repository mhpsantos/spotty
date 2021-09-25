<?php

namespace App\Http\Repository\Deezer\Library;

use App\Http\Repository\Deezer\User\UserRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

/**
 *
 */
class LibraryRepository
{
    private $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function getSongId(string $query)
    {
        try {
            $deezer_api_token = Cache::get('deezer_access_token', 'default');

            $client = new Client();
            $response = $client->request('GET', "https://api.deezer.com/search?q=${query}&access_token=${deezer_api_token}");

            return json_decode($response->getBody()->getContents())->data[0]->id;

        } catch (\Exception$e) {
            return $e;
        }
    }

    public function createPlaylist($playlistName)
    {
        try {
            $deezer_api_token = Cache::get('deezer_access_token', 'default');
            $user_id = $this->userRepository->getUserId();

            $client = new Client();
            $response = $client->request('POST', "https://api.deezer.com/user/${user_id}/playlists&title=${playlistName}&access_token=${deezer_api_token}");
            return json_decode($response->getBody()->getContents())->id;
        } catch (\Exception$e) {
            return $e;
        }
    }
}

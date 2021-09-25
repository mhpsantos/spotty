<?php

namespace App\Http\Repository\Spotify\Library;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;


/**
 *
 */
class LibraryRepository
{
    public function getPlaylist($playlist_id)
    {
        $headers = ['Authorization' => 'Bearer ' . Cache::get('spotify_access_token')];
        $client = new Client();
        $response = $client->request('GET', "https://api.spotify.com/v1/playlists/${playlist_id}/tracks", [
            'headers' => $headers,
        ]);
        $tracks = array();
        foreach (json_decode($response->getBody()->getContents())->items as $item) {
            $artists = $item->track->artists;
            $info = $item->track->name . " - " . $artists[0]->name;
            array_push($tracks, $info);
        }
        return $tracks;
    }

    public static function getPlaylists()
    {
        $client = new Client();

        $response = $client->request('GET', 'https://api.spotify.com/v1/me/playlists?limit=50', [
            'headers' => [
                'Authorization' => 'Bearer ' . Cache::get('spotify_access_token'),
            ],
        ]);
        $playlists = array();
        foreach (json_decode($response->getBody()->getContents())->items as $item) {
            array_push($playlists, ['name' => $item->name, 'id' => $item->id, 'images' => $item->images]);
        }
        return $playlists;
    }
}

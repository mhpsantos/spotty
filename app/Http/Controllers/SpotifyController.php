<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Client;

use Illuminate\Support\Facades\Cache;

class SpotifyController extends Controller
{
    static function isLoggedIn(){
        try {
            $client = new Client;

            $response = $client->request('GET', 'https://api.spotify.com/v1/me',[
                'headers' => [
                    "Authorization" => 'Bearer ' . Cache::get('spotify_access_token', 'default'),
                ]
            ]);
            $display_name = json_decode($response->getBody()->getContents())->display_name;
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getPlaylist($playlist_id){
        $headers = ["Authorization" => 'Bearer ' . Cache::get('spotify_access_token', 'default')];
        $client = new Client;
        $response = $client->request('GET', "https://api.spotify.com/v1/playlists/$playlist_id/tracks",[
            'headers' => $headers,
        ]);
        $tracks = array();
        foreach (json_decode($response->getBody()->getContents())->items as  $item) {
            $artists = $item->track->artists;
            $info = $item->track->name . " - " . $artists[0]->name;
            array_push($tracks,$info);
        }
        return $tracks;
    }

    public static function getUserId(){
        $client = new Client;

        $response = $client->request('GET', 'https://api.spotify.com/v1/me',[
            'headers' => [
                "Authorization" => 'Bearer ' . Cache::get('spotify_access_token', 'default'),
            ]
        ]);
        return json_decode($response->getBody()->getContents())->id;
    }

    public static function getPlaylists(){
        $client = new Client;

        $response = $client->request('GET', 'https://api.spotify.com/v1/me/playlists?limit=50',[
            'headers' => [
                "Authorization" => 'Bearer ' . Cache::get('spotify_access_token', 'default'),
            ],
        ]);
        $playlists = array();
        foreach (json_decode($response->getBody()->getContents())->items as $item){
            array_push($playlists,['name' => $item->name, 'id' => $item->id, 'images'=> $item->images]);
        }
        return $playlists;

    }
}

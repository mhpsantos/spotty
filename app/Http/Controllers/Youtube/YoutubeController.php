<?php

namespace App\Http\Controllers;

use App\Http\Controllers\SpotifyController as Spotify;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class YoutubeController extends Controller
{
    public static function createPlaylist($playlistName)
    {
        try {

            $client = new Client;
            $response = $client->request('POST', 'https://www.googleapis.com/youtube/v3/playlists?part=snippet&part=status', [
                'headers' => [
                    "Authorization" => 'Bearer ' . Cache::get('youtube_access_token'),
                ],
                'json' => [
                    'snippet' => [
                        'title' => $playlistName,
                    ],
                    'status' => [
                        'privacyStatus' => 'private',
                    ],
                ],
            ]);

            return json_decode($response->getBody()->getContents())->id;

        } catch (\GuzzleHttp\Exception\ClientException$e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
    }

    public static function getYoutubeVideoId($query)
    {
        try {
            $client = new Client;
            $response = $client->request('GET', "https://www.googleapis.com/youtube/v3/search?part=id&q=$query+audio&maxResults=1", [
                'headers' => [
                    "Authorization" => 'Bearer ' . Cache::get('youtube_access_token'),
                ],
            ]);

            return json_decode($response->getBody()->getContents())->items[0]->id->videoId;

        } catch (\GuzzleHttp\Exception\ClientException$e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
    }

    public static function insertToPlaylist($videoId, $playlistId)
    {
        try {

            $client = new Client;
            $response = $client->request('POST', 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet', [
                'headers' => [
                    "Authorization" => 'Bearer ' . Cache::get('youtube_access_token'),
                ],
                'json' => [
                    'snippet' => [
                        'playlistId' => $playlistId,
                        'resourceId' => [
                            'videoId' => $videoId,
                            'kind' => 'youtube#video',
                        ],
                    ],
                ],
            ]);

            return true;

        } catch (\GuzzleHttp\Exception\ClientException$e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
    }

    public static function convertFromSpotify(Request $request)
    {
        try {
            if(Cache::get('youtube_access_token')){
                $tracks = Spotify::getPlaylist($request->playlistId);
                $playlistId = YoutubeController::createPlaylist(($request->newPlaylistName));

                foreach ($tracks as $track) {
                    YoutubeController::insertToPlaylist(YoutubeController::getYoutubeVideoId($track), $playlistId);
                }
                return redirect('/')->with('status', "Convertido com sucesso.");
            }else{
                return redirect('/youtube/auth');
            }
        } catch (\Exception$e) {
            return redirect('/')->with('error', $e);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\SpotifyController as Spotify;
use Illuminate\Support\Facades\Cache;
class YoutubeController extends Controller
{
    static function createPlaylist($playlistName){
        try {
                
            $client = new Client;
            $response = $client->request('POST','https://www.googleapis.com/youtube/v3/playlists?part=snippet&part=status',[
                'headers' => [
                    "Authorization" => 'Bearer ' . Cache::get('youtube_access_token', 'default'),
                ],
                'json' => [
                    'snippet' => [
                        'title' => $playlistName,
                    ],
                    'status' => [
                        'privacyStatus' => 'private',
                    ],
                ]
            ]);

            return json_decode($response->getBody()->getContents())->id;
    
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
    }

    static function getYoutubeVideoId($query){
        try {
                
            $client = new Client;
            $response = $client->request('GET',"https://www.googleapis.com/youtube/v3/search?part=id&q=$query&maxResults=1",[
                'headers' => [
                    "Authorization" => 'Bearer ' . Cache::get('youtube_access_token', 'default'),
                ],
            ]);

            return json_decode($response->getBody()->getContents())->items[0]->id->videoId;
    
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
    }

    static function insertToPlaylist($videoId, $playlistId){
        try {
                
            $client = new Client;
            $response = $client->request('POST','https://www.googleapis.com/youtube/v3/playlistItems?part=snippet',[
                'headers' => [
                    "Authorization" => 'Bearer ' . Cache::get('youtube_access_token', 'default'),
                ],
                'json' => [
                    'snippet' => [
                        'playlistId' => $playlistId,
                        'resourceId' => [
                            'videoId' => $videoId,
                            'kind' => 'youtube#video'
                        ]
                    ]
                ]
            ]);

            return true;
    
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
    }

    static function convertFromSpotify(Request $request){
        try {
            $tracks = Spotify::getPlaylist($request->playlistId);
            $playlistId = YoutubeController::createPlaylist(($request->newPlaylistName));

            foreach ($tracks as $track) {
                YoutubeController::insertToPlaylist(YoutubeController::getYoutubeVideoId($track), $playlistId);
            }
            print_r("convertido com sucesso");
        } catch (\Exception $e) {
            print_r($e);
        }
    }
}

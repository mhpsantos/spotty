<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\SpotifyController as Spotify;

use GuzzleHttp\Client;

use Illuminate\Support\Facades\Cache;

class DeezerController extends Controller
{
    
    static function isLoggedIn(){
        $deezer_api_token = Cache::get('deezer_access_token', 'default');

        $client = new Client;
        $response = $client->request('GET', "https://api.deezer.com/user/me&access_token=$deezer_api_token");
        
        if(isset(json_decode($response->getBody()->getContents())->firstname)){
            return true;
        }else{
            return redirect('deezer/auth');;
        }
    }

    static function getDeezerSongId($query){
        try {
            $deezer_api_token = Cache::get('deezer_access_token', 'default');

            $client = new Client;
            $response = $client->request('GET', "https://api.deezer.com/search?q=$query&access_token=$deezer_api_token");

            return json_decode($response->getBody()->getContents())->data[0]->id;
            
        } catch (\Exception $e) {
            return $e;
        }
    }

    static function convertFromSpotify(Request $request){
        try {
            $deezer_api_token = Cache::get('deezer_access_token', 'default');
            $tracks = Spotify::getPlaylist($request->playlistId);
            $playlistId = DeezerController::createPlaylist($request->newPlaylistName);
    
            $newTracks = '';
            foreach ($tracks as $item) {
                $newTracks .= DeezerController::getDeezerSongId($item) . ",";
            }
    
            $client = new Client;
            $response = $client->request('POST', "https://api.deezer.com/playlist/$playlistId/tracks&songs=$newTracks&access_token=$deezer_api_token");
    
            print_r($response->getBody()->getContents());
        } catch (\Exception $e) {
            return $e;
        }
    }

    static function getUserId(){
        try {
            $deezer_api_token = Cache::get('deezer_access_token', 'default');

            $client = new Client;
            $response = $client->request('GET', "https://api.deezer.com/user/me&access_token=$deezer_api_token");

            return json_decode($response->getBody()->getContents())->id;
        } catch (\Exception $e) {
            return $e;
        }
    }
    static function createPlaylist($playlistName){
        try {
            $deezer_api_token = Cache::get('deezer_access_token', 'default');
            $user_id = DeezerController::getUserId();

            $client = new Client;
            $response = $client->request('POST', "https://api.deezer.com/user/$user_id/playlists&title=$playlistName&access_token=$deezer_api_token");
            return json_decode($response->getBody()->getContents())->id;
        } catch (\Exception $e) {
            return $e;
        }
    }
}

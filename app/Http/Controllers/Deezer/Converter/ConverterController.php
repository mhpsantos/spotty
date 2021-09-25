<?php

namespace App\Http\Controllers\Deezer\Converter;

use App\Http\Controllers\Controller;
use App\Http\Traits\Deezer\Library\LibraryTrait as DeezerLibrary;
use App\Http\Traits\Spotify\Library\LibraryTrait as SpotifyLibrary;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConverterController extends Controller
{
    use DeezerLibrary, SpotifyLibrary;

    public function convertFromSpotify(Request $request)
    {
        try {
            $deezer_api_token = Cache::get('deezer_access_token', 'default');
            $tracks = $this->getPlaylist($request->playlistId);
            $playlistId = $this->createPlaylist($request->newPlaylistName);

            $newTracks = '';
            foreach ($tracks as $item) {
                $newTracks .= $this->getSongId($item) . ",";
            }

            $client = new Client();
            $client->request('POST', "https://api.deezer.com/playlist/${playlistId}/tracks&songs=${newTracks}&access_token=${deezer_api_token}");

            return redirect('/')->with('status', "Convertido com sucesso.");
        } catch (\Exception$e) {
            return redirect('/')->with('error', $e);
        }
    }

}

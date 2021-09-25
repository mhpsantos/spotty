<?php

use App\Http\Controllers\Deezer\DeezerController as Deezer;
use App\Http\Controllers\Spotify\SpotifyController as Spotify;
use App\Http\Controllers\YoutubeController as Youtube;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home');

Route::view('authenticated', 'authenticated');

Route::get('test', function () {
    return redirect('/')->with('status', "Convertido com sucesso");;
});

Route::get('fromspotify', function () {
    if (Cache::get('spotify_access_token')) {
        return view('fromspotify');
    }
    return redirect('spotify/auth');;

});

Route::get('deezer/auth', function (Request $request) {
    $app_id = config('deezer.app_id');
    $redirect_uri = config('deezer.redirect_uri');

    return redirect("https://connect.deezer.com/oauth/auth.php?app_id=${app_id}&redirect_uri=${redirect_uri}&perms=manage_library");
});

Route::get('deezer/code/', [Deezer::class, 'authenticate']);

Route::get('spotify/auth', function () {
    $client_id = config('spotify.client_id');
    $redirect_uri = config('spotify.redirect_uri');
    $scopes = 'user-read-private playlist-read-private playlist-modify-private';

    return redirect("https://accounts.spotify.com/authorize?client_id=${client_id}&redirect_uri=${redirect_uri}&scope=${scopes}&response_type=code");

});

Route::get("spotify/code", [Spotify::class, 'authenticate']);

Route::get('youtube/auth', function () {
    $client_id = config('youtube.client_id');
    $redirect_uri = config('youtube.redirect_uri');
    $scopes = 'https://www.googleapis.com/auth/youtube';

    return redirect("https://accounts.google.com/o/oauth2/auth?client_id=$client_id&redirect_uri=$redirect_uri&scope=$scopes&response_type=code&access_type=offline");
});

Route::get('youtube/code', function (Request $request) {

    $body = [
        'grant_type' => 'authorization_code',
        'redirect_uri' => config('youtube.redirect_uri'),
        'client_id' => config('youtube.client_id'),
        'client_secret' => config('youtube.client_secret'),
        'code' => $request->code,
    ];

    $client = new Client;

    $response = $client->request('POST', "https://accounts.google.com/o/oauth2/token", [
        'form_params' => $body,
        'allow_redirects' => true,
    ]);

    Cache::add('youtube_access_token', json_decode($response->getBody()->getContents())->access_token, 3600);

    return redirect('/');
});

Route::post('spotifytodeezer', [Deezer::class, 'convertFromSpotify']);

Route::post('spotifytoyoutube', [Youtube::class, 'convertFromSpotify']);

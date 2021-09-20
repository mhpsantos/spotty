<?php

use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\SpotifyController as Spotify;
use App\Http\Controllers\DeezerController as Deezer;
use Illuminate\Support\Facades\Cache;

Route::view('test', 'test');

Route::view('authenticated', 'authenticated');

Route::view('fromspotify', 'fromspotify');

Route::view('/', 'home');

Route::get('deezer/auth', function (Request $request) {
    
    $app_id = config('deezer.app_id');
    $redirect_uri = config('deezer.redirect_uri');

    return redirect("https://connect.deezer.com/oauth/auth.php?app_id=$app_id&redirect_uri=$redirect_uri&perms=manage_library");
});

Route::get('deezer/code', function (Request $request) {
    $deezer_app_id = config('deezer.app_id');

    $deezer_secret = config('deezer.secret');
    $client = new Client;

    $response = $client->request('POST', "https://connect.deezer.com/oauth/access_token.php?app_id=$deezer_app_id&secret=$deezer_secret&code=$request->code&output=json");

    Cache::add('deezer_access_token', json_decode($response->getBody()->getContents())->access_token, 3600);

    return redirect('/');
});


Route::get("spotify/code", function (Request $request) {
    
    $headers = ["Authorization" => 'Basic ' . base64_encode(config('spotify.client_id') . ":" . config('spotify.client_secret'))];

    $body = [
        'grant_type' => 'authorization_code',
        'redirect_uri' => config('spotify.redirect_uri'),
        'client_id' => config('spotify.client_id'),
        'client_secret' => config('spotify.client_secret'),
        'code' => $request->code,
    ];

    $client = new Client;

    $response = $client->request('POST', "https://accounts.spotify.com/api/token",[
        'form_params' => $body,
        'allow_redirects' => true,
    ]);

    Cache::add('spotify_access_token', json_decode($response->getBody()->getContents())->access_token, 3600);

    return redirect("/");
});

Route::get('spotify/auth', function () {
    $client_id = config('spotify.client_id');
    $redirect_uri = config('spotify.redirect_uri');
    $scopes = 'user-read-private playlist-read-private playlist-modify-private';

    return redirect("https://accounts.spotify.com/authorize?client_id=$client_id&redirect_uri=$redirect_uri&scope=$scopes&response_type=code");
    
});

Route::post('spotifytodeezer', [Deezer::class,'convertFromSpotify']);

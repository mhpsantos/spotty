<?php

use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\SpotifyController as Spotify;
use App\Http\Controllers\DeezerController as Deezer;
use App\Http\Controllers\YoutubeController as Youtube;
use Illuminate\Support\Facades\Cache;

Route::view('/', 'home');

Route::view('authenticated', 'authenticated');

Route::get('test', function () {
    return redirect('/')->with('status', "Convertido com sucesso");;
});

Route::get('fromspotify', function () {
    if (Spotify::isLoggedIn()){
        return view('fromspotify');
    }else{
        return redirect('spotify/auth');;
    }
});

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


Route::get('spotify/auth', function () {
    $client_id = config('spotify.client_id');
    $redirect_uri = config('spotify.redirect_uri');
    $scopes = 'user-read-private playlist-read-private playlist-modify-private';

    return redirect("https://accounts.spotify.com/authorize?client_id=$client_id&redirect_uri=$redirect_uri&scope=$scopes&response_type=code");
    
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

    $response = $client->request('POST', "https://accounts.google.com/o/oauth2/token",[
        'form_params' => $body,
        'allow_redirects' => true,
    ]);

    Cache::add('youtube_access_token', json_decode($response->getBody()->getContents())->access_token, 3600);

    return redirect('/');
});

Route::post('spotifytodeezer', [Deezer::class,'convertFromSpotify']);

Route::post('spotifytoyoutube', [Youtube::class,'convertFromSpotify']);

<?php

namespace App\Http\Controllers\Spotify;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Spotify\Auth\TokenController;
use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    private $tokenController;

    public function __construct(TokenController $tokenController)
    {
        $this->tokenController = $tokenController;
    }
    public function authenticate(Request $request)
    {
        if ($this->tokenController->setAccessToken($request)) {
            return redirect('/');
        }
        return redirect('/')->with('error', 'Error while logging in to Spotify.');
    }

}

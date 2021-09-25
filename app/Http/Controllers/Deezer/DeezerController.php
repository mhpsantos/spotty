<?php

namespace App\Http\Controllers\Deezer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Deezer\Auth\TokenController;
use Illuminate\Http\Request;

class DeezerController extends Controller
{
    private $tokenController;
    private $convertController;
    public function __construct(TokenController $tokenController)
    {
        $this->tokenController = $tokenController;
    }

    public function authenticate(Request $request)
    {
        if ($this->tokenController->setAccessToken($request)) {
            return redirect('/');
        }
        return redirect('/')->with('error', 'Error while logging in to Deezer.');
    }

}

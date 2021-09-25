@php
    use App\Http\Repository\Spotify\Library\LibraryRepository as SpotifyLibrary;
@endphp

@extends('layouts.header')
<style>
        label {
            cursor: pointer;
            filter: grayscale(100%);
        }

        label:hover {
            filter: grayscale(0);
        }
        input[type="radio"]:checked + label {
            filter: grayscale(0);
        }
</style>
@section('content')
<div class=" text-center">
    <div class="d-flex justify-content-center">
        <div class="spinner-grow text-success" style="display: none" id="spinner" role="status">
        </div>
    </div>
    <h4 class="my-4">Choose a playlist</h4>
    <form action="#" method="post">
        @csrf
        <div class="text-center">
            @foreach (SpotifyLibrary::getPlaylists() as $item)
                <span class="">
                    <input class="visually-hidden" value="{{$item['id']}}" type="radio" name="playlistId" id="{{$item['name']}}">
                    <label class="m-1 p-3 bg-success rounded-3" for="{{$item['name']}}">
                        <img src="{{$item['images'][0]->url}}" width="150px" height="150px">
                        <div class="text-truncate m-1" style="width: 150px">{{$item['name']}}</div>
                    </label>
                </span>
            @endforeach 
            <div class="my-5 container text-center">
                <input class="form-control text-center mx-auto my-3" placeholder="New Playlist Name" style="width: 300px" type="text" name="newPlaylistName" id="newPlaylistName">
                <button class="btn btn-primary mx-2" formaction="{{url('spotifytoyoutube')}}" onclick="showSpinner()">To Deezer</button>
                <button class="btn btn-danger mx-2" formaction="{{url('spotifytoyoutube')}}" onclick="showSpinner()">To YouTube</button>
            </div>
        </div>
    </form>
</div>
@endsection
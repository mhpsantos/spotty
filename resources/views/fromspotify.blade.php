@php
    use App\Http\Controllers\SpotifyController as Spotify;
    use App\Http\Controllers\DeezerController as Deezer;
@endphp

@extends('layouts.header')
<script>
</script>
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
<div class="container text-center">
    <h4 class="my-4">Choose a playlist</h4>
    <form action="#" method="post">
        @csrf
        <div class="container text-center">
            @foreach (Spotify::getPlaylists() as $item)
                <span class="">
                    <input class="visually-hidden" value="{{$item['id']}}" type="radio" name="playlistId" id="{{$item['name']}}">
                    <label class="m-1 p-3 bg-success rounded-3" for="{{$item['name']}}">
                        <img src="{{$item['images'][0]->url}}" width="150px" height="150px">
                        <div class="text-truncate m-1" style="width: 150px">{{$item['name']}}</div>
                    </label>
                </span>
                {{-- <input type="image" id="playlistId" onclick="getValue()" value="{{$item['id']}}" src="{{$item['images'][0]->url}}" width="150px">   --}}
            @endforeach 
            <div class="my-5 container text-center">
                <input class="form-control text-center mx-auto my-3" placeholder="New Playlist Name" style="width: 300px" type="text" name="newPlaylistName" id="newPlaylistName">
                <button class="btn btn-primary mx-2" formaction="{{url('spotifytodeezer')}}">To Deezer</button>
                <button class="btn btn-danger mx-2" formaction="{{url('spotifytoyoutube')}}">To YouTube</button>
            </div>
        </div>
    </form>
</div>
@endsection
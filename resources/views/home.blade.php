@extends('layouts.header')
<script>
    function deezerAuth(){
        var win = window.open("deezer/auth", "_blank", "width=500px");
        setTimeout(() => {
            document.location.reload(true);
            win.close();
        }, 10000); 
    }
    function spotifyAuth(){
        var win = window.open("/auth", "_blank", "width=500px");
        setTimeout(() => {
            document.location.reload(true);
            win.close();
        }, 10000); 
    }
    function deezerAuth(){
        var win = window.open("deezer/auth", "_blank", "width=500px");
        setTimeout(() => {
            document.location.reload(true);
            win.close();
        }, 10000); 
    }
</script>
@section('content')
    <div class="container">
        @if(session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif 
    </div>
    <div class="container text-center text-light"  style="margin-top: 10%">
        <h4 class="my-3">Convert playlist</h4>
        <div class="row-md">
            <div class="col">
                <h4 class="text-muted py-3">From</h4>
                <div class="container my-3">
                    <a href="fromdeezer" class="btn btn-primary mx-3" id="deezerBtn">Deezer</a>
                    
                    <div class="w-100 d-sm-none my-4"></div>
        
                    <a href="fromspotify" class="btn btn-success mx-3" id="spotifyBtn">Spotify</a>
                    
                    <div class="w-100 d-sm-none my-4"></div>
        
                    <a href="fromyoutube" class="btn btn-danger mx-3" id="youtubeBtn">YouTube</a>
                </div>
            </div>
        </div>
    </div>
@endsection
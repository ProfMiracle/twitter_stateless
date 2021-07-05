<?php


namespace App\Http\Controllers;


use Abraham\TwitterOAuth\TwitterOAuth;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Cache;

class Home extends Controller
{
    public function index()
    {
        $tempId = bin2hex(random_bytes(40));

        $connection = new TwitterOAuth("Fj1skBBtAUuvuuYHJE0c3vDcK", "uHaXF3uF7e4tQ3FkVOfYf7uetPZp8xUERWguZ5WRqnSET7i1BB");
        $requestToken = $connection->oauth('oauth/request_token', array('oauth_callback' => 'https://twitter-stateless.herokuapp.com/callback?user='.$tempId));

        Cache::put($tempId, $requestToken['oauth_token_secret'], 1);
        $url = $connection->url('oauth/authorize', array('oauth_token' => $requestToken['oauth_token']));
        return $url;
    }
}

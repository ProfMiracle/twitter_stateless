<?php


namespace App\Http\Controllers;


use Abraham\TwitterOAuth\TwitterOAuth;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class Home extends Controller
{
    public function index()
    {
        $tempId = bin2hex(random_bytes(40));

        $connection = new TwitterOAuth("Fj1skBBtAUuvuuYHJE0c3vDcK", "uHaXF3uF7e4tQ3FkVOfYf7uetPZp8xUERWguZ5WRqnSET7i1BB");
        $requestToken = $connection->oauth('oauth/request_token', array('oauth_callback' => 'https://twitter-stateless.herokuapp.com/callback?user='.$tempId));

        Cache::put($tempId, $requestToken['oauth_token_secret'], 1);
        $url = $connection->url('oauth/authorize', array('oauth_token' => $requestToken['oauth_token']));
        print function_exists('curl_exec') ? 'curl_exec is enabled' : 'curl_exec is disabled';
        return $url;
    }

    public function callb(Request $request)
    {
        $connection = new TwitterOAuth("Fj1skBBtAUuvuuYHJE0c3vDcK", "uHaXF3uF7e4tQ3FkVOfYf7uetPZp8xUERWguZ5WRqnSET7i1BB", $request->oauth_token, Cache::get($request->user));
        $access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $request->oauth_verifier]);

        $connection = new TwitterOAuth(config('services.twitter.client_id'), config('services.twitter.client_secret'), $access_token['oauth_token'], $access_token['oauth_token_secret']);
        $content = $connection->get("account/verify_credentials");

        echo "<pre>";
        var_dump($content);
        echo "</pre>";
    }
}

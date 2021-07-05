<?php


namespace App\Http\Controllers;


use Abraham\TwitterOAuth\TwitterOAuth;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Home extends Controller
{
    const mIV = "BuyamBeerBot1234";
    const mKEY = "This is a Key";
    const Cypher = "AES-128-CTR";
    const Options = 0;
    /**
     * @var false|int
     */
    private $iv_length;

    public function __construct()
    {
        $this->iv_length = openssl_cipher_iv_length(self::Cypher);
    }

    public function index()
    {
        /**
         * encrypt auth_token_secret and send out as tempId
         *when it comes back, you decrypt and get authtokensecret back
         */

        $tempId = Str::random(40);

        $connection = new TwitterOAuth("Fj1skBBtAUuvuuYHJE0c3vDcK", "uHaXF3uF7e4tQ3FkVOfYf7uetPZp8xUERWguZ5WRqnSET7i1BB");
        $requestToken = $connection->oauth('oauth/request_token', array('oauth_callback' => 'https://twitter-stateless.herokuapp.com/callback?user='.$tempId));

        //$tempId = $this->encrypt($requestToken['oauth_token_secret']);
        Cache::put($tempId, $requestToken['oauth_token_secret'], 1);

        $url = $connection->url('oauth/authorize', array('oauth_token' => $requestToken['oauth_token']));

        return $url.'&user='.$tempId;
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

    private function encrypt($string)
    {
        return openssl_encrypt($string, self::Cypher,
            self::mKEY, self::Options, self::mIV);
    }

    private function decrypt($string)
    {
        return openssl_decrypt ($string, self::Cypher,
            self::mKEY, self::Options, self::mIV);
    }
}

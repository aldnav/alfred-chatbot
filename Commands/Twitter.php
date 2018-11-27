<?php

use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\Yaml\Yaml;
include_once 'BaseCommand.php';

$settings = Yaml::parse(file_get_contents(__DIR__ . '/../twitterauth.yaml'));
$consumer_key = $settings['consumer_key'];
$consumer_secret = $settings['consumer_secret'];
$access_token = $settings['access_token'];
$access_token_secret = $settings['access_token_secret'];


class TwitterCommand extends BaseCommand {
    protected $twitterSessions;

    function __construct($args) {
        if (isset($args['bot'])) {
            $this->$bot = $args['bot'];
            $this->twitterSessions = $this->$bot->collection->twitter;
        }
        global $consumer_key;
        global $consumer_secret;
        global $access_token;
        global $access_token_secret;
        $this->connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
        $this->connection->host = "https://api.twitter.com/1.1/";
        $this->connection->ssl_verifypeer = TRUE;
        $this->connection->content_type = 'application/x-www-form-urlencoded';
    }

    public function handle($input) {
        $output = $input;

        $output['message'] = 'FROM TWITTER COMMAND: hi';
        $this->$bot->sendMessage($output);
    }

    public function getLatestTweet($topic) {
        $response = $this->connection->get('search/tweets', ['q' => $topic]);
        var_dump('-*-*-*-*-');
        var_dump($response);
    }


    // public function handle($input) {
    //     $input['message'] = 'TWITTER: '.$input['message'];
    //     var_dump($input);
        
    //     $session = $this->getSessions($input);
    //     if (!$session) {
    //         $this->createSession($input);
    //     }
    // }
    
    // public function getSessions($input) {
    //     return $this->twitterSessions->findOne(['sender_id' => $input['senderid']]);
    // }

    // public function createSession($input) {
    //     return $this->twitterSessions->insertOne([
    //         'sender_id' => $input['senderid']
    //     ]);
    // }
}

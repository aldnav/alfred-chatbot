<?php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

include_once 'BaseCommand.php';

class TwitterCommand extends BaseCommand {
    protected $twitterSessions;
    protected $searchEndpoint = 'https://api.twitter.com/1.1/search/tweets.json';

    function __construct($args) {
        if (isset($args['bot'])) {
            $this->$bot = $args['bot'];
            $this->twitterSessions = $this->$bot->collection->twitter;
        }
        $this->client = new Client();
        $this->headers = array(
            'authorization' => 'OAuth',
            'oauth_consumer_key' => "l7bMfBG75mqr0YtrPoD59l6xJ", 
            'oauth_nonce' => "kYjzVBB8Y0ZFabxSWbWovY3uYSQ2pTgmZeNu2VS4cg",
            // 'oauth_signature' => "generated-signature", 
            'oauth_signature_method' => "HMAC-SHA1",
            'oauth_timestamp' => time(), 
            'oauth_token' => "1246167145-yy4o5YcHJm3ZzxPJMLDVOsRFojY7HNTDuAT37h8",
            'oauth_version' => "1.0"
        );
    }

    public function handle($input) {
        $output = $input;

        $output['message'] = 'FROM TWITTER COMMAND: hi';
        $this->$bot->sendMessage($output);

        $this->getLatestTweet($input['message']);
    }

    public function getLatestTweet($topic) {
        $apiEndpoint = $this->searchEndpoint . '?q=' . $topic;
        var_dump($apiEndpoint);

        $response = $this->client->post($apiEndpoint, [
            'headers' => $this->headers
        ]);
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

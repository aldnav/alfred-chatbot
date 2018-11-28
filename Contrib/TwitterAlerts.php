<?php
require_once __DIR__ . "/../vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\Yaml\Yaml;
include __DIR__ . '/../FbBot.php';

$settings = Yaml::parse(file_get_contents(__DIR__ . '/../twitterauth.yaml'));
$consumer_key = $settings['consumer_key'];
$consumer_secret = $settings['consumer_secret'];
$access_token = $settings['access_token'];
$access_token_secret = $settings['access_token_secret'];
$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);

function getLatestTweet($con, $topic) {
    echo "Getting latest tweet with " . $topic . '\n';
    $response = $con->get('search/tweets', ['q' => $topic, 'count' => 1]);
    if (count($response->statuses) == 0) {
        return;
    }
    $tweet = [
        'text' => $response->statuses[0]->text,
        'url' => 'https://twitter.com/statuses/'.$response->statuses[0]->id_str
    ];
    return $tweet;
}

$loop = React\EventLoop\Factory::create();

/**
 * Sub-optimal approach
 * One periodic timer
 * Checks every topic from twitter
 * And messages all users
 * A topic has a send status: IDLE, PROCESSING, SENDING 
 */

function stayAlive($connection, $loop) {

    $loop->addPeriodicTimer(10, function () use ($connection) {
        $collection = (new MongoDB\Client)->prod;
        $twitterTopics = $collection->twitterTopics;
        $cursor = $twitterTopics->find(['send_status' => 'IDLE']);
        foreach ($cursor as $document) {
            $updateResult = $twitterTopics->updateOne(
                [ '_id' => $document['_id'] ],
                [ '$set' => [ 'send_status' => 'PROCESSING' ] ]
            );
            $latestTweet = getLatestTweet($connection, $document['topic']);
            if ($latestTweet && (!isset($document['latest_tweet_url']) ||
                    $latestTweet['url'] != $document['latest_tweet_url'])) {
                // update latest_tweet_url and latest_tweet_text
                // then set status to sending
                var_dump('Updating topic tweet');
                $twitterTopics->updateOne(
                    [ '_id' => $document['_id'] ],
                    [ '$set' => [
                        'send_status' => 'FOR_SENDING',
                        'latest_tweet_text' => $latestTweet['text'],
                        'latest_tweet_url' => $latestTweet['url']
                      ]
                    ]
                );
            } else {
                // set back to idle
                $twitterTopics->updateOne(
                    [ '_id' => $document['_id'] ],
                    [ '$set' => [ 'send_status' => 'IDLE' ] ]
                );
            }
        }
    });

    $loop->addPeriodicTimer(5, function () use ($connection) {
        $collection = (new MongoDB\Client)->prod;
        $twitterTopics = $collection->twitterTopics;
        $cursor = $twitterTopics->find(['send_status' => 'FOR_SENDING']);
        $accessToken = file_get_contents(__DIR__ .'/../accesstoken.txt');
        $bot = new FbBot();
        $bot->setAccessToken($accessToken );
        foreach ($cursor as $document) {
            $twitterTopics->updateOne(
                [ '_id' => $document['_id'] ],
                [ '$set' => [ 'send_status' => 'SENDING' ] ]
            );
            
            foreach ($document['user_id'] as $user) {
                $message = [
                    'senderid' => $user
                ];
                
                $message['message'] = "A tweet matched " . $document['topic'];
                $bot->sendMessage($message);

                $message['message'] = $document['latest_tweet_text'] . ' ' . $document['latest_tweet_url'];
                $bot->sendMessage($message);
            }

            $twitterTopics->updateOne(
                [ '_id' => $document['_id'] ],
                [ '$set' => [ 'send_status' => 'IDLE' ] ]
            );
        }


    });
    
    $loop->run();

}

stayAlive($connection, $loop);
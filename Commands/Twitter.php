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
            $this->twitterTopics = $this->$bot->collection->twitterTopics;
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
        $msg_array = explode(' ', $input['message']);
        $cmd = strtoupper($msg_array[0]);
        array_shift($msg_array);
        $topics = $msg_array;

        if ($cmd == 'LISTEN') {
            if (count($topics) == 0) {
                $this->helpListen($output);
                return;
            }

            $already_sub = array_intersect(
                $this->getTopicsFromUser($input['senderid']), $topics);
            $already_sub = implode(' ', $already_sub);
            if (count($already_sub) > 0) {
                $output['message'] = 'You are already subscribed to ' . $already_sub;
                $this->$bot->sendMessage($output);
            }

            $subscribed = [];
            foreach ($topics as $topic) {
                $sub_topic = $this->subscribe($topic, $input['senderid']);
                if ($sub_topic) {
                    array_push($subscribed, $sub_topic);
                }
            }
            $subscribed_topics = implode(' ', $subscribed);
            if (strlen(trim($subscribed_topics)) > 0) {
                $output['message'] = 'You are now listening to ' . $subscribed_topics . ' tweets.';
                $this->$bot->sendMessage($output);
            } else {
                $this->helpListen($output);
            }  
        } else if ($cmd == 'CANCEL') {
            if (count($topics) == 0) {
                $this->helpCancel($output);
                return;
            }

            if ($topics[0] == 'ALL') {
                $this->unsubscribeAll($input['senderid']);
                $ouput['message'] = 'You stopped listening to tweets.';
                $this->$bot->sendMessage($output);
                return;
            }

            $unsubscribed = [];
            foreach ($topics as $topic) {
                $sub_topic = $this->unsubscribe($topic, $input['senderid']);
                if ($sub_topic) {
                    array_push($unsubscribed, $sub_topic);
                }
            }

            $unsubscribed_topics = implode(' ', $unsubscribed);
            if (strlen(trim($unsubscribed_topics)) > 0) {
                $ouput['message'] = 'You are no longer listening to ' . $unsubscribed_topics . ' tweets.';
                $this->$bot->sendMessage($output);
            } else {
                $this->helpCancel($output);
            }
        } else if ($cmd == 'TOPICS') {
            $this->getTopicsFromUser($input['senderid']);
        }
    }

    public function subscribe($topic, $user_id) {
        if ($topic[0] != '#') {
            return FALSE;
        }
        
        $topicEntry = $this->twitterTopics->findOne(['topic' => $topic]);
        if (!$topicEntry) {
            $this->twitterTopics->insertOne(['topic' => $topic]);
        }

        $updateResult = $this->twitterTopics->updateOne(
            ['topic' => $topic],
            ['$addToSet' => ['user_id' => $user_id]]
        );

        if ($updateResult->getModifiedCount() > 0) {
            return $topic;
        }
        return FALSE;

        // printf("Matched %d document(s)\n", $updateResult->getMatchedCount());
        // printf("Modified %d document(s)\n", $updateResult->getModifiedCount());
    }

    public function unsubscribe($topic, $user_id) {
        if ($topic[0] != '#') {
            return FALSE;
        }

        $this->twitterTopics->updateOne(
            ['topic' => $topic],
            ['$pull' => ['user_id' => $user_id]]
        );

        return $topic;
    }

    public function unsubscribeAll($user_id) {
        $this->twitterTopics->updateOne(
            ['user_id' => $user_id],
            ['$pull' => ['user_id' => $user_id]]
        );
    }

    public function helpListen($output) {
        $output['message'] = 'Start listening to tweets!';
        $this->$bot->sendMessage($output);
        $output['message'] = 'LISTEN #yesalfred #nasa';
        $this->$bot->sendMessage($output);
    }

    public function helpCancel($output) {
        $output['message'] = 'Stop listening to tweets by ...';
        $this->$bot->sendMessage($output);
        $output['message'] = 'CANCEL #yesalfred #nasa';
        $this->$bot->sendMessage($output);
    }

    public function getTopicsFromUser($user_id) {
        $cursor = $this->twitterTopics->find(['user_id' => $user_id]);
        $topics = [];
        foreach ($cursor as $document) {
            array_push($topics, $document['topic']);
        }
        // $topics = implode('\n', $topics);
        return $topics;
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

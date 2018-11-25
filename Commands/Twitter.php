<?php
include_once 'BaseCommand.php';

class TwitterCommand extends BaseCommand {
    protected $twitterSessions;

    function __construct($args) {
        if (isset($args['bot'])) {
            $this->$bot = $args['bot'];
            $this->twitterSessions = $this->$bot->collection->twitter;
        }
    }

    public function handle($input) {
        $input['message'] = 'TWITTER: '.$input['message'];
        var_dump($input);
        
        $session = $this->getSessions($input);
        if (!$session) {
            $this->createSession($input);
        }
    }
    
    public function getSessions($input) {
        return $this->twitterSessions->findOne(['sender_id' => $input['senderid']]);
    }

    public function createSession($input) {
        return $this->twitterSessions->insertOne([
            'sender_id' => $input['senderid']
        ]);
    }
}

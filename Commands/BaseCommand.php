<?php

class BaseCommand {
    private $bot = null;

    function __construct($args) {
        if (isset($args['bot'])) {
            $this->$bot = $args['bot'];
        }
    }

    public function handle($input) {
        var_dump($input);
    }

    public function setUserCommand($input, $commandName) {
        $this->$bot->collection->updateOne(
            ['sender_id' => $input['senderid']],
            ['$set' => ['command' => $commandName]]
        );
    }

}
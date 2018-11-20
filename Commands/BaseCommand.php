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

}
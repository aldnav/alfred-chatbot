<?php
include_once 'BaseCommand.php';

class TwitterCommand extends BaseCommand {

    public function handle($input) {
        $input['message'] = 'TWITTER: '.$input['message'];
        var_dump($input);
    }
}

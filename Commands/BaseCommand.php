<?php


class BaseCommand {
    private $bot = null;
    public $cmdList = "LISTEN #hashtag\nREMIND";
    public $cmdDocs = array(
        'LISTEN' => "Keep updated of the latest tweets with #hashtag\ne.g. LISTEN #yesalfred",
        'REMIND' => "Want me to remind of you of something?"
    );

    function __construct($args) {
        if (isset($args['bot'])) {
            $this->$bot = $args['bot'];
        }
    }

    public function handle($input) {
        /**
         * Every command's main function is "handle".
         * For replying to user, a command may use $bot->sendMessage
         * and pass an assoc. array with 'message' key in it.
        */
        // var_dump($input);
        // $this->$bot->sendMessage($input);
        $output = $input;
        $msgArray = explode(' ', $input['message']);
        if ($msgArray[0] && strtoupper($msgArray[0]) == "HELP") {
            if ($msgArray[1] && isset($this->cmdDocs[strtoupper($msgArray[1])])) {
                // Asking specific help for a command
                $output['message'] = $this->cmdDocs[strtoupper($msgArray[1])];
                $this->$bot->sendMessage($output);
            } else {
                $output['message'] = $this->cmdList;
                $this->$bot->sendMessage($output);
                $output['message'] = "You can also say HELP LISTEN to know more of my services.";
                $this->$bot->sendMessage($output);
            }
            
        } else {
            $output['message'] = "...";
            $this->$bot->sendMessage($output);

            $output['message'] = "Sorry. I can't help you with that.\nI'm still new.";
            $this->$bot->sendMessage($output);

            $output['message'] = "Is there anything I'd be able to assist you with?";
            $this->$bot->sendMessage($output);

            $output['message'] = "Say \"HELP\" to know my services.";
            $this->$bot->sendMessage($output);
        }
    }

    public function setUserCommand($input, $commandName) {
        $this->$bot->collection->updateOne(
            ['sender_id' => $input['senderid']],
            ['$set' => ['command' => $commandName]]
        );
    }

}
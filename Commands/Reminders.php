<?php
	
require_once __DIR__ . "/../vendor/autoload.php";

class RemindCommand {

	function __construct() {
			$this->collection = (new MongoDB\Client)->prod;
			$this->reminders = $this->collection->reminders;
	}	


	public function handle($input) {
		$cmd = $input['message'];
		$sender = $input['senderid'];
		if($cmd=="REMIND") {
			$updateResult = $this->collection->sessions->updateOne(['sender_id' => $sender],['$set' => ['command' => "REMIND"]]);
			return "What is the reminder about?";
		} else if ($cmd=="LIST") {
			$updateResult = $this->collection->sessions->updateOne(['sender_id' => $sender],['$set' => ['command' => "LIST"]]);
			$result = $this->getReminders($sender);
			$response ='';
			foreach ($result as $message){
				$response .= $message. "\n";
			}
			return $response; //send result as message
		} else {
			$session_cmd = $this->getSession($input)['command'];
			//gets previous command of session
			if($session_cmd == "REMIND") {
				$this->addReminder($input);
				$message = "When do you want to be reminded?";
				return $message;
			} else if ($session_cmd == "REMIND MESSAGE") {
				return "I will remind you on ". $input['message']. " about this.";
			}
		}

	}

    public function getReminders($user_id){
    	$cursor = $this->reminders->find(['user_id' => $user_id, 'status'=> 'ACTIVE']);
    	$items = [];
    	$i=1;
    	foreach ($cursor as $document) {
    		$date = $document['timestamp'];
    		$utcdatetime = new MongoDB\BSON\UTCDateTime($date);
			$timestamp = $utcdatetime->toDateTime()->format(DATE_COOKIE);
			$local = strtotime($timestamp); 
			$local = date("Y-m-d H:i", $local);
    		array_push($items, "[".$i."] ".$local." ".$document['reminder']);
    		$i++;
    	}

    	return $items;
    }


    public function addReminder($input) {
    	$reminder = $input['message'];
    	$userid = $input['senderid'];
    	$timestamp = $input['timestamp'];

		$this->reminders->insertOne(['user_id'=>$userid, 'reminder'=>$reminder, 'remind_time' => '', 'timestamp'=> $timestamp, 'status'=>'UNTIMED']); 
		$updateResult = $this->collection->sessions->updateOne(['sender_id' => $userid], ['$set' => ['command' => "REMIND MESSAGE"]]);
    }



    //added this for testing purposes only
	public function getSession($input) {
        if (!isset($input['senderid'])) {
        	var_dump("no inputs\n");
            return;
        }
        return $this->collection->sessions->findOne(['sender_id' => $input['senderid']]);
    }

    public function readMessage() {
        try {
        	$input = json_decode(file_get_contents('php://input'), true);
            $payloads    = null;
            $senderId    = $input['entry'][0]['messaging'][0]['sender']['id'];
            $messageText = $input['entry'][0]['messaging'][0]['message']['text'];
           
            $timestamp = $input['entry'][0]['messaging'][0]['timestamp'];

            if (!empty($postback)) {
                $payloads = $input['entry'][0]['messaging'][0]['postback']['payload'];
                return ['senderid' => $senderId, 'message' => $payloads];
            }
            if (!empty($loctitle)) {
                $payloads = $input['entry'][0]['messaging'][0]['postback']['payload'];
                return ['senderid' => $senderId, 'message' => $messageText, 'location' => $loctitle];
            }

            return ['senderid' => $senderId, 'message' => $messageText, 'timestamp' => $timestamp];
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
}


$rc = new RemindCommand();
//var_dump($rc->reminders);
 $message = $rc->readMessage();
 //$res = $rc->handle($message);
 // foreach($res as $doc) {
 // 	var_dump($doc);
 // }
// $rc->addReminder($message);
 var_dump($rc->handle($message));

?>
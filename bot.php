<?php

include 'FbBot.php';

error_reporting(E_ALL);
ini_set('display_errors', 'Off');
ini_set('error_log', 'errors.log');
ini_set('log_errors_max_len', 1024);
set_exception_handler(function($exception) {
   error_log($exception);
   // error_page("Something went wrong!");
});

$token = $_REQUEST['hub_verify_token'];
$hubVerifyToken = 'masterbruce';
$challenge = $_REQUEST['hub_challenge'];
$accessToken = 'EAACtJPoHTQcBAJZCwVcjLahg4FmXpeyFauJ5HbVni0Y7RW00EcHvFTRA11E0fZBvPibAyez7rj0kaE75vNGVDxQmutmBmX7qx2ZCORHtE9y4HvgsZBZCZAwoznCFQ5Nk00Ft8mcZC2QhVVecteuIFpSV7DD1VNsGpPkcb8WW4AiDAZDZD';

$bot = new FbBot();
$bot->setHubVerifyToken($hubVerifyToken);
$bot->setAccessToken($accessToken);
echo $bot->verifyToken($token, $challenge);

$input = json_decode(file_get_contents('php://input'), true);
$message = $bot->readMessage($input);
$textmessage = $bot->sendMessage($message);
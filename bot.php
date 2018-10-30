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

$hubVerifyToken = 'masterbruce';
if(isset($_REQUEST['hub_challenge'])) {
    // for verification
    $challenge = $_REQUEST['hub_challenge'];
    $token = $_REQUEST['hub_verify_token'];
}

$bot = new FbBot();
echo $bot->verifyToken($token, $challenge);

// handle bot responses
$accessToken = file_get_contents(__DIR__ .'/accesstoken.txt');
$bot->setHubVerifyToken($hubVerifyToken);
$bot->setAccessToken($accessToken);

$input = json_decode(file_get_contents('php://input'), true);
$message = $bot->readMessage($input);
$textmessage = $bot->sendMessage($message);
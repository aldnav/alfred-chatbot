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
$accessToken = getenv('FB_ACCESS_TOKEN');

$bot = new FbBot();
$bot->setHubVerifyToken($hubVerifyToken);
$bot->setAccessToken($accessToken);
echo $bot->verifyToken($token, $challenge);

$input = json_decode(file_get_contents('php://input'), true);
$message = $bot->readMessage($input);
$textmessage = $bot->sendMessage($message);
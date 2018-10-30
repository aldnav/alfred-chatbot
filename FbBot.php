<?php

require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class FbBot {
    private $hubVerifyToken = null;
    private $accessToken = null;
    private $token = false;
    protected $client = null;

    function __construct() {}

    public function setHubVerifyToken($value) {
        $this->hubVerifyToken = $value;
    }

    public function setAccessToken($value) {
        $this->accessToken = $value;
    }

    public function verifyToken($hubVerifyToken, $challenge) {
        // if (!$hubVerifyToken) {
        //     throw new Exception('"$hubVerifyToken" is required');
        // }
        // if (!$challenge) {
        //     throw new Exception('"$challenge" is required');
        // }

        try {
            if ($hubVerifyToken === $this->hubVerifyToken) {
                return $challenge;
            } else {
                throw new Exception("Token not verified");
            }
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function readMessage($input) {
        try {
            $payloads    = null;
            $senderId    = $input['entry'][0]['messaging'][0]['sender']['id'];
            $messageText = $input['entry'][0]['messaging'][0]['message']['text'];
            $postback    = $input['entry'][0]['messaging'][0]['postback'];
            $loctitle    = $input['entry'][0]['messaging'][0]['message']['attachments'][0]['title'];
            if (!empty($postback)) {
                $payloads = $input['entry'][0]['messaging'][0]['postback']['payload'];
                return ['senderid' => $senderId, 'message' => $payloads];
            }
            if (!empty($loctitle)) {
                $payloads = $input['entry'][0]['messaging'][0]['postback']['payload'];
                return ['senderid' => $senderId, 'message' => $messageText, 'location' => $loctitle];
            }
            // var_dump($senderId, $messageText, $payload);
            // $payload_txt = $input['entry'][0]['messaging'][0]['message']['quick_reply']‌​['payload'];
            return ['senderid' => $senderId, 'message' => $messageText];
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function sendMessage($input) {
        try {
            $client      = new Client();
            $url         = "https://graph.facebook.com/v2.6/me/messages";
            $messageText = strtolower($input['message']);
            $senderId    = $input['senderid'];
            $msgarray    = explode(' ', $messageText);
            $response    = null;
            $answer      = '';
            $header      = array(
                'content-type' => 'application/json',
            );

            $answer = [
                'text' => $messageText
            ];

            $response = [
                'recipient' => ['id' => $senderId],
                'message' => $answer,
                'access_token' => $this->accessToken
            ];
            $response = $client->post($url, ['query' => $response, 'headers' => $header]);
            return true;
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody(true)->getContents());
            return $response;
        }
    }
}
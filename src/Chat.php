<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {

    private $clients;
    private $db;

    public function __construct() {
        $this->clients = array();
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients[] = $conn;
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $msg = json_decode($msg);
        
        print_r($clients);

        $postFields = [
            "time" => $msg->time,
            "user_id" => $msg->user_id,
            "person_id" => $msg->person_id,
            "product_id" => $msg->product_id
        ];

        if (!empty($msg->message)) {
            $postFields["message"] = $msg->message;
        } elseif (!empty($msg->image)) {
            $postFields["image"] = $msg->image;
        }
        $response = $this->sendCurlRequest($postFields);

        foreach($this->clients as $key => $client) {
            if($client === $from) {
                if(empty($msg->message) and empty($msg->image)) {
                    $this->clients[$key] = $from;
                    $client->send('{"status":"offline"}');
                    foreach($this->clients as $client) {
                        if($msg->person_id == $client->user_id) {
                            $client->send('{"status":"online"}');
                            $from->send('{"status":"online"}');
                        }
                    }
                }
            } else {
                if (!empty($msg->message) || !empty($msg->image)) {
                    if($client->user_id == $msg->person_id && $msg->user_id == $client->person_id && $client->product_id == $msg->product_id) {
                        $client->send($response);
                    }
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        foreach($this->clients as $key => $client) {
            if($client === $conn) {
                $off['user_id'] = $client->user_id;
                $off['person_id'] = $client->person_id;
                $off['product_id'] = $client->product_id;
            }
        }
        foreach($this->clients as $client) {
            if($off['person_id'] == $client->user_id) {
                $client->send('{"status":"offline"}');
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo $e->getMessage();
    }

    private function sendCurlRequest($postFields) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://upuse.digitalsupporter.in/api/message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
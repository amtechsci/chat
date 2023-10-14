<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {

    private $clients;
    private $db;

    public function __construnct() {
        $this->clients = array();
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients[] = $conn;
    }

    public function onMessage(ConnectionInterface $from, $msg) {
    $msg = json_decode($msg);
if(empty($msg->user_id)){
    	print_r($msg);
    }
    if (!empty($msg->message)) {
            $curl = curl_init();
curl_setopt_array($curl, array(CURLOPT_URL => 'https://upuse.digitalsupporter.in/api/message',CURLOPT_RETURNTRANSFER => true,CURLOPT_ENCODING => '',CURLOPT_MAXREDIRS => 10,CURLOPT_TIMEOUT => 0,CURLOPT_FOLLOWLOCATION => true,CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,CURLOPT_CUSTOMREQUEST => 'POST',CURLOPT_POSTFIELDS => array("time"=>$msg->time,"user_id"=>$msg->user_id,"person_id"=>$msg->person_id,"product_id"=>"$msg->product_id","message"=>$msg->message,"time"=>$msg->time),
));$response = curl_exec($curl);curl_close($curl);
}elseif (!empty($msg->image)) {
$curl = curl_init();
curl_setopt_array($curl, array(CURLOPT_URL => 'https://upuse.digitalsupporter.in/api/message',CURLOPT_RETURNTRANSFER => true,CURLOPT_ENCODING => '',CURLOPT_MAXREDIRS => 10,CURLOPT_TIMEOUT => 0,CURLOPT_FOLLOWLOCATION => true,CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,CURLOPT_CUSTOMREQUEST => 'POST',CURLOPT_POSTFIELDS => array("time"=>$msg->time,"user_id"=>$msg->user_id,"person_id"=>$msg->person_id,"product_id"=>"$msg->product_id","image"=>$msg->image,"time"=>$msg->time),
));$response = curl_exec($curl);curl_close($curl);
}
    foreach($this->clients as $key => $client) {
    if($client == $from){
    if(empty($msg->message) and empty($msg->image)){
    $from->user_id = $msg->user_id;
    $from->person_id = $msg->person_id;
    $from->product_id = $msg->product_id;
    $this->clients[$key] = $from;
    $curl = curl_init();
	curl_setopt_array($curl, array(CURLOPT_URL => 'https://upuse.digitalsupporter.in/api/message',CURLOPT_RETURNTRANSFER => true,CURLOPT_ENCODING => '',CURLOPT_MAXREDIRS => 10,CURLOPT_TIMEOUT => 0,CURLOPT_FOLLOWLOCATION => true,CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,CURLOPT_CUSTOMREQUEST => 'POST',CURLOPT_POSTFIELDS => array("user_id"=>$msg->user_id,"person_id"=>$msg->person_id,"product_id"=>"$msg->product_id"),
	));curl_exec($curl);curl_close($curl);
	$client->send('{"status":"offline"}');
    		foreach($this->clients as $key => $client) {
    		if(($msg->person_id == $client->user_id)){
        	$client->send('{"status":"online"}');
        	$from->send('{"status":"online"}');
    		}}
    }
    }else{
        if (!empty($msg->message)) {
        if(($client->user_id == $msg->person_id) and ($msg->user_id == $client->person_id) and ($client->product_id == $msg->product_id)){$client->send($response);}}
        if (!empty($msg->image)) {
            if(($client->user_id == $msg->person_id) and ($msg->user_id == $client->person_id) and ($client->product_id == $msg->product_id)){$client->send($response);}}
    }}}

    public function onClose(ConnectionInterface $conn) {
    	foreach($this->clients as $key => $client) {
    		if($client == $conn){
        	$off['user_id'] = $client->user_id;
        	$off['person_id'] = $client->person_id;
        	$off['product_id'] = $client->product_id;
    		}}
    		foreach($this->clients as $key => $client) {
    		if(($off['person_id'] = $client->user_id)){
        	$client->send('{"status":"offline"}');
    		}}
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo $e->getMessage();
    }
}
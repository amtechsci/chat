<?php
$host = '156.67.222.254';
$port = '8080';
set_time_limit(0);
$soket = socket_create(AF_INET,SOCK_STREAM,0);
$result = socket_bind($soket,$host,$port);
socket_listen($soket,3);
class chat{
    
    function readline(){
        return rtrim(fgets(STDIN));
}}
do{
    $accept = socket_accept($soket);
    $msg = socket_read($accept,1024);
    $msg = trim($msg);
    echo "hh ".$msg;
    $line = new Chat();
    echo "enter reply";
    $reply = $line->readline();
    socket_write($accept,$reply,strlen($reply));
}while(true);
socket_close($accept);
socket_close($soket);
?>
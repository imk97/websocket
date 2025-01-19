<?php

require_once "functions.php";

error_reporting(E_ALL);

/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush();

$clients = array();
// $tmpClients = array();

$address = '192.168.0.23';
$port = 10000;

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n");
array_push($clients, $sock);
// array_push($clients, $sock);
// socket_set_nonblock($sock);

// socket_bind($sock, $address, $port) or die("socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n");

if (!socket_bind($sock, $address, $port)) {
    socketClose($sock);
    die("socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n");
}

socket_listen($sock) or die("socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n");

// $msg = socket_read($client, 1024);
// echo "From client : " . $msg . "\n";
// $out = "Hi, I'm From Server";
// socket_write($client, $out, strlen($out));

do {

    $reads = $clients;
    // var_dump($reads);

    if (in_array($sock, $reads)) {
        $client = socket_accept($sock) or die("socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n");
        $msg = socket_read($client, 1024);
        // echo "Message from client: " . $msg;
        handshake($msg, $client); // Handshake for user use browser
        // array_push($tmpClients, $client);

        array_push($clients, $client);
        $response = pack_data("Hi, I'm From Server hihi\n\n");
        socket_write($client, $response, strlen($response));

        // $firstIndex = array_search($sock, $reads);
        // unset($reads[$firstIndex]);
    }


    // echo "hello";
    // $count = 0;
    // var_dump($reads);
    // foreach ($reads as $key => $value) {
    //     $count++;
    //     echo "hello " . $count;
    //     $data = socket_read($value, 1024);
    //     echo "Message from client: " . $data;
    // }
    // foreach($reads as $key => $value) {
    //     echo "hello";
    //     $data = socket_read($value, 1024);
    //     echo "Message from client: " . $data;

    //     // if (!empty($data)) {
    //     //     # code...
    //     // }
    // }

    // $msg = socket_read($client, 1024);

    // $reply = "Hi, I'm From Server";
    // socket_write($client, $reply, strlen($reply));

    
    // if (isset($msg)) {
    //     # code...
    //     // $out = "Hi, I'm From Server";
    //     socket_write($client, $msg, strlen($msg));
    // } else {
    //     echo "Connection is stop";
    // }

    // $out = "Hi, I'm From Server \n";
    // // $out = readline("Enter from server: ");
    // socket_write($client, $out, strlen($out));
} while (true);

function socketClose($sock) {
    // socket_close($sock);
    socket_shutdown($sock, 2);
}



?>
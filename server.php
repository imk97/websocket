<?php

error_reporting(E_ALL);

/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush();

$clients = array();

$address = '127.0.0.1';
$port = 10000;

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n");
// socket_set_nonblock($sock);

// socket_bind($sock, $address, $port) or die("socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n");

if (!socket_bind($sock, $address, $port)) {
    socketClose($sock);
    die("socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n");
}

socket_listen($sock, 5) or die("socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n");

$count = 0;

do {
    $client = socket_accept($sock) or die("socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n");

    // var_dump($client);
    array_push($clients, $client);

    if ($client) {
        echo "hello " . $count . "\n";
        $msg = socket_read($client, 1024);
        // $msg = "testasdfsdfsdfsdf";
        // $receive = socket_recv($client, $msg, 2048, MSG_DONTWAIT);
        // if (!$receive) {
        //     socketClose($sock);
        //     die("socket_recv() failed; reason: " . socket_strerror(socket_last_error($sock)) . "\n");
        // }

        echo "From client : " . $msg . "\n";
    }

    $out = "Hi, I'm From Server";
    socket_write($client, $out, strlen($out));
    $count++;
} while (true);

function socketClose($sock) {
    socket_close($sock);
}

?>
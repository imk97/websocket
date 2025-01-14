<?php
error_reporting(E_ALL);

/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush();

$clients = array();

$address = '192.168.0.23';
$port = 10000;

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n");
// socket_set_nonblock($sock);

// socket_bind($sock, $address, $port) or die("socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n");

if (!socket_bind($sock, $address, $port)) {
    socketClose($sock);
    die("socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n");
}

socket_listen($sock, 5) or die("socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n");

$client = socket_accept($sock) or die("socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n");
// var_dump($client);
array_push($clients, $client);

do {

    // echo "hello " . $count . "\n";
    $msg = socket_read($client, 1024);
    // $msg = "testasdfsdfsdfsdf";
    // $receive = socket_recv($client, $msg, 2048, MSG_DONTWAIT);
    // if (!$receive) {
    //     socketClose($sock);
    //     die("socket_recv() failed; reason: " . socket_strerror(socket_last_error($sock)) . "\n");
    // }

    echo "From client : " . $msg . "\n";

    if ($msg == "shutdown") {
        echo "Shutdown\n\n";
        socket_close($client);
        break;
    }

    // $out = "Hi, I'm From Server hihihi";
    // $out = $_GET["fname"];
    $out = readline("Enter from server: ");
    socket_write($client, $out, strlen($out));
} while (true);

function socketClose($sock) {
    // socket_close($sock);
    socket_shutdown($sock, 2);
}

?>
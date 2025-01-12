<?php
// $address = '192.168.0.23';
// $port = 10000;
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_close($sock);
?>
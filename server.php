<?php
$address = '192.168.2.68';
$port = 10000;
// $null = NULL;

include 'functions.php';

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($sock, $address, $port);
socket_listen($sock);

$members = [];
$connections = [];
$connections[] = $sock;

echo "Listening for new connections on port $port: " . "\n";

while(true) {

    $reads = $writes = $exceptions = $connections;

    socket_select(read: $reads, write: $writes, except: $exceptions, seconds: 0);

    /**
     * When a fresh connection comes, $reads will contain $sock, 
     * which is the Socket instance created with socket_create()
     * 
     * For other incoming messages from already connected users,
     * $reads won't contain $sock, instead the Socket instance created earlier
     * with socket_accept() will be there
     */

    if(in_array($sock, $reads)) {
        $new_connection = socket_accept($sock);
        $header = socket_read($new_connection, 1024);
        handshake($header, $new_connection, $address, $port);
        $connections[] = $new_connection;
        $reply = [
            "type" => "join",
            "sender" => "Server",
            "text" => "enter name to join... \n"
        ];
        $reply = pack_data(text: json_encode(value: $reply));
        socket_write(socket: $new_connection, data: $reply, length: strlen(string: $reply));

        /**
         * The fresh connection is accepted, with socket_accept
         * Now remove the $sock from $reads so that it won't go to foreach below
         */
        
        $firstIndex = array_search($sock, $reads);
        unset($reads[$firstIndex]);
    }

    foreach ($reads as $key => $value) {

        $data = socket_read($value, 1024);

        if(!empty($data)) {
            $message = unmask($data);
            echo "Client: " .$message;
            $decoded_message = json_decode($message, true);
            if ($decoded_message) {
                if(isset($decoded_message['text'])){
                    if($decoded_message['type'] === 'join') {
                        $members[$key] = [
                            'name' => $decoded_message['sender'],
                            'connection' => $value
                        ];
                    }
                    $maskedMessage = pack_data($message);
                    foreach ($members as $mkey => $mvalue) {
                        socket_write($mvalue['connection'], $maskedMessage, strlen(string: $maskedMessage));
                    }
                }
            }
        }

        else if($data === '')  {
            echo "disconnected " . $key . " \n";
            unset($connections[$key]);
            if(array_key_exists($key, $members)) {
                
                $message = [
                    "type" => "left",
                    "sender" => "Server",
                    "text" => $members[$key]['name'] . " left the chat \n"
                ];
                $maskedMessage = pack_data(json_encode(value: $message));
                unset($members[$key]);
                foreach ($members as $mkey => $mvalue) {
                    socket_write($mvalue['connection'], $maskedMessage, strlen(string: $maskedMessage));
                }
            }
            socket_close($value);
        }
    }

}

socket_close(socket: $sock);

?>
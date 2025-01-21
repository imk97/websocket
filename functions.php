<?php

function unmask($msg) {
    // $byte1 = ord($msg)[0]; // 1byte
    $length = ord($msg[1]) & 127; // 2byte

    if ($length == 126) {
        $masks = substr($msg, 4, 4);
        $data = substr($msg, 8);
    } else if ($length == 127) {
        $masks = substr($msg, 10, 4);
        $data = substr($msg, 14);
    } else {
        $masks = substr($msg, 2, 4);
        $data = substr($msg, 6);
    }

    $text = "";
    for ($i=0; $i < strlen($data); $i++) { 
        //bitwise operators
        //decrypt algorithm, refer rfc4566 documentation
        $text .= $data[$i] ^ $masks[$i % 4];
    }

    return $text;
}

function handshake($req_headers, $socket) {
    // echo $req_headers; exit();
    $headers = array();
    $lines = preg_split("/\r\n/", $req_headers);

    //Get Sec-WebSocket-Key header from Client
    foreach($lines as $line) {
        $line = chop($line);
        if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
            $headers[$matches[1]] = $matches[2];
        }
    }
    // var_dump($headers); exit();
    // echo $sec_key; exit();

    $sec_key = $headers['Sec-WebSocket-Key'];
    $sec_accept = base64_encode(pack('H*', sha1($sec_key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
    $response_headers = "HTTP/1.1 101 Switching Protocols\r\n" . 
        "Upgrade : websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "Sec-WebSocket-Accept:$sec_accept\r\n\r\n";

    socket_write($socket, $response_headers, strlen($response_headers));
}

//Fragmentation (Opcode Network)
//Convert string/data into binary for communication
// %x0 denotes a continuation frame
// %x1 denotes a text frame
// %x2 denotes a binary frame
// %x3-7 are reserved for further non-control frames
// %x8 denotes a connection close
// %x9 denotes a ping
// %xA denotes a pong
// %xB-F are reserved for further control frames
function pack_data($text) {
    $b1 = 0x80 | (0x1 & 0x0f);
    $length = strlen($text);

    if($length <= 125) {
		$header = pack('CC', $b1, $length);
	}
        
    elseif($length > 125 && $length < 65536) {
		$header = pack('CCn', $b1, 126, $length);
	}
        
    elseif($length >= 65536) {
		$header = pack('CCNN', $b1, 127, $length);
	}
    // echo $header; exit();
    return $header.$text;
}
?>
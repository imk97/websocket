<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-time application</title>
</head>
<body>
    <form action="client-socket.php" method="post">
        <input type="text" name="test" id="">
        <button type="submit">Hantar</button>
    </form>
    <!-- <script src="./socket-conn.js"></script> -->
</body>
<script>
    // console.log(window.location.hostname)
    // Refer Web Browser API
    // Create WebSocket connection.
    const socket = new WebSocket("ws://"+window.location.hostname+":10000");
    console.log(socket)
    // Connection opened
    socket.addEventListener("open", (event) => {
        console.log("Connection to server has been maded")
        // socket.send("Hello Server!");
    });

    socket.addEventListener("error", (event) => {
        console.log(event)
        // socket.send("Hello Server!");
    });
    
    // Listen for messages
    socket.addEventListener("message", (event) => {
        debugger
        console.log("Message from server ", event.data);
    });

</script>
</html>

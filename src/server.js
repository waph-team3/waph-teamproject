const WebSocket = require('ws');

const wss = new WebSocket.Server({ port: 6969 });

wss.on('connection', function connection(ws) {
    console.log('A new client connected!');

    ws.on('message', function incoming(data) {
        console.log('Received: %s', data);

        // Broadcast the received message to all clients as a string
        const message = data.toString(); // Convert data to string
        wss.clients.forEach(function each(client) {
            if (client !== ws && client.readyState === WebSocket.OPEN) {
                client.send(message); // Send the message to other clients
            }
        });
    });

    ws.send('Welcome to the WebSocket server!'); // Send a welcome message to the connected client
});

console.log('WebSocket server is running on ws://localhost:6969');

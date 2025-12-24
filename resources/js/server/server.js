import WebSocket, { WebSocketServer } from 'ws';

const PORT = 5050; // Vue connects here
const JAVA_WS = 'ws://localhost:5060';

let javaSocket = new WebSocket(JAVA_WS);

javaSocket.on('open', () => {
    console.log('Connected to Java WS');
});

javaSocket.on('close', () => {
    console.log('Java WS disconnected. Reconnecting in 2s...');
    setTimeout(() => {
        javaSocket = new WebSocket(JAVA_WS);
    }, 2000);
});

javaSocket.on('message', (msg) => {
    // Forward Java messages to all Vue clients
    wss.clients.forEach(client => {
        if (client.readyState === WebSocket.OPEN) client.send(msg.toString());
    });
});

const wss = new WebSocketServer({ port: PORT });

wss.on('connection', (ws) => {
    console.log('Vue frontend connected');

    ws.on('message', (msg) => {
        // Forward messages from Vue to Java
        if (javaSocket.readyState === WebSocket.OPEN) {
            javaSocket.send(msg.toString());
        } else {
            console.log('Java WS not ready, cannot forward reset');
        }
    });
});

console.log(`Node WS proxy running at ws://localhost:${PORT}`);

import WebSocket, { WebSocketServer } from 'ws';

const VUE_PORT = 5050; // Vue connects here
const JAVA_WS_URL = 'ws://localhost:5060'; // Java service runs here

let javaSocket = null;
let reconnectInterval = 2000;

// Function to establish and maintain connection to the Java service
function connectToJava() {
    console.log(`Attempting to connect to Java WS at ${JAVA_WS_URL}...`);
    javaSocket = new WebSocket(JAVA_WS_URL);

    javaSocket.on('open', () => {
        console.log('✅ Connected to Java WS.');
    });

    javaSocket.on('close', () => {
        console.log('❌ Java WS disconnected. Reconnecting...');
        // Only attempt to reconnect if it's not currently open
        if (javaSocket.readyState !== WebSocket.OPEN) {
            setTimeout(connectToJava, reconnectInterval);
        }
    });
    
    javaSocket.on('error', (err) => {
        // Prevent crashing if connection fails (e.g., Java app is off)
        console.error('Java WS Error:', err.message);
    });

    javaSocket.on('message', (msg) => {
        // Forward Java messages to all connected Vue clients
        wss.clients.forEach(client => {
            if (client.readyState === WebSocket.OPEN) {
                client.send(msg.toString());
            }
        });
    });

    
}

// Start connecting to Java immediately
connectToJava();

// --- WebSocket Server for Vue Frontend ---
const wss = new WebSocketServer({ port: VUE_PORT });

wss.on('connection', (ws) => {
    console.log('➕ Vue frontend connected');

    ws.on('message', (msg) => {
        // Forward messages from Vue to Java
        if (javaSocket && javaSocket.readyState === WebSocket.OPEN) {
            javaSocket.send(msg.toString());
        } else {
            console.log('⚠️ Java WS not ready, cannot forward message.');
            ws.send(JSON.stringify({ log: 'Error: Scanner service is currently offline.' }));
        }
    });

    ws.on('close', () => {
        console.log('➖ Vue frontend disconnected');
    });
});

console.log(`Node WS proxy running at ws://localhost:${VUE_PORT}`);




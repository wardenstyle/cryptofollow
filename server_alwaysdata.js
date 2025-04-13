const WebSocket = require("ws");
const amqp = require("amqplib");
const http = require('http');

const PORT = process.env.PORT || 8100;

// Gérer le serveur HTTP (obligatoire pour AlwaysData)
const server = http.createServer((req, res) => {
  res.writeHead(200, { 'Content-Type': 'text/plain' });
  res.end('Serveur HTTP prêt');
});

// Serveur WebSocket lié au serveur HTTP
const wss = new WebSocket.Server({ noServer: true });

console.log('Serveur WebSocket démarré');

// Connexions WebSocket
let clients = [];

wss.on("connection", (ws) => {
  clients.push(ws);
  console.log("Nouveau client connecté.");

  ws.on("close", () => {
    clients = clients.filter((client) => client !== ws);
    console.log("Client déconnecté.");
  });
});

// Support de WebSocket sur requête HTTP (AlwaysData upgrade)
server.on('upgrade', (request, socket, head) => {
  wss.handleUpgrade(request, socket, head, (ws) => {
    wss.emit('connection', ws, request);
  });
});

// Lancement du serveur HTTP (sur AlwaysData)
server.listen(PORT, '::', () => {
  console.log(`Serveur HTTP & WebSocket en écoute sur le port ${PORT}`);
});

// Connexion à RabbitMQ
async function startRabbitMQ() {
  try {
    const connection = await amqp.connect("amqp://cryptofollow:Crypt0-f0ll0w@rabbitmq-cryptofollow.alwaysdata.net/cryptofollow_mq");
    const channel = await connection.createChannel();

    const queue = "crypto_prices";
    await channel.assertQueue(queue, { durable: true });

    console.log("En attente de messages RabbitMQ...");
    console.log("Connexion à la queue :", queue);
    channel.consume(queue, (msg) => {
      if (msg !== null) {
        const data = msg.content.toString();
        console.log("Message reçu :", data);
    
        // Envoie à tous les clients WebSocket
        clients.forEach((client) => {
          if (client.readyState === WebSocket.OPEN) {
            client.send(data);
          }
        });
      }
    }, {
      noAck: true // important : auto-acknowledge, sinon le message ne part pas
    });
  } catch (err) {
    console.error("Erreur RabbitMQ :", err);
  }
}

startRabbitMQ();
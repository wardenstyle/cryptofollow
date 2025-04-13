const WebSocket = require("ws");
const amqp = require("amqplib");

const PORT = 8080;
const wss = new WebSocket.Server({ port: PORT });

console.log(`Serveur WebSocket en écoute sur le port ${PORT}`);

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

// Connexion RabbitMQ
async function startRabbitMQ() {
  try {
    //const connection = await amqp.connect("amqp://cryptofollow:Crypt0-f0ll0w@rabbitmq-cryptofollow.alwaysdata.net/cryptofollow_mq");
    const connection = await amqp.connect("amqp://guest:guest@localhost");
    const channel = await connection.createChannel();

    const queue = "crypto_prices";
    await channel.assertQueue(queue, { durable: true });

    console.log("En attente de messages RabbitMQ...");

    channel.consume(queue, (msg) => {
      if (msg !== null) {
        const data = msg.content.toString();
        console.log("Message reçu :", data);

        // Envoie le message à tous les clients WebSocket connectés
        clients.forEach((client) => {
          if (client.readyState === WebSocket.OPEN) {
            client.send(data);
          }
        });
      }
    });
  } catch (err) {
    console.error("Erreur RabbitMQ :", err);
  }
}

startRabbitMQ();
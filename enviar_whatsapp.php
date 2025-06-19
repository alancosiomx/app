<?php
require_once __DIR__ . '/vendor/autoload.php'; // Asegúrate que el SDK esté instalado

use Twilio\Rest\Client;

// ✅ CREDENCIALES DE TWILIO
$sid    = 'AC607efa330bae90ab6be43bc12d58622e';
$token  = 'b47e5e5365d06665301c0b7b222b81b2';
$twilio = new Client($sid, $token);

// ✅ NÚMEROS
$from = 'whatsapp:+14155238886';  // Twilio Sandbox
$to   = 'whatsapp:+521XXXXXXXXXX'; // Reemplaza con tu número verificado (¡importante!)

$mensaje = "👋 Hola Alan, este es tu primer mensaje real enviado desde PHP vía Twilio. 🚀";

try {
    $message = $twilio->messages->create($to, [
        "from" => $from,
        "body" => $mensaje
    ]);
    echo "✅ Mensaje enviado con SID: " . $message->sid;
} catch (Exception $e) {
    echo "❌ Error al enviar mensaje: " . $e->getMessage();
}

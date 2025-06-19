<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ✅ SDK cargado manualmente desde composer/twilio-php-main
require_once __DIR__ . '/composer/twilio-php-main/src/Twilio/autoload.php';

use Twilio\Rest\Client;

// ✅ TUS DATOS TWILIO
$sid    = 'AC607efa330bae90ab6be43bc12d58622e';
$token  = 'b47e5e5365d06665301c0b7b222b81b2';
$twilio = new Client($sid, $token);

// ✅ NÚMEROS
$from = 'whatsapp:+14155238886';
$to   = 'whatsapp:+5219982460606'; // Tu número verificado en sandbox

$mensaje = "👋 ¡Mensaje enviado desde OMNIPOS con SDK manual dentro de composer!";

try {
    $message = $twilio->messages->create($to, [
        "from" => $from,
        "body" => $mensaje
    ]);
    echo "✅ Mensaje enviado con SID: " . $message->sid;
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

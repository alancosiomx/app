<?php
require_once __DIR__ . '/../vendor/twilio-php-main/src/Twilio/autoload.php';
use Twilio\Rest\Client;

function enviarWhatsapp($mensaje, $telefono) {
    $config = require __DIR__ . '/config.php';

    $sid    = $config['sid'];
    $token  = $config['token'];
    $from   = $config['from'];

    $client = new Client($sid, $token);

    try {
        $message = $client->messages->create("whatsapp:$telefono", [
            'from' => $from,
            'body' => $mensaje
        ]);

        // Optional: log message
        $log = "[" . date('Y-m-d H:i:s') . "] ✅ Enviado a $telefono: $mensaje (SID: {$message->sid})\n";
        file_put_contents(__DIR__ . '/logs/whatsapp.log', $log, FILE_APPEND);

        return "✅ Enviado con éxito";
    } catch (Exception $e) {
        $log = "[" . date('Y-m-d H:i:s') . "] ❌ ERROR a $telefono: " . $e->getMessage() . "\n";
        file_put_contents(__DIR__ . '/logs/whatsapp.log', $log, FILE_APPEND);

        return "❌ Error: " . $e->getMessage();
    }
}

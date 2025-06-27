require_once 'whatsapp_functions.php';

try {
    $response = send_whatsapp('5219981234567', 'Hola técnico, tienes un nuevo servicio asignado 📋');
    echo "✅ Mensaje enviado con SID: {$response->sid}";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

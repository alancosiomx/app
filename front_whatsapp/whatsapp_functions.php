require_once __DIR__ . '/../vendor/twilio-php-main/src/Twilio/autoload.php';
require_once __DIR__ . '/config.php';

use Twilio\Rest\Client;

function send_whatsapp($to, $message) {
    $client = new Client(TWILIO_SID, TWILIO_TOKEN);
    return $client->messages->create(
        "whatsapp:$to",
        [
            'from' => TWILIO_NUMBER,
            'body' => $message
        ]
    );
}

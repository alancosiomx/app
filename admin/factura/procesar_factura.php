
<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?vista=nueva");
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    die("Error de seguridad. Por favor recarga la página.");
}

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die("❌ Token CSRF inválido. Recarga la página.");
}

$cliente_id = $_POST['cliente_id'] ?? null;
$concepto_ids = $_POST['concepto_id'] ?? [];
$cantidades = $_POST['cantidad'] ?? [];

if (!$cliente_id || empty($concepto_ids) || empty($cantidades)) {
    die("❌ Faltan datos del formulario.");
}

// Obtener datos del cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
$stmt->execute([':id' => $cliente_id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    die("❌ Cliente no encontrado.");
}

// Validar campos requeridos del cliente
foreach (['uso_cfdi', 'regimen_fiscal', 'codigo_postal'] as $campo) {
    if (empty($cliente[$campo])) {
        die("❌ El campo '$campo' del cliente está vacío.");
    }
}

// Obtener conceptos desde la base
$conceptos = [];
$total = 0;

for ($i = 0; $i < count($concepto_ids); $i++) {
    $id = intval($concepto_ids[$i]);
    $cantidad = floatval($cantidades[$i]) ?: 1;

    if ($id <= 0 || $cantidad <= 0) continue;

    $stmt = $pdo->prepare("SELECT * FROM conceptos_factura WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $c = $stmt->fetch();

    if (!$c) continue;

    $conceptos[] = [
        "cantidad" => $cantidad,
        "clave_prod_serv" => $c['clave_prod_serv'] ?? '78101800',
        "clave_unidad" => $c['clave_unidad'] ?? 'E48',
        "unidad" => $c['unidad'] ?? 'Servicio',
        "descripcion" => $c['descripcion'],
        "valor_unitario" => floatval($c['precio_unitario'])
    ];

    $total += $cantidad * floatval($c['precio_unitario']);
}

if (count($conceptos) === 0) {
    die("❌ No se construyó ningún concepto. Verifica que todos los campos estén llenos.");
}

// Armar payload
$payload = [
    "receptor" => [
        "rfc" => $cliente['rfc'],
        "nombre" => $cliente['razon_social'],
        "uso_cfdi" => $cliente['uso_cfdi'],
        "regimen_fiscal_receptor" => $cliente['regimen_fiscal'],
        "domicilio_fiscal_receptor" => $cliente['codigo_postal']
    ],
    "conceptos" => $conceptos
];

// Enviar a FiscalPOP
$url = "https://api.fiscalpop.com/api/v1/cfdi/stamp/" . FISCALPOP_TOKEN;


$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error_msg = curl_error($ch);
curl_close($ch);

// Validar respuesta
if (!$response || $http_code !== 200) {
    echo "<pre>❌ Error al generar factura\n";
    echo "HTTP: $http_code\n";
    echo "Error: $error_msg\n";
    echo "Respuesta:\n";
    echo htmlspecialchars($response);
    echo "</pre>";
    exit;
}

$result = json_decode($response, true);

if ($result === null || empty($result['uuid'])) {
    echo "<pre>❌ JSON inválido recibido de FiscalPOP:\n\n";
    echo htmlspecialchars($response);
    echo "\n\nHTTP CODE: $http_code</pre>";
    exit;
}

// Guardar localmente (opcional)
$stmt = $pdo->prepare("INSERT INTO facturas (cliente_id, origen, destino, precio, id_usuario) VALUES (:cliente_id, :origen, :destino, :precio, :usuario)");

foreach ($conceptos as $c) {
    $origen = explode('-', $c['descripcion'])[0] ?? '---';
    $destino = explode('-', $c['descripcion'])[1] ?? '---';
    $stmt->execute([
        ':cliente_id' => $cliente_id,
        ':origen' => trim($origen),
        ':destino' => trim($destino),
        ':precio' => $c['cantidad'] * $c['valor_unitario'],
        ':usuario' => $_SESSION['usuario_id']
    ]);
}

header("Location: index.php?vista=nueva&ok=1");
exit;
?>

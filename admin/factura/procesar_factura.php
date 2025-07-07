<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config.php';

// ✅ Validación CSRF segura
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die("❌ Error de seguridad. Por favor recarga la página.");
}

// Variables del POST
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

foreach (['uso_cfdi', 'regimen_fiscal', 'codigo_postal'] as $campo) {
    if (empty($cliente[$campo])) {
        die("❌ El campo '$campo' del cliente está vacío.");
    }
}

// Obtener conceptos desde la base
$conceptos = [];
foreach ($_POST['concepto_id'] as $i => $concepto_id) {
    $cantidad = $_POST['cantidad'][$i] ?? 1;

    $stmt = $pdo->prepare("SELECT * FROM conceptos_factura WHERE id = ?");
    $stmt->execute([$concepto_id]);
    $c = $stmt->fetch();

    if (!$c) continue; // si no lo encuentra, salta

    $conceptos[] = [
        "cantidad" => floatval($cantidad),
        "clave_prod_serv" => $c['clave_prod_serv'],
        "clave_unidad" => $c['clave_unidad'],
        "unidad" => $c['unidad'],
        "descripcion" => $c['descripcion'],
        "valor_unitario" => floatval($c['precio_unitario'])
    ];
}


    $total += $cantidad * floatval($c['precio_unitario']);
}

if (count($conceptos) === 0) {
    die("❌ No se construyó ningún concepto. Verifica que todos los campos estén llenos.");
}

// Payload para FiscalPOP
echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;

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

// Endpoint y token
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

// Validación de respuesta
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

// Guardar en la base de datos local (opcional)
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

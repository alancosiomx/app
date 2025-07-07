<?php
// app/admin/facturacion/procesar_factura.php

require_once __DIR__ . '/../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?vista=nueva");
    exit();
}

// Validación CSRF
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die("❌ Error de seguridad. Por favor recarga la página.");
}

$cliente_id = $_POST['cliente_id'] ?? null;
$concepto_ids = $_POST['concepto_id'] ?? [];
$cantidades = $_POST['cantidad'] ?? [];

if (!$cliente_id || empty($concepto_ids) || empty($cantidades)) {
    die("❌ Faltan datos para generar la factura.");
}

// Obtener datos del cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    die("❌ Cliente no encontrado.");
}

// Validar campos requeridos
foreach (['uso_cfdi', 'regimen_fiscal', 'codigo_postal', 'rfc', 'razon_social'] as $campo) {
    if (empty($cliente[$campo])) {
        die("❌ El campo '$campo' del cliente está vacío.");
    }
}

// Construir conceptos para el payload
$conceptos = [];
for ($i = 0; $i < count($concepto_ids); $i++) {
    $concepto_id = intval($concepto_ids[$i]);
    $cantidad = floatval($cantidades[$i]) ?: 1;

    if ($concepto_id <= 0 || $cantidad <= 0) continue;

    $stmt = $pdo->prepare("SELECT * FROM conceptos_factura WHERE id = ?");
    $stmt->execute([$concepto_id]);
    $c = $stmt->fetch();

    if (!$c) continue;

    $conceptos[] = [
        "claveProdServ" => $c['clave_prod_serv'],
        "claveUnidad" => $c['clave_unidad'],
        "cantidad" => $cantidad,
        "descripcion" => $c['descripcion'],
        "valorUnitario" => floatval($c['precio_unitario']),
        "impuestos" => [
            [
                "type" => "iva",
                "retencion" => false,
                "tasa" => 0.16
            ]
        ]
    ];
}

if (empty($conceptos)) {
    die("❌ No se especificaron conceptos válidos.");
}

// Construir payload para FiscalPOP
$payload = [
    "formaPago" => "01",
    "metodoPago" => "PUE",
    "lugarExpedicion" => $cliente['codigo_postal'],
    "receptor" => [
        "nombre" => $cliente['razon_social'],
        "rfc" => $cliente['rfc'],
        "usoCFDI" => $cliente['uso_cfdi'],
        "regimen" => $cliente['regimen_fiscal'],
        "zip" => $cliente['codigo_postal']
        "correo" => $cliente['email'],  // Asegúrate que esté en tu tabla clientes

    ],
    "conceptos" => $conceptos
];

$url = "https://api.fiscalpop.com/api/v1/cfdi/stamp/" . FISCALPOP_TOKEN;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error_msg = curl_error($ch);
curl_close($ch);

if ($http_code !== 200) {
    echo "<h2>❌ Error al generar factura</h2>";
    echo "<strong>HTTP Code:</strong> " . $http_code . "<br>";
    echo "<strong>Error cURL:</strong> " . htmlspecialchars($error_msg) . "<br>";
    echo "<strong>Respuesta:</strong><pre>" . htmlspecialchars($response) . "</pre>";
    exit;
}

$result = json_decode($response, true);

if ($result === null || empty($result['uuid'])) {
    echo "<h2>❌ Respuesta inválida de FiscalPOP</h2>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    exit;
}

// Guardar factura localmente (puedes adaptar la tabla y campos según tu estructura)
$stmt = $pdo->prepare("
    INSERT INTO facturas (cliente_id, origen, destino, precio, uuid, fecha, id_usuario)
    VALUES (:cliente_id, :origen, :destino, :precio, :uuid, NOW(), :usuario)
");

// Guardar cada concepto como línea en factura
foreach ($conceptos as $c) {
    // Origen y destino se deben extraer o asignar según tu lógica
    // Aquí como ejemplo lo dejamos vacío o usa la descripción
    $descripcion = $c['descripcion'];
    $origen = '';
    $destino = '';
    if (preg_match('/de (.*?) - (.*)/i', $descripcion, $matches)) {
        $origen = $matches[1];
        $destino = $matches[2];
    }

    $stmt->execute([
        ':cliente_id' => $cliente_id,
        ':origen' => $origen,
        ':destino' => $destino,
        ':precio' => $c['cantidad'] * $c['valorUnitario'],
        ':uuid' => $result['uuid'],
        ':usuario' => $_SESSION['usuario_id'] ?? null
    ]);
}

header("Location: index.php?vista=historial&ok=1");
exit;
?>

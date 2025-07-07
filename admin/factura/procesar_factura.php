<?php
// app/admin/facturacion/procesar_factura.php

require_once __DIR__ . '/../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?vista=nueva");
    exit();
}

// CSRF
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die("❌ Error de seguridad. Por favor recarga la página.");
}

// Inputs
$cliente_id = $_POST['cliente_id'] ?? null;
$concepto_ids = $_POST['concepto_id'] ?? [];
$cantidades = $_POST['cantidad'] ?? [];

if (!$cliente_id || empty($concepto_ids) || empty($cantidades)) {
    die("❌ Faltan datos para generar la factura.");
}

// Cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    die("❌ Cliente no encontrado.");
}

foreach (['uso_cfdi', 'regimen_fiscal', 'codigo_postal', 'rfc', 'razon_social'] as $campo) {
    if (empty($cliente[$campo])) {
        die("❌ El campo '$campo' del cliente está vacío.");
    }
}

// Construir conceptos
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

// Payload FiscalPOP
$payload = [
    "formaPago" => "01",
    "metodoPago" => "PUE",
    "lugarExpedicion" => $cliente['codigo_postal'],
    "receptor" => [
        "nombre" => $cliente['razon_social'],
        "rfc" => $cliente['rfc'],
        "usoCFDI" => $cliente['uso_cfdi'],
        "regimen" => $cliente['regimen_fiscal'],
        "zip" => $cliente['codigo_postal'],
        "correo" => $cliente['email'] ?? null
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

// Decode response JSON
$result = json_decode($response, true);

if ($http_code !== 200 && $http_code !== 201) {
    echo "<h2>❌ Error al generar factura</h2>";
    echo "<strong>HTTP Code:</strong> " . $http_code . "<br>";
    echo "<strong>cURL Error:</strong> " . htmlspecialchars($error_msg) . "<br>";
    echo "<strong>Respuesta:</strong><pre>" . htmlspecialchars($response) . "</pre>";
    exit;
}

if ($result === null || empty($result['uuid'])) {
    echo "<h2>❌ Respuesta inválida de FiscalPOP</h2>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    exit;
}

// Insertar factura en BD
$stmt = $pdo->prepare("
    INSERT INTO facturas (cliente_id, origen, destino, precio, uuid, fecha, id_usuario)
    VALUES (:cliente_id, :origen, :destino, :precio, :uuid, NOW(), :usuario)
");

// Extraer origen y destino de los conceptos (ejemplo sencillo)
$origen = $destino = '';
if (preg_match('/de (.*?) - (.*)/i', $conceptos[0]['descripcion'], $matches)) {
    $origen = $matches[1];
    $destino = $matches[2];
}

$precio = 0;
foreach ($conceptos as $c) {
    $precio += $c['cantidad'] * $c['valorUnitario'];
}

$stmt->execute([
    ':cliente_id' => $cliente_id,
    ':origen' => $origen,
    ':destino' => $destino,
    ':precio' => $precio,
    ':uuid' => $result['uuid'],
    ':usuario' => $_SESSION['usuario_id'] ?? null
]);

// Mostrar éxito con links de descarga
$pdf_url = "https://api.fiscalpop.com/api/v1/cfdi/pdf/" . $result['uuid'] . "?token=" . FISCALPOP_TOKEN;
$xml_url = "https://api.fiscalpop.com/api/v1/cfdi/xml/" . $result['uuid'] . "?token=" . FISCALPOP_TOKEN;

echo "<h2>✅ Factura generada correctamente</h2>";
echo "<p>UUID: " . htmlspecialchars($result['uuid']) . "</p>";
echo "<p><a href='" . htmlspecialchars($pdf_url) . "' target='_blank'>Ver / Descargar PDF</a></p>";
echo "<p><a href='" . htmlspecialchars($xml_url) . "' target='_blank'>Ver / Descargar XML</a></p>";
echo "<p><a href='index.php?vista=historial'>Volver al historial</a></p>";

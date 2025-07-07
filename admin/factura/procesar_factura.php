<?php
require_once __DIR__ . '/../../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?vista=nueva");
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    die("Error de seguridad. Por favor recarga la página.");
}

// INPUTS
$cliente_id = $_POST['cliente_id'] ?? null;
$origenes   = $_POST['origen'] ?? [];
$destinos   = $_POST['destino'] ?? [];
$cantidades = $_POST['cantidad'] ?? [];
$precios    = $_POST['precio'] ?? [];

if (!$cliente_id || count($origenes) === 0) {
    die("❌ Faltan datos del formulario.");
}

// OBTENER CLIENTE
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
$stmt->execute([':id' => $cliente_id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    die("❌ Cliente no encontrado.");
}

// CONSTRUIR CONCEPTOS
$conceptos = [];
$total = 0;

for ($i = 0; $i < count($origenes); $i++) {
    $origen   = trim($origenes[$i]);
    $destino  = trim($destinos[$i]);
    $cantidad = floatval($cantidades[$i]) ?: 1;
    $precio   = floatval($precios[$i]);

    if (!$origen || !$destino || $precio <= 0 || $cantidad <= 0) continue;

    $conceptos[] = [
        "cantidad"         => $cantidad,
        "clave_prod_serv"  => "78101800",
        "clave_unidad"     => "E48",
        "unidad"           => "Servicio",
        "descripcion"      => "Traslado de $origen a $destino",
        "valor_unitario"   => $precio
    ];

    $total += $cantidad * $precio;
}

if (empty($conceptos)) {
    die("❌ Debes ingresar al menos una línea válida.");
}

// PREPARAR PAYLOAD
$payload = [
    "receptor" => [
        "rfc"                       => $cliente['rfc'],
        "nombre"                    => $cliente['razon_social'],
        "uso_cfdi"                  => $cliente['uso_cfdi'], // ✅ CORRECTO
        "regimen_fiscal_receptor"  => $cliente['regimen_fiscal'], // ✅ CORRECTO
        "domicilio_fiscal_receptor"=> $cliente['codigo_postal'] // ✅ CORRECTO
    ],
    "conceptos" => $conceptos
];


// ENVÍO A FISCALPOP
$token = "ce74b81a-771b-4bed-9d26-666b5f023ae8";
$url = "https://api.fiscalpop.com/api/v1/cfdi/stamp/$token";

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

// DEBUG SI FALLA
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

// GUARDAR REGISTRO LOCAL
$stmt = $pdo->prepare("
    INSERT INTO facturas (cliente_id, origen, destino, precio, id_usuario)
    VALUES (:cliente_id, :origen, :destino, :precio, :usuario)
");

foreach ($conceptos as $c) {
    preg_match('/de (.*?) a (.*)/', $c['descripcion'], $partes);
    $origen  = $partes[1] ?? '---';
    $destino = $partes[2] ?? '---';

    $stmt->execute([
        ':cliente_id' => $cliente_id,
        ':origen'     => $origen,
        ':destino'    => $destino,
        ':precio'     => $c['cantidad'] * $c['valor_unitario'],
        ':usuario'    => $_SESSION['usuario_id']
    ]);
}

header("Location: index.php?vista=nueva&ok=1");
exit;

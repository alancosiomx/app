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

// Validar inputs
$cliente_id = $_POST['cliente_id'] ?? null;
$origenes   = $_POST['origen'] ?? [];
$destinos   = $_POST['destino'] ?? [];
$cantidades = $_POST['cantidad'] ?? [];
$precios    = $_POST['precio'] ?? [];

if (!$cliente_id || count($origenes) === 0) {
    die("❌ Faltan datos del formulario.");
}

// Obtener datos del cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
$stmt->execute([':id' => $cliente_id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    die("❌ Cliente no encontrado.");
}

// Construir conceptos
$conceptos = [];
$total = 0;

for ($i = 0; $i < count($origenes); $i++) {
    $origen   = trim($origenes[$i]);
    $destino  = trim($destinos[$i]);
    $cantidad = floatval($cantidades[$i]) ?: 1;
    $precio   = floatval($precios[$i]);

    if (!$origen || !$destino || $precio <= 0 || $cantidad <= 0) continue;

    $conceptos[] = [
        "descripcion"     => "Traslado de $origen a $destino",
        "cantidad"        => $cantidad,
        "precio_unitario" => $precio,
        "clave_sat"       => "78101800"
    ];

    $total += $cantidad * $precio;
}

if (empty($conceptos)) {
    die("❌ Debes ingresar al menos una línea válida de producto.");
}

// Armar payload para FiscalPOP
$payload = [
    "cliente" => [
        "rfc"            => $cliente['rfc'],
        "razon_social"   => $cliente['razon_social'],
        "correo"         => $cliente['email'],
        "uso_cfdi"       => $cliente['uso_cfdi'],
        "regimen_fiscal" => $cliente['regimen_fiscal'],
        "codigo_postal"  => $cliente['codigo_postal']
    ],
    "conceptos" => $conceptos
];

// Enviar a FiscalPOP
$ch = curl_init('https://api.fiscalpop.com/v1/facturar');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ce74b81a-771b-4bed-9d26-666b5f023ae8' // 👈 Reemplaza esto
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error_msg = curl_error($ch);

if (!$response) {
    die("❌ No hubo respuesta de la API FiscalPOP. Error cURL: $error_msg");
}

$result = json_decode($response, true);

if ($result === null) {
    echo "<pre>❌ JSON inválido recibido de FiscalPOP:\n\n";
    echo htmlspecialchars($response);
    echo "\n\nHTTP CODE: $http_code</pre>";
    exit;
}


// Validar respuesta
if ($result['status'] === true) {
    // Guardar factura localmente (sin UUID, solo registro)
    $stmt = $pdo->prepare("
        INSERT INTO facturas (cliente_id, origen, destino, precio, id_usuario)
        VALUES (:cliente_id, :origen, :destino, :precio, :usuario)
    ");

    foreach ($conceptos as $c) {
        $stmt->execute([
            ':cliente_id' => $cliente_id,
            ':origen'     => explode(" a ", str_replace("Traslado de ", "", $c['descripcion']))[0],
            ':destino'    => explode(" a ", str_replace("Traslado de ", "", $c['descripcion']))[1],
            ':precio'     => $c['cantidad'] * $c['precio_unitario'],
            ':usuario'    => $_SESSION['usuario_id']
        ]);
    }

    header("Location: index.php?vista=nueva&ok=1");
    exit;
} else {
    die("❌ Error al generar la factura: " . ($result['message'] ?? 'Respuesta inválida de la API.'));
}

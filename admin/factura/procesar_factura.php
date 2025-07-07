<?php
// app/admin/facturacion/procesar_factura.php

require_once __DIR__ . '/../../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?vista=nueva");
    exit();
}

// Seguridad CSRF
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

if (!$cliente_id || empty($concepto_ids)) {
    die("❌ Faltan datos para generar la factura.");
}

// Obtener datos del cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    die("❌ Cliente no encontrado.");
}

// Armar conceptos
$conceptos = [];

foreach ($concepto_ids as $i => $concepto_id) {
    $cantidad = floatval($cantidades[$i] ?? 1);

    $stmt = $pdo->prepare("SELECT * FROM conceptos_factura WHERE id = ?");
    $stmt->execute([$concepto_id]);
    $c = $stmt->fetch();

    if (!$c) continue;

    $conceptos[] = [
        "cantidad" => $cantidad,
        "clave_prod_serv" => $c['clave_prod_serv'],
        "clave_unidad" => $c['clave_unidad'],
        "unidad" => $c['unidad'],
        "descripcion" => $c['descripcion'],
        "valor_unitario" => floatval($c['precio_unitario'])
    ];
}

if (empty($conceptos)) {
    die("❌ No se especificaron conceptos válidos.");
}

// Armar payload FiscalPOP
$payload = [
    "fecha" => date("Y-m-d\TH:i:s"),
    "serie" => "A",
    "folio" => "01",
    "formaPago" => "01",
    "metodoPago" => "PUE",
    "lugarExpedicion" => "77533",
    "moneda" => "MXN",
    "tipoDeComprobante" => "I",
    "emisor" => [
        "rfc" => "CBI220621QM9",
        "nombre" => "COMERCIALIZADORA BRING IT",
        "regimenFiscal" => "626"
    ],
    {
  "receptor": {
    "rfc": "TDI860730D9A",
    "nombre": "TELEINFORMATICA DINAMICA SA DE CV",
    "uso_cfdi": "G03",
    "regimen_fiscal_receptor": "601",
    "domicilio_fiscal_receptor": "04330"
  },
  "conceptos": [
    {
      "cantidad": 1,
      "descripcion": "Servicio Cancún - Cozumel",
      "clave_prod_serv": "78101800",
      "clave_unidad": "E48",
      "unidad": "Servicio",
      "valor_unitario": 650
    }
  ]
}

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
curl_close($ch);

// Ver resultado
if ($http_code !== 200) {
    echo "❌ Error al generar factura<br>";
    echo "HTTP: " . $http_code . "<br>";
    echo "Error: <br>";
    echo "Respuesta:<br>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    exit;
}

// ✅ Éxito
echo "✅ Factura generada correctamente.";
echo "<pre>" . htmlspecialchars($response) . "</pre>";
?>

<?php
require_once __DIR__ . '/../../init.php';

function fiscalpop_api_post(string $url, array $data, string $token): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error_msg = curl_error($ch);
    curl_close($ch);

    return [
        'code' => $http_code,
        'response' => $response,
        'error' => $error_msg
    ];
}

// Obtén datos del cliente de tu BD
$cliente_id = $_POST['cliente_id'] ?? null;
if (!$cliente_id) die("Falta cliente_id");

$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch();
if (!$cliente) die("Cliente no encontrado");

// Tu token FiscalPOP (definido en config.php)
$token = FISCALPOP_TOKEN;

// 1. Crear cliente en FiscalPOP
$url_clientes = "https://api.fiscalpop.com/api/v1/clients/$token";

$cliente_payload = [
    "nombre" => $cliente['razon_social'],
    "rfc" => $cliente['rfc'],
    "usoCFDI" => $cliente['uso_cfdi'],
    "regimen" => $cliente['regimen_fiscal'],
    "domicilio" => [
        "codigo_postal" => $cliente['codigo_postal']
    ],
    "correo" => $cliente['email'] ?? ''
];

$res_create = fiscalpop_api_post($url_clientes, $cliente_payload, $token);
$data_create = json_decode($res_create['response'], true);

if ($res_create['code'] !== 200 && $res_create['code'] !== 201) {
    die("Error al crear cliente: HTTP {$res_create['code']} - {$res_create['response']}");
}

// Opcional: verifica aquí $data_create si la creación fue exitosa

// 2. Preparar y enviar factura (similar a lo que ya tienes)
// Ejemplo simple: (debes construir $conceptos igual que antes)

$conceptos = [
    [
        "claveProdServ" => "78101800",
        "claveUnidad" => "E48",
        "cantidad" => 1,
        "descripcion" => "Servicio ejemplo",
        "valorUnitario" => 650.00,
        "impuestos" => [
            [
                "type" => "iva",
                "retencion" => false,
                "tasa" => 0.16
            ]
        ]
    ]
];

$factura_payload = [
    "formaPago" => "01",
    "metodoPago" => "PUE",
    "lugarExpedicion" => $cliente['codigo_postal'],
    "receptor" => [
        "nombre" => $cliente['razon_social'],
        "rfc" => $cliente['rfc'],
        "usoCFDI" => $cliente['uso_cfdi'],
        "regimen" => $cliente['regimen_fiscal'],
        "zip" => $cliente['codigo_postal'],
        "correo" => $cliente['email'] ?? ''
    ],
    "conceptos" => $conceptos
];

$url_factura = "https://api.fiscalpop.com/api/v1/cfdi/stamp/$token";

$res_factura = fiscalpop_api_post($url_factura, $factura_payload, $token);
$data_factura = json_decode($res_factura['response'], true);

if ($res_factura['code'] !== 200 && $res_factura['code'] !== 201) {
    die("Error al generar factura: HTTP {$res_factura['code']} - {$res_factura['response']}");
}

// Aquí ya tienes la factura generada, puedes guardar UUID, mostrar links, etc.

echo "Factura generada exitosamente con UUID: " . htmlspecialchars($data_factura['uuid']);

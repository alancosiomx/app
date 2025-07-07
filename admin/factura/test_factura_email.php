<?php
// test_factura_email.php

$token = 'TU_TOKEN_PRODUCCION_AQUI';
$cliente = [
    'razon_social' => 'ALAN GONZALO COSIO PEREZ',
    'rfc' => 'COPA991113AA3',
    'uso_cfdi' => 'G03',
    'regimen_fiscal' => '612',
    'codigo_postal' => '77533',
    'email' => 'c.alancosio@gmail.com',
];

$conceptos = [
    [
        "claveProdServ" => "78101800",
        "claveUnidad" => "E48",
        "cantidad" => 1,
        "descripcion" => "Servicio de mensajería Cancún - Cozumel",
        "valorUnitario" => 560.34,
        "impuestos" => [
            [
                "type" => "iva",
                "retencion" => false,
                "tasa" => 0.16
            ]
        ]
    ]
];

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
        "correo" => $cliente['email']
    ],
    "conceptos" => $conceptos
];

$url = "https://api.fiscalpop.com/api/v1/cfdi/stamp/" . $token;

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

echo "HTTP Code: $http_code\n";
if ($error_msg) {
    echo "Error cURL: $error_msg\n";
}
echo "Respuesta:\n$response\n";

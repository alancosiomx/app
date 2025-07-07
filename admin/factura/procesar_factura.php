<?php
require_once __DIR__ . '/../../config/database.php';

$cliente_id = $_POST['cliente_id'];
$origen     = $_POST['origen'];
$destino    = $_POST['destino'];
$precio     = $_POST['precio'];
$id_usuario = $_SESSION['usuario_id'] ?? 1;

// Obtener datos del cliente
$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();

// Datos para la API de FiscalPOP
$payload = [
    "cliente" => [
        "rfc" => $cliente['rfc'],
        "razon_social" => $cliente['razon_social'],
        "correo" => $cliente['email'],
        "uso_cfdi" => $cliente['uso_cfdi'],
        "regimen_fiscal" => $cliente['regimen_fiscal'],
        "codigo_postal" => $cliente['codigo_postal']
    ],
    "concepto" => [
        "descripcion" => "Traslado de $origen a $destino",
        "cantidad" => 1,
        "precio_unitario" => floatval($precio),
        "clave_sat" => "78101800"
    ]
];

// Consumir API FiscalPOP
$ch = curl_init('https://api.fiscalpop.com/v1/facturar');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ce74b81a-771b-4bed-9d26-666b5f023ae8'
]);
$response = curl_exec($ch);
$result = json_decode($response, true);

if ($result['status'] === true) {
    // Guardar movimiento
    $stmt = $conn->prepare("INSERT INTO facturas (cliente_id, origen, destino, precio, id_usuario) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issdi", $cliente_id, $origen, $destino, $precio, $id_usuario);
    $stmt->execute();
    header("Location: index.php?vista=nueva&ok=1");
    exit;
} else {
    die("Error al facturar: " . ($result['message'] ?? 'Error desconocido'));
}

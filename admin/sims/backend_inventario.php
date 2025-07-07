<?php
require_once __DIR__ . '/../../config.php'; // conexión correcta

$sql = "SELECT *, DATEDIFF(CURDATE(), fecha_ultimo_movimiento) AS dias_sin_movimiento FROM inventario_sims WHERE baja_definitiva = 0";
$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['data' => $data]);

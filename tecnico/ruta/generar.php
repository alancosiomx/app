<?php
// app/tecnico/ruta/generar.php

require_once __DIR__ . '/../../../config.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("Acceso no autorizado.");
}

$tecnico_id = $_SESSION['usuario_id'];
$apiKey = "AIzaSyBxTzJOwe4yXKwmC6gSo47rPZzw4YwKww0"; // Reemplaza con tu clave real
$hoy = date("Y-m-d");

// 1. Obtener direcciones asignadas
$stmt = $pdo->prepare("SELECT domicilio, colonia, ciudad, cp FROM servicios_omnipos 
    WHERE idc = ? AND actual_status = 'En Ruta' AND DATE(fecha_inicio) = ?");
$stmt->execute([$tecnico_id, $hoy]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Validar direcciones usando Geocoding API
$direcciones_validas = [];
$omitidas = [];

foreach ($servicios as $s) {
    if (!empty($s['domicilio']) && !empty($s['cp'])) {
        $direccion = "{$s['domicilio']}, {$s['colonia']}, {$s['ciudad']}, {$s['cp']}, México";
        $encoded = urlencode($direccion);
        $geourl = "https://maps.googleapis.com/maps/api/geocode/json?address={$encoded}&key={$apiKey}";
        $response = file_get_contents($geourl);
        $data = json_decode($response, true);

        if (isset($data['results'][0]['geometry']['location_type']) && $data['results'][0]['geometry']['location_type'] === 'ROOFTOP') {
            $direcciones_validas[] = $direccion;
        } else {
            $omitidas[] = $direccion;
        }
    }
}

if (count($direcciones_validas) < 2) {
    die("No hay suficientes direcciones precisas para generar la ruta.");
}

// 3. Armar ruta
$origen = array_shift($direcciones_validas);
$destino = array_pop($direcciones_validas);
$waypoints = implode("|", array_map("urlencode", $direcciones_validas));

$url = "https://www.google.com/maps/dir/?api=1";
$url .= "&origin=" . urlencode($origen);
$url .= "&destination=" . urlencode($destino);
if (!empty($waypoints)) {
    $url .= "&waypoints={$waypoints}";
}
$url .= "&travelmode=driving";

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ruta con Validación</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        ul { margin-top: 10px; }
        li { margin-bottom: 5px; }
        .omitido { color: red; font-size: 0.9em; }
    </style>
</head>
<body>
    <h2>📍 Ruta sugerida (solo direcciones validadas)</h2>
    <ul>
        <li><strong>Origen:</strong> <?= htmlspecialchars($origen) ?></li>
        <?php foreach ($direcciones_validas as $i => $dir): ?>
            <li>📍 Parada <?= $i + 1 ?>: <?= htmlspecialchars($dir) ?></li>
        <?php endforeach; ?>
        <li><strong>Destino:</strong> <?= htmlspecialchars($destino) ?></li>
    </ul>

    <p><a href="<?= $url ?>" target="_blank" style="font-size: 18px;">🚗 Ver Ruta en Google Maps</a></p>

    <?php if (!empty($omitidas)): ?>
        <h4>⚠️ Direcciones omitidas por baja precisión:</h4>
        <ul>
            <?php foreach ($omitidas as $o): ?>
                <li class="omitido"><?= htmlspecialchars($o) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>

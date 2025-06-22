<?php
require_once __DIR__ . '/../config.php';

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$tecnico = $_GET['tecnico'] ?? '';

$where = 'WHERE 1=1';
$params = [];

if ($fecha_inicio) {
    $where .= " AND fecha_inicio >= ?";
    $params[] = $fecha_inicio . " 00:00:00";
}
if ($fecha_fin) {
    $where .= " AND fecha_inicio <= ?";
    $params[] = $fecha_fin . " 23:59:59";
}
if ($tecnico) {
    $where .= " AND idc = ?";
    $params[] = $tecnico;
}

// KPI Totales
$kpiQuery = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN estatus = 'Histórico' THEN 1 ELSE 0 END) AS concluidos,
    SUM(CASE WHEN DATE(fecha_cita) = CURDATE() THEN 1 ELSE 0 END) AS citas_hoy,
    SUM(CASE WHEN DATE(fecha_cita) = CURDATE() + INTERVAL 1 DAY THEN 1 ELSE 0 END) AS citas_manana
    FROM servicios_omnipos $where";

$stmt = $pdo->prepare($kpiQuery);
$stmt->execute($params);
$kpi = $stmt->fetch(PDO::FETCH_ASSOC);

// Gráfica
$graficaQuery = "
    SELECT DATE(fecha_inicio) AS fecha, COUNT(*) AS total
    FROM servicios_omnipos
    $where
    GROUP BY DATE(fecha_inicio)
    ORDER BY fecha ASC
";

$stmt = $pdo->prepare($graficaQuery);
$stmt->execute($params);
$grafica_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Filtros -->
<form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div>
        <label class="text-sm font-medium text-gray-700">Fecha inicio</label>
        <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" class="w-full rounded border-gray-300 p-2 text-sm">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Fecha fin</label>
        <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>" class="w-full rounded border-gray-300 p-2 text-sm">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Técnico</label>
        <input type="text" name="tecnico" value="<?= htmlspecialchars($tecnico) ?>" placeholder="Nombre IDC" class="w-full rounded border-gray-300 p-2 text-sm">
    </div>
    <div class="flex items-end justify-end">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Filtrar</button>
    </div>
</form>

<!-- KPIs -->
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm">Total Servicios</div>
        <div class="text-2xl font-bold text-blue-600"><?= $kpi['total'] ?? 0 ?></div>
    </div>
    <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm">Concluidos</div>
        <div class="text-2xl font-bold text-green-600"><?= $kpi['concluidos'] ?? 0 ?></div>
    </div>
    <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm">Citas Hoy / Mañana</div>
        <div class="text-xl font-semibold text-yellow-600"><?= $kpi['citas_hoy'] ?? 0 ?> / <?= $kpi['citas_manana'] ?? 0 ?></div>
    </div>
</div>

<!-- Gráfica (puedes usar Chart.js u otra librería, esto es ejemplo textual) -->
<div class="bg-white p-4 rounded shadow">
    <h3 class="text-lg font-bold mb-2">📊 Servicios por Día</h3>
    <ul class="text-sm text-gray-700 space-y-1">
        <?php foreach ($grafica_data as $row): ?>
            <li><?= $row['fecha'] ?>: <?= $row['total'] ?> servicios</li>
        <?php endforeach; ?>
    </ul>
</div>

<?php
require_once __DIR__ . '/../config.php';

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$tecnico = $_GET['tecnico'] ?? '';

$where = 'WHERE 1=1';
$params = [];

if ($fecha_inicio) {
    $where .= " AND fecha_atencion >= ?";
    $params[] = $fecha_inicio . " 00:00:00";
}
if ($fecha_fin) {
    $where .= " AND fecha_atencion <= ?";
    $params[] = $fecha_fin . " 23:59:59";
}
if ($tecnico) {
    $where .= " AND idc = ?";
    $params[] = $tecnico;
}

// KPI Totales por estatus
$estadoQuery = "SELECT 
    SUM(CASE WHEN estatus = 'Por Asignar' THEN 1 ELSE 0 END) AS por_asignar,
    SUM(CASE WHEN estatus = 'En Ruta' THEN 1 ELSE 0 END) AS en_ruta,
    SUM(CASE WHEN estatus = 'Histórico' AND conclusion = 'Éxito' THEN 1 ELSE 0 END) AS exitos,
    SUM(CASE WHEN estatus = 'Histórico' AND conclusion = 'Rechazo' THEN 1 ELSE 0 END) AS rechazos
    FROM servicios_omnipos";
$resumen = $pdo->query($estadoQuery)->fetch(PDO::FETCH_ASSOC);

// KPIs
$kpiQuery = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN DATE(fecha_cita) = CURDATE() THEN 1 ELSE 0 END) AS citas_hoy,
    SUM(CASE WHEN DATE(fecha_cita) = CURDATE() + INTERVAL 1 DAY THEN 1 ELSE 0 END) AS citas_manana
    FROM servicios_omnipos $where";
$stmt = $pdo->prepare($kpiQuery);
$stmt->execute($params);
$kpi = $stmt->fetch(PDO::FETCH_ASSOC);

// Gráfica por día
$graficaQuery = "SELECT DATE(fecha_atencion) AS fecha, COUNT(*) AS total FROM servicios_omnipos $where GROUP BY fecha ORDER BY fecha ASC";
$stmt = $pdo->prepare($graficaQuery);
$stmt->execute($params);
$grafica_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gráfica por técnico
$graficaTecnicoQuery = "SELECT idc, COUNT(*) AS total FROM servicios_omnipos $where AND estatus = 'Histórico' GROUP BY idc ORDER BY total DESC";
$stmt = $pdo->prepare($graficaTecnicoQuery);
$stmt->execute($params);
$grafica_tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm">Por Asignar</div>
        <div class="text-2xl font-bold text-yellow-600"><?= $resumen['por_asignar'] ?></div>
    </div>
    <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm">En Ruta</div>
        <div class="text-2xl font-bold text-blue-600"><?= $resumen['en_ruta'] ?></div>
    </div>
    <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm">Éxitos</div>
        <div class="text-2xl font-bold text-green-600"><?= $resumen['exitos'] ?></div>
    </div>
    <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm">Rechazos</div>
        <div class="text-2xl font-bold text-red-600"><?= $resumen['rechazos'] ?></div>
    </div>
</div>

<!-- KPIs adicionales -->
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm">Total Filtrados</div>
        <div class="text-2xl font-bold text-indigo-600"><?= $kpi['total'] ?? 0 ?></div>
    </div>
    <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm">Citas Hoy</div>
        <div class="text-2xl font-bold text-yellow-600"><?= $kpi['citas_hoy'] ?? 0 ?></div>
    </div>
    <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm">Citas Mañana</div>
        <div class="text-2xl font-bold text-yellow-600"><?= $kpi['citas_manana'] ?? 0 ?></div>
    </div>
</div>

<!-- Gráfica por día -->
<div class="bg-white p-4 rounded shadow mb-6">
    <h3 class="text-lg font-bold mb-2">📅 Servicios Atendidos por Día</h3>
    <ul class="text-sm text-gray-700 space-y-1">
        <?php foreach ($grafica_data as $row): ?>
            <li><?= $row['fecha'] ?>: <strong><?= $row['total'] ?></strong> servicios</li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- Gráfica por técnico -->
<div class="bg-white p-4 rounded shadow">
    <h3 class="text-lg font-bold mb-2">🧰 Servicios Concluidos por Técnico</h3>
    <ul class="text-sm text-gray-700 space-y-1">
        <?php foreach ($grafica_tecnicos as $t): ?>
            <li><strong><?= htmlspecialchars($t['idc']) ?></strong>: <?= $t['total'] ?> servicios</li>
        <?php endforeach; ?>
    </ul>
</div>

<?php
// Filtros opcionales
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$tecnico = $_GET['idc'] ?? '';

// Lista de técnicos únicos con servicios
$tecnicos = $pdo->query("SELECT DISTINCT idc FROM servicios_omnipos WHERE idc IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);

// Armado de condición para filtros
$where = "WHERE 1=1";
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

// KPIs
$total = $pdo->query("SELECT COUNT(*) FROM servicios_omnipos")->fetchColumn();
$porAsignar = $pdo->query("SELECT COUNT(*) FROM servicios_omnipos WHERE estatus = 'Por Asignar'")->fetchColumn();
$enRuta = $pdo->query("SELECT COUNT(*) FROM servicios_omnipos WHERE estatus = 'En Ruta'")->fetchColumn();
$historico = $pdo->query("SELECT COUNT(*) FROM servicios_omnipos WHERE estatus = 'Histórico'")->fetchColumn();

// Citas
$hoy = date('Y-m-d');
$maniana = date('Y-m-d', strtotime('+1 day'));

$citasHoy = $pdo->prepare("SELECT COUNT(*) FROM servicios_omnipos WHERE fecha_cita = ?");
$citasHoy->execute([$hoy]);
$citasHoy = $citasHoy->fetchColumn();

$citasManiana = $pdo->prepare("SELECT COUNT(*) FROM servicios_omnipos WHERE fecha_cita = ?");
$citasManiana->execute([$maniana]);
$citasManiana = $citasManiana->fetchColumn();

// Gráfica de servicios históricos
$graficaQuery = "
    SELECT DATE(fecha_atencion) AS fecha, COUNT(*) AS total
    FROM servicios_omnipos
    WHERE estatus = 'Histórico'
    " . ($where !== "WHERE 1=1" ? $where : "AND fecha_atencion >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)") . "
    GROUP BY DATE(fecha_atencion)
    ORDER BY fecha ASC
";
$stmt = $pdo->prepare($graficaQuery);
$stmt->execute($params);
$grafica = $stmt->fetchAll(PDO::FETCH_ASSOC);

$fechas = array_column($grafica, 'fecha');
$totales = array_column($grafica, 'total');
?>

<h2 class="text-xl font-bold text-gray-800 mb-6">📊 Resumen de Servicios</h2>

<!-- Filtros -->
<form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <div>
    <label class="text-sm font-semibold text-gray-700">Fecha Inicio</label>
    <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" class="w-full rounded border p-2 text-sm">
  </div>
  <div>
    <label class="text-sm font-semibold text-gray-700">Fecha Fin</label>
    <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>" class="w-full rounded border p-2 text-sm">
  </div>
  <div>
    <label class="text-sm font-semibold text-gray-700">Técnico</label>
    <select name="idc" class="w-full rounded border p-2 text-sm">
      <option value="">Todos</option>
      <?php foreach ($tecnicos as $idc): ?>
        <option value="<?= htmlspecialchars($idc) ?>" <?= $tecnico === $idc ? 'selected' : '' ?>>
          <?= htmlspecialchars($idc) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="flex items-end">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm w-full">
      Aplicar Filtros
    </button>
  </div>
</form>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-white p-4 rounded-xl shadow text-center">
    <div class="text-sm text-gray-500">Total</div>
    <div class="text-2xl font-bold text-gray-800"><?= $total ?></div>
  </div>
  <div class="bg-white p-4 rounded-xl shadow text-center">
    <div class="text-sm text-yellow-600">Por Asignar</div>
    <div class="text-2xl font-bold text-yellow-600"><?= $porAsignar ?></div>
  </div>
  <div class="bg-white p-4 rounded-xl shadow text-center">
    <div class="text-sm text-blue-600">En Ruta</div>
    <div class="text-2xl font-bold text-blue-600"><?= $enRuta ?></div>
  </div>
  <div class="bg-white p-4 rounded-xl shadow text-center">
    <div class="text-sm text-green-600">Histórico</div>
    <div class="text-2xl font-bold text-green-600"><?= $historico ?></div>
  </div>
  <div class="bg-white p-4 rounded-xl shadow text-center col-span-2">
    <div class="text-sm text-purple-600">Citas Hoy</div>
    <div class="text-2xl font-bold text-purple-600"><?= $citasHoy ?></div>
  </div>
  <div class="bg-white p-4 rounded-xl shadow text-center col-span-2">
    <div class="text-sm text-pink-600">Citas Mañana</div>
    <div class="text-2xl font-bold text-pink-600"><?= $citasManiana ?></div>
  </div>
</div>

<!-- Gráfica -->
<div class="bg-white p-6 rounded-xl shadow">
  <h3 class="text-lg font-semibold mb-4">📈 Servicios Atendidos (últimos días)</h3>
  <canvas id="grafica_servicios" height="100"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('grafica_servicios').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($fechas) ?>,
        datasets: [{
            label: 'Servicios concluidos',
            data: <?= json_encode($totales) ?>,
            backgroundColor: '#3b82f6'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

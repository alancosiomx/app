<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/service_functions.php';
session_start();

// Filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$tecnico_id = $_GET['tecnico_id'] ?? '';
$ticket_busqueda = $_GET['ticket'] ?? '';

// Técnicos
$tecnicos = $pdo->query("SELECT id, nombre FROM usuarios WHERE activo = 1 AND roles LIKE '%idc%'")->fetchAll(PDO::FETCH_ASSOC);

// Query base
$sql = "SELECT * FROM servicios_omnipos WHERE estatus = 'Histórico' ";
$params = [];

if ($fecha_inicio) {
    $sql .= " AND fecha_atencion >= ? ";
    $params[] = $fecha_inicio . ' 00:00:00';
}
if ($fecha_fin) {
    $sql .= " AND fecha_atencion <= ? ";
    $params[] = $fecha_fin . ' 23:59:59';
}
if ($tecnico_id) {
    $stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $stmt->execute([$tecnico_id]);
    $nombre_tecnico = $stmt->fetchColumn();
    $sql .= " AND idc = ? ";
    $params[] = $nombre_tecnico;
}
if ($ticket_busqueda) {
    $sql .= " AND (ticket LIKE ? OR afiliacion LIKE ?) ";
    $params[] = "%$ticket_busqueda%";
    $params[] = "%$ticket_busqueda%";
}

$sql .= " ORDER BY fecha_atencion DESC LIMIT 200";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="mb-4">Histórico de Servicios</h1>
<p>Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador') ?></strong></p>

<?php include __DIR__ . '/../includes/tabs_servicios.php'; ?>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class='alert alert-success'><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
    <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class='alert alert-danger'><?= htmlspecialchars($_SESSION['error']) ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form method="get" class="row g-3 mb-3">
    <input type="hidden" name="tab" value="concluido">
    <div class="col-md-3">
        <label for="fecha_inicio" class="form-label">Fecha inicio</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($fecha_inicio) ?>">
    </div>
    <div class="col-md-3">
        <label for="fecha_fin" class="form-label">Fecha fin</label>
        <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($fecha_fin) ?>">
    </div>
    <div class="col-md-3">
        <label for="tecnico_id" class="form-label">Técnico</label>
        <select id="tecnico_id" name="tecnico_id" class="form-select">
            <option value="">Todos</option>
            <?php foreach ($tecnicos as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $tecnico_id == $t['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label for="ticket" class="form-label">Ticket o Afiliación</label>
        <input type="text" id="ticket" name="ticket" class="form-control" placeholder="Buscar..." value="<?= htmlspecialchars($ticket_busqueda) ?>">
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="exportar_excel.php?<?= http_build_query($_GET) ?>" class="btn btn-success ms-2">Exportar a Excel</a>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-sm" id="tabla_concluido">
        <thead class="table-light">
            <tr>
                <th>Ticket</th>
                <th>Afiliación</th>
                <th>Comercio</th>
                <th>Ciudad</th>
                <th>Fecha Atención</th>
                <th>Resultado</th>
                <th>Comentarios</th>
                <th>📃 HS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicios as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['ticket']) ?></td>
                    <td><?= htmlspecialchars($s['afiliacion']) ?></td>
                    <td><?= htmlspecialchars($s['comercio']) ?></td>
                    <td><?= htmlspecialchars($s['ciudad']) ?></td>
                    <td><?= htmlspecialchars($s['fecha_atencion']) ?></td>
                    <td><?= htmlspecialchars($s['conclusion']) ?></td>
                    <td><?= htmlspecialchars($s['comentarios']) ?></td>
                    <td><a href="generar_hs.php?ticket=<?= urlencode($s['ticket']) ?>" target="_blank" title="Reimprimir Hoja de Servicio">📃</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

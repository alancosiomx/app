<?php
// Filtros
$serie_filtro = $_GET['serie'] ?? '';
$usuario_filtro = $_GET['usuario'] ?? '';
$tipo_filtro = $_GET['tipo'] ?? '';
$fecha_ini = $_GET['fecha_ini'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

// Construir query dinámico
$where = [];
$params = [];

if ($serie_filtro !== '') {
    $where[] = "serie LIKE ?";
    $params[] = "%$serie_filtro%";
}
if ($usuario_filtro !== '') {
    $where[] = "usuario LIKE ?";
    $params[] = "%$usuario_filtro%";
}
if ($tipo_filtro !== '') {
    $where[] = "tipo_movimiento = ?";
    $params[] = $tipo_filtro;
}
if ($fecha_ini !== '' && $fecha_fin !== '') {
    $where[] = "DATE(fecha) BETWEEN ? AND ?";
    $params[] = $fecha_ini;
    $params[] = $fecha_fin;
}

$sql = "SELECT * FROM log_inventario";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Exportar a Excel simple (.xls)
if (isset($_GET['exportar']) && $_GET['exportar'] == '1') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=log_inventario_" . date('Ymd_His') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<table border='1'>";
    echo "<tr>
            <th>Fecha</th>
            <th>Serie</th>
            <th>Movimiento</th>
            <th>Usuario</th>
            <th>Observaciones</th>
          </tr>";

    foreach ($logs as $l) {
        echo "<tr>
                <td>{$l['fecha']}</td>
                <td>" . htmlspecialchars($l['serie']) . "</td>
                <td>{$l['tipo_movimiento']}</td>
                <td>" . htmlspecialchars($l['usuario']) . "</td>
                <td>" . htmlspecialchars($l['observaciones']) . "</td>
              </tr>";
    }

    echo "</table>";
    exit;
}

// Tipos de movimiento
$tipos = ['Recepción', 'Asignación', 'Instalación', 'Retiro', 'Dañado', 'Devolución'];
?>

<h4>📖 Historial de Movimientos</h4>

<form method="get" class="row g-3 mb-4">
    <div class="col-md-2">
        <input type="text" name="serie" class="form-control" placeholder="Serie" value="<?= htmlspecialchars($serie_filtro) ?>">
    </div>
    <div class="col-md-2">
        <input type="text" name="usuario" class="form-control" placeholder="Usuario" value="<?= htmlspecialchars($usuario_filtro) ?>">
    </div>
    <div class="col-md-2">
        <select name="tipo" class="form-select">
            <option value="">Todos</option>
            <?php foreach ($tipos as $tipo): ?>
                <option value="<?= $tipo ?>" <?= $tipo === $tipo_filtro ? 'selected' : '' ?>><?= $tipo ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <input type="date" name="fecha_ini" class="form-control" value="<?= $fecha_ini ?>">
    </div>
    <div class="col-md-2">
        <input type="date" name="fecha_fin" class="form-control" value="<?= $fecha_fin ?>">
    </div>
    <div class="col-md-1">
        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
    </div>
    <div class="col-md-1">
        <button type="submit" name="exportar" value="1" class="btn btn-success w-100">Excel</button>
    </div>
</form>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-sm table-striped table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Serie</th>
                    <th>Movimiento</th>
                    <th>Usuario</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                    <tr>
                        <td><?= $l['fecha'] ?></td>
                        <td><?= htmlspecialchars($l['serie']) ?></td>
                        <td><span class="badge bg-dark"><?= $l['tipo_movimiento'] ?></span></td>
                        <td><?= htmlspecialchars($l['usuario']) ?></td>
                        <td><?= htmlspecialchars($l['observaciones']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

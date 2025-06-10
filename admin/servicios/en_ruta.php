<?php
require_once __DIR__ . '/../../init.php';
require_once 'funciones_servicios.php';

// 🧠 PROCESAR ACCIONES
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket = $_POST['ticket'] ?? null;
    $accion = $_POST['accion'] ?? '';
    $reasignar = $_POST['reasignar_a'] ?? '';

    if ($ticket) {
        if ($accion === 'exito') {
            marcarResultado($pdo, $ticket, 'Exito');
        } elseif ($accion === 'rechazo') {
            marcarResultado($pdo, $ticket, 'Rechazo');
        } elseif ($accion === 'reasignar' && !empty($reasignar)) {
            asignarTecnico($pdo, $ticket, $reasignar);
        }
    }

    // Refrescar la página para evitar resubmit
    header("Location: ../../servicios.php?tab=en_ruta");
    exit;
}

// 🔄 CARGAR SERVICIOS EN RUTA
$servicios = getServiciosPorEstado($pdo, 'En Ruta');

// 🔄 Lista de técnicos disponibles para reasignar
$idcs = $pdo->query("SELECT nombre FROM usuarios 
    JOIN usuarios_roles ON usuarios.id = usuarios_roles.usuario_id 
    WHERE rol = 'idc' AND activo = 1")->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- HTML embebido -->
<p>Se encontraron <?= count($servicios) ?> servicios en ruta.</p>
<table class="table table-bordered table-hover bg-white">
    <thead class="table-dark">
        <tr>
            <th>Ticket</th>
            <th>Afiliación</th>
            <th>Comercio</th>
            <th>Ciudad</th>
            <th>Asignado a</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($servicios as $servicio): ?>
            <tr>
                <td><?= $servicio['ticket'] ?></td>
                <td><?= $servicio['afiliacion'] ?></td>
                <td><?= $servicio['comercio'] ?></td>
                <td><?= $servicio['ciudad'] ?></td>
                <td><?= $servicio['idc'] ?></td>
                <td>
                    <form method="POST" class="d-flex flex-wrap gap-2">
                        <input type="hidden" name="ticket" value="<?= $servicio['ticket'] ?>">

                        <button name="accion" value="exito" class="btn btn-success btn-sm">Éxito</button>
                        <button name="accion" value="rechazo" class="btn btn-danger btn-sm">Rechazo</button>

                        <select name="reasignar_a" class="form-select form-select-sm">
                            <option value="">Reasignar a...</option>
                            <?php foreach ($idcs as $idc): ?>
                                <option value="<?= $idc ?>"><?= $idc ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button name="accion" value="reasignar" class="btn btn-warning btn-sm">Reasignar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

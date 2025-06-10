<?php
require_once __DIR__ . '/../../config.php';


// Procesar Asignación aquí mismo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tecnico_id'], $_POST['tickets'])) {
    $tecnico_id = trim($_POST['tecnico_id']);
    $tickets = $_POST['tickets'];

    if (!empty($tecnico_id) && !empty($tickets)) {
        // Buscar nombre del técnico
        $stmtTecnico = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
        $stmtTecnico->execute([$tecnico_id]);
        $tecnicoData = $stmtTecnico->fetch(PDO::FETCH_ASSOC);

        if ($tecnicoData) {
            $nombreTecnico = $tecnicoData['nombre'];

            // Actualizar servicios seleccionados
            $inQuery = implode(',', array_fill(0, count($tickets), '?'));
            $params = array_merge([$nombreTecnico], $tickets);

            $stmt = $pdo->prepare("UPDATE servicios_omnipos SET idc = ?, estatus = 'En Ruta' WHERE ticket IN ($inQuery)");
            $stmt->execute($params);

            // Redirigir a la misma página con éxito
            header("Location: " . BASE_URL . "/admin/servicios/servicios.php?tab=por_asignar&success=1");
            exit;
        }
    }
}
?>

<div class="container mt-3">
    <h2>Servicios Por Asignar</h2>

    <!-- Mensaje de éxito -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ¡Servicios asignados exitosamente!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="POST" id="formAsignar">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="tecnico" class="form-label">Asignar Técnico:</label>
                <select name="tecnico_id" id="tecnico" class="form-select" required>
                    <option value="">Selecciona un Técnico</option>
                    <?php
                    // Consulta Técnicos
                    $queryTecnicos = "
                        SELECT id, nombre
                        FROM usuarios
                        WHERE roles = 'tecnico' AND activo = 1
                    ";
                    $resultTecnicos = $pdo->query($queryTecnicos);
                    $tecnicos = $resultTecnicos->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($tecnicos as $tecnico): ?>
                        <option value="<?= htmlspecialchars($tecnico['id']) ?>">
                            <?= htmlspecialchars($tecnico['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <table id="tablaPorAsignar" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>Ticket</th>
                    <th>Afiliación</th>
                    <th>Comercio</th>
                    <th>Ciudad</th>
                    <th>Horario</th>
                    <th>Comentarios</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta Servicios Por Asignar
                $queryServicios = "
                    SELECT ticket, afiliacion, comercio, ciudad, horario, comentarios 
                    FROM servicios_omnipos 
                    WHERE estatus = 'Por Asignar' 
                    ORDER BY fecha_limite ASC
                ";
                $resultServicios = $pdo->query($queryServicios);
                $servicios = $resultServicios->fetchAll(PDO::FETCH_ASSOC);
                foreach ($servicios as $servicio): ?>
                <tr>
                    <td><input type="checkbox" name="tickets[]" value="<?= htmlspecialchars($servicio['ticket']) ?>"></td>
                    <td><?= htmlspecialchars($servicio['ticket'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($servicio['afiliacion'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($servicio['comercio'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($servicio['ciudad'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($servicio['horario'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($servicio['comentarios'] ?? '—') ?></td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm detalles" data-ticket="<?= htmlspecialchars($servicio['ticket']) ?>">🔍</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" class="btn btn-success mt-3">Asignar Técnico</button>
    </form>
</div>

<!-- Scripts de DataTables y Bootstrap -->
<link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#tablaPorAsignar').DataTable({
        "scrollX": true
    });

    // Seleccionar todos los checkboxes
    $('#selectAll').click(function() {
        var checkedStatus = this.checked;
        $('input[name="tickets[]"]').each(function() {
            this.checked = checkedStatus;
        });
    });

    // Botón de Detalles
    $(document).on('click', '.detalles', function() {
        const ticket = $(this).data('ticket');
        alert('Detalles del ticket: ' + ticket);
    });
});
</script>

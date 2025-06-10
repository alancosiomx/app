<?php
require_once __DIR__ . '/../../config.php';
session_start();

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login.php");
    exit();
}

// Consulta servicios Concluidos (Histórico)
$query = "SELECT * FROM servicios_omnipos WHERE estatus = 'Histórico' ORDER BY fecha_cierre DESC";
$result = $pdo->query($query);
$servicios = $result->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container mt-3">
    <h2>Servicios Concluidos (Histórico)</h2>
    
    <table id="tablaConcluido" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Afiliación</th>
                <th>Comercio</th>
                <th>Ciudad</th>
                <th>Resultado</th>
                <th>Fecha de Cierre</th>
                <th>SLA</th>
                <th>Detalles</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicios as $servicio): ?>
                <tr>
                    <td><?= htmlspecialchars($servicio['ticket']) ?></td>
                    <td><?= htmlspecialchars($servicio['afiliacion']) ?></td>
                    <td><?= htmlspecialchars($servicio['comercio']) ?></td>
                    <td><?= htmlspecialchars($servicio['ciudad']) ?></td>
                    <td><?= htmlspecialchars($servicio['conclusion']) ?></td>
                    <td><?= htmlspecialchars($servicio['fecha_cierre']) ?></td>
                    <td><?= htmlspecialchars($servicio['sla']) ?></td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm detalles" data-ticket="<?= $servicio['ticket'] ?>">🔍</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- DataTables Scripts -->
<link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#tablaConcluido').DataTable({
        "scrollX": true
    });

    $('.detalles').click(function() {
        const ticket = $(this).data('ticket');
        // Puedes hacer una llamada AJAX para mostrar detalles
        alert('Detalles del ticket: ' + ticket);
    });
});
</script>

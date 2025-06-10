<?php
// your_project/tecnico/dashboard.php
require_once __DIR__ . '/../init.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Técnico';
$idc     = $usuario; // asumiendo que `nombre` del usuario es el mismo que se guarda en la columna `idc`

// Obtener los servicios asignados al IDC con estado 'En Ruta'
// Asegúrate de que tu tabla 'servicios_omnipos' tenga la columna 'ticket' para el ID
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE actual_status = 'En Ruta' AND idc = :idc");
$stmt->execute(['idc' => $idc]);
$servicios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Técnico - OMNIPOS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand">OMNIPOS - Técnico</span>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item text-white mt-2 me-3"><?= htmlspecialchars($usuario) ?></li>
                <li class="nav-item"><a class="nav-link" href="/logout.php">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container" style="margin-top: 80px;">
    <h4 class="mb-4">Mis servicios asignados</h4>

    <?php
    // Mensajes de estado (éxito/error) al generar PDF
    // Si el PDF se genera correctamente, la URL puede llevar un 'file_url'
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'success') {
            // Aquí el 'file_url' no se usará directamente porque el PDF se stream-ea,
            // pero si el process_service_sheet.php se modificara para guardar el PDF y luego redirigir,
            // entonces esta parte sería útil.
            echo '<div class="alert alert-success">PDF generado exitosamente.</div>';
        } elseif ($_GET['status'] == 'error') {
            echo '<div class="alert alert-danger">Error al generar el PDF: ' . htmlspecialchars($_GET['message']) . '</div>';
        }
    }
    ?>

    <?php if (count($servicios) === 0): ?>
        <div class="alert alert-info">No tienes servicios asignados actualmente.</div>
    <?php else: ?>
        <table class="table table-bordered bg-white">
            <thead class="table-dark">
                <tr>
                    <th>Ticket</th>
                    <th>Comercio</th>
                    <th>Ciudad</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicios as $s): ?>
                    <tr>
                        <td><?= $s['ticket'] ?></td>
                        <td><?= $s['comercio'] ?></td>
                        <td><?= $s['ciudad'] ?></td>
                        <td><?= $s['telefono_contacto_1'] ?></td>
                        <td>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="ticket" value="<?= $s['ticket'] ?>">
                                <button name="accion" value="exito" class="btn btn-success btn-sm">Éxito</button>
                                <button name="accion" value="rechazo" class="btn btn-danger btn-sm">Rechazo</button>
                            </form>
                                                        <a href="descargar_hs.php?ticket_id=<?= htmlspecialchars($s['ticket']) ?>" target="_blank" class="btn btn-info btn-sm ms-2">Generar HS</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
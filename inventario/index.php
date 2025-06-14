<?php
require '../auth.php';
require '../config.php';
require '../permisos.php';

if (!tienePermiso('ver_inventario')) {
    die('⛔ Acceso denegado');
}

if (!defined('HEAD') || !defined('MENU') || !defined('FOOT')) {
    require_once dirname(__DIR__) . '/config.php';
}
require HEAD;
require MENU;
?>

<div class="main-content">
    <h3>Inventario de Terminales</h3>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <?php if (tienePermiso('registrar_inventario')): ?>
            <a href="nuevo.php" class="btn btn-success">+ Nueva Terminal</a>
            <div>
                <a href="fabricantes.php" class="btn btn-outline-secondary btn-sm me-2">Gestionar Fabricantes</a>
                <a href="modelos.php" class="btn btn-outline-secondary btn-sm">Gestionar Modelos</a>
            </div>
        <?php endif; ?>
    </div>


    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-sm align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fabricante</th>
                        <th>Modelo</th>
                        <th>Banco</th>
                        <th>Descripción</th>
                        <th>Activo</th>
                        <th>Fecha ingreso</th>
                        <?php if (tienePermiso('asignar_inventario')): ?>
                            <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("
                        SELECT inv.id, f.nombre AS fabricante, m.nombre AS modelo, inv.banco, inv.descripcion, inv.activo, inv.fecha_ingreso
                        FROM inventario_disponible inv
                        INNER JOIN modelos m ON inv.modelo_id = m.id
                        INNER JOIN fabricantes f ON m.fabricante_id = f.id
                        ORDER BY inv.id DESC
                    ");

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>" . htmlspecialchars($row['fabricante']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['banco']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
                        echo "<td>" . ($row['activo'] ? 'Sí' : 'No') . "</td>";
                        echo "<td>" . htmlspecialchars($row['fecha_ingreso']) . "</td>";
                        if (tienePermiso('asignar_inventario')) {
                            echo "<td>
                                <a href='editar.php?id={$row['id']}' class='btn btn-sm btn-primary'>Editar</a> ";

                            if ($row['activo']) {
                                echo "<a href='desactivar.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('¿Desactivar esta terminal?');\">Desactivar</a>";
                            } else {
                                echo "<a href='reactivar.php?id={$row['id']}' class='btn btn-sm btn-success' onclick=\"return confirm('¿Reactivar esta terminal?');\">Reactivar</a>";
                            }

                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require FOOT; ?>

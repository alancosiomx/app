<?php
require '../../auth.php';
require '../../config.php';

if (!in_array('admin', $_SESSION['usuario_roles'])) {
    header("Location: ../../dashboard.php");
    exit;
}

require '../../includes/header.php';
require '../../includes/menu.php';
?>

<div class="main-content">
    <h3>Gestión de Usuarios</h3>
    <a href="nuevo.php" class="btn btn-success mb-3">+ Nuevo Usuario</a>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Roles</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id ASC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['roles']) . "</td>";
                        echo "<td>" . ($row['activo'] ? 'Sí' : 'No') . "</td>";
                        echo "<td>
                                <a href='editar.php?id={$row['id']}' class='btn btn-sm btn-primary'>Editar</a> ";
                        if ($row['activo']) {
                            echo "<a href='eliminar.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('¿Desactivar este usuario?');\">Desactivar</a>";
                        } else {
                            echo "<a href='reactivar.php?id={$row['id']}' class='btn btn-sm btn-success' onclick=\"return confirm('¿Reactivar este usuario?');\">Reactivar</a>";
                        }
                        echo "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>

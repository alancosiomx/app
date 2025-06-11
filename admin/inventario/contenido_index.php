<?php
$terminales = $pdo->query("
    SELECT t.id, t.serie, t.estado, t.banco, t.fecha_entrada, t.observaciones,
           m.nombre AS modelo, f.nombre AS fabricante
    FROM inventario_tpv t
    JOIN modelos m ON t.modelo_id = m.id
    JOIN fabricantes f ON m.fabricante_id = f.id
    ORDER BY t.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>📦 Inventario de Terminales</h4>
    <div>
        <a href="nuevo.php" class="btn btn-success me-2">+ Nueva Terminal</a>
        <a href="asignar.php" class="btn btn-outline-primary me-2">Asignar a Técnico</a>
        <a href="recibir_danado.php" class="btn btn-outline-danger me-2">Recibir como Dañada</a>
        <a href="preparar_envio.php" class="btn btn-outline-dark me-2">Enviar a CEDIS</a>
        <a href="movimientos.php" class="btn btn-outline-secondary">📄 Movimientos</a>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Serie</th>
                    <th>Modelo</th>
                    <th>Fabricante</th>
                    <th>Banco</th>
                    <th>Estado</th>
                    <th>Fecha ingreso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($terminales as $t): ?>
                    <tr>
                        <td><?= $t['id'] ?></td>
                        <td><?= htmlspecialchars($t['serie']) ?></td>
                        <td><?= htmlspecialchars($t['modelo']) ?></td>
                        <td><?= htmlspecialchars($t['fabricante']) ?></td>
                        <td><?= htmlspecialchars($t['banco']) ?></td>
                        <td><?= htmlspecialchars($t['estado']) ?></td>
                        <td><?= $t['fecha_entrada'] ?></td>
                        <td>
                            <a href="editar.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Navegación por tabs
$tabs = [
    'resumen' => 'Resumen',
    'asignar' => 'Asignar',
    'editar' => 'Editar',
    'devolver' => 'Devoluciones',
    'recibir_danado' => 'Dañados'
];

echo "<div class='flex gap-2 mb-6'>";
foreach (\$tabs as \$clave => \$label) {
    \$active = \$tab === \$clave ? 'bg-gray-900 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300';
    echo "<a href='?tab=\$clave&type=\$tipo' class='px-3 py-1.5 rounded \$active'>\$label</a>";
}
echo "</div>";

require_once __DIR__ . '/../init.php';

// Obtener técnicos del sistema
$stmt = $pdo->query("SELECT nombre FROM usuarios WHERE roles LIKE '%tecnico%' ORDER BY nombre");
$tecnicos = $stmt->fetchAll(PDO::FETCH_COLUMN);

$resumen = [];
$tabla = $tipo === 'tpv' ? 'inventario_tpv' : 'inventario_sims';
$campo_serie = $tipo === 'tpv' ? 'serie' : 'serie_sim';

foreach ($tecnicos as $tecnico) {
    $stmt = $pdo->prepare("SELECT banco, estado, COUNT(*) as total FROM $tabla WHERE tecnico_actual = ? GROUP BY banco, estado");
    $stmt->execute([$tecnico]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($res as $r) {
        $banco = $r['banco'] ?: 'Sin Banco';
        $estado = $r['estado'];
        $resumen[$tecnico][$banco]['Asignados'] = ($resumen[$tecnico][$banco]['Asignados'] ?? 0) + $r['total'];
        if ($estado === 'Disponible' || $estado === 'Asignada') {
            $resumen[$tecnico][$banco]['Disponibles'] = ($resumen[$tecnico][$banco]['Disponibles'] ?? 0) + $r['total'];
        }
        if ($estado === 'Dañado' || $estado === 'Dañada') {
            $resumen[$tecnico][$banco]['Dañados'] = ($resumen[$tecnico][$banco]['Dañados'] ?? 0) + $r['total'];
        }
    }
}
?>

<h2 class="text-xl font-bold mb-4">Resumen de <?= strtoupper($tipo) ?> por Técnico y Banco</h2>

<?php foreach ($resumen as $tecnico => $bancos): ?>
    <div class="mb-4 border rounded p-4 bg-white shadow">
        <h3 class="text-lg font-semibold mb-2">
            <?= $tipo === 'tpv' ? '👷‍♂️' : '📱' ?> <?= htmlspecialchars($tecnico) ?>
        </h3>
        <table class="w-full table-auto border text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left">Banco</th>
                    <th class="px-3 py-2 text-left">Asignados</th>
                    <th class="px-3 py-2 text-left">Disponibles</th>
                    <th class="px-3 py-2 text-left">Dañados</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bancos as $banco => $data): ?>
                    <tr>
                        <td class="px-3 py-1 border-t"><?= htmlspecialchars($banco) ?></td>
                        <td class="px-3 py-1 border-t"><?= $data['Asignados'] ?? 0 ?></td>
                        <td class="px-3 py-1 border-t"><?= $data['Disponibles'] ?? 0 ?></td>
                        <td class="px-3 py-1 border-t"><?= $data['Dañados'] ?? 0 ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endforeach; ?>

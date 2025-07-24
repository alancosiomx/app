<?php
require_once __DIR__ . '/../init.php';

// Obtener técnicos del sistema
$stmt = $pdo->query("SELECT nombre FROM usuarios WHERE roles LIKE '%tecnico%' ORDER BY nombre");
$tecnicos = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Inicializar arrays para resumen
$resumen_tpv = [];
$resumen_sims = [];

foreach ($tecnicos as $tecnico) {
    // --- TPVs por banco ---
    $stmt = $pdo->prepare("SELECT banco, estado, COUNT(*) as total FROM inventario_tpv WHERE tecnico_actual = ? GROUP BY banco, estado");
    $stmt->execute([$tecnico]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($res as $r) {
        $banco = $r['banco'] ?: 'Sin Banco';
        $estado = $r['estado'];
        $resumen_tpv[$tecnico][$banco]['Asignados'] = ($resumen_tpv[$tecnico][$banco]['Asignados'] ?? 0) + $r['total'];
        if ($estado === 'Disponible') {
            $resumen_tpv[$tecnico][$banco]['Disponibles'] = ($resumen_tpv[$tecnico][$banco]['Disponibles'] ?? 0) + $r['total'];
        }
        if ($estado === 'Dañado') {
            $resumen_tpv[$tecnico][$banco]['Dañados'] = ($resumen_tpv[$tecnico][$banco]['Dañados'] ?? 0) + $r['total'];
        }
    }

    // --- SIMs por banco ---
    $stmt = $pdo->prepare("SELECT banco, estado, COUNT(*) as total FROM inventario_sims WHERE tecnico_actual = ? GROUP BY banco, estado");
    $stmt->execute([$tecnico]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($res as $r) {
        $banco = $r['banco'] ?: 'Sin Banco';
        $estado = $r['estado'];
        $resumen_sims[$tecnico][$banco]['Asignados'] = ($resumen_sims[$tecnico][$banco]['Asignados'] ?? 0) + $r['total'];
        if ($estado === 'Disponible') {
            $resumen_sims[$tecnico][$banco]['Disponibles'] = ($resumen_sims[$tecnico][$banco]['Disponibles'] ?? 0) + $r['total'];
        }
        if ($estado === 'Dañada') {
            $resumen_sims[$tecnico][$banco]['Dañados'] = ($resumen_sims[$tecnico][$banco]['Dañados'] ?? 0) + $r['total'];
        }
    }
}

?>

<div class="flex flex-wrap gap-3 mb-6">
    <a href="nuevo_movimiento.php?tipo=tpv" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">➕ Nuevo TPV</a>
    <a href="asignar.php?tipo=tpv" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">📦 Asignar TPV</a>
    <a href="devolver.php?tipo=tpv" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">🔁 Devolver TPV</a>
    <a href="preparar_envio.php?tipo=tpv" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">📬 Preparar envío TPV</a>
    <a href="recibir_danado.php?tipo=tpv" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">⚠️ Recibir TPV dañado</a>
    <span class="mx-2 border-l"></span>
    <a href="nuevo_movimiento.php?tipo=sim" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">➕ Nuevo SIM</a>
    <a href="asignar.php?tipo=sim" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">📦 Asignar SIM</a>
    <a href="devolver.php?tipo=sim" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">🔁 Devolver SIM</a>
    <a href="preparar_envio.php?tipo=sim" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">📬 Preparar envío SIM</a>
    <a href="recibir_danado.php?tipo=sim" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">⚠️ Recibir SIM dañado</a>
</div>

<h2 class="text-xl font-bold mb-4">Resumen de TPVs por Técnico y Banco</h2>
<?php foreach ($resumen_tpv as $tecnico => $bancos): ?>
    <div class="mb-4 border rounded p-4 bg-white shadow">
        <h3 class="text-lg font-semibold mb-2">👷‍♂️ <?= htmlspecialchars($tecnico) ?></h3>
        <table class="w-full table-auto border">
            <thead>
                <tr class="bg-gray-100">
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

<h2 class="text-xl font-bold mt-10 mb-4">Resumen de SIMs por Técnico y Banco</h2>
<?php foreach ($resumen_sims as $tecnico => $bancos): ?>
    <div class="mb-4 border rounded p-4 bg-white shadow">
        <h3 class="text-lg font-semibold mb-2">📱 <?= htmlspecialchars($tecnico) ?></h3>
        <table class="w-full table-auto border">
            <thead>
                <tr class="bg-gray-100">
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

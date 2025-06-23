<?php
require_once __DIR__ . '/../init.php';

try {
    $hoy = date('Y-m-d');
    $mañana = date('Y-m-d', strtotime('+1 day'));

    $sql = "SELECT ticket, afiliacion, comercio, fecha_cita 
            FROM servicios_omnipos 
            WHERE estatus = 'En Ruta' 
            AND fecha_cita IS NOT NULL 
            AND (fecha_cita = :hoy OR fecha_cita = :maniana)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['hoy' => $hoy, 'maniana' => $mañana]);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $citas_hoy = array_filter($citas, fn($c) => $c['fecha_cita'] === $hoy);
    $citas_manana = array_filter($citas, fn($c) => $c['fecha_cita'] === $mañana);

} catch (Exception $e) {
    echo "<div class='bg-red-100 text-red-700 p-3 rounded-md font-bold'>❌ Error al cargar alertas: " . $e->getMessage() . "</div>";
    return;
}
?>

<?php if (!empty($citas_hoy) || !empty($citas_manana)): ?>
<div class="bg-white shadow-md rounded-xl p-4 mb-6 border-l-4 border-blue-500">
    <h2 class="text-lg font-bold mb-2">🔔 Panel de Citas Programadas</h2>

    <?php if (!empty($citas_hoy)): ?>
        <div class="mb-2">
            <h3 class="font-semibold text-red-600">📅 CITA HOY (<?= count($citas_hoy) ?>)</h3>
            <ul class="list-disc pl-5 text-sm text-gray-800">
                <?php foreach ($citas_hoy as $cita): ?>
                    <li><strong><?= $cita['ticket'] ?></strong> – <?= htmlspecialchars($cita['comercio']) ?> (<?= $cita['afiliacion'] ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($citas_manana)): ?>
        <div>
            <h3 class="font-semibold text-orange-500">📅 CITA MAÑANA (<?= count($citas_manana) ?>)</h3>
            <ul class="list-disc pl-5 text-sm text-gray-800">
                <?php foreach ($citas_manana as $cita): ?>
                    <li><strong><?= $cita['ticket'] ?></strong> – <?= htmlspecialchars($cita['comercio']) ?> (<?= $cita['afiliacion'] ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

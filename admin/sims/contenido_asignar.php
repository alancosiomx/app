<?php
require_once __DIR__ . '/constants.php';

$vista_actual = $_GET['vista'] ?? 'asignar';

echo '<div class="tabs mb-4">';
foreach ($tabs_sims as $key => $label) {
    $active = ($vista_actual === $key) ? 'font-bold underline text-blue-600' : 'text-gray-500';
    echo "<a href='index.php?vista=$key' class='mr-4 $active'>$label</a>";
}
echo '</div>';
?>

<h2 class="text-xl font-bold mb-4">👤 Asignar SIMs a Técnico</h2>
<!-- Aquí va el formulario de asignación -->

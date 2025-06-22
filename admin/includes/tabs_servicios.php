<?php
$tab_actual = $_GET['tab'] ?? 'por_asignar';

function tabClass($tab, $actual) {
    return $tab === $actual
        ? 'bg-blue-600 text-white font-semibold px-4 py-2 rounded shadow'
        : 'bg-white text-gray-600 hover:bg-gray-100 px-4 py-2 border border-gray-300 rounded';
}
?>

<div class="flex flex-wrap gap-2 mb-6">
    <a href="?tab=por_asignar" class="<?= tabClass('por_asignar', $tab_actual) ?>">📌 Por Asignar</a>
    <a href="?tab=en_ruta" class="<?= tabClass('en_ruta', $tab_actual) ?>">🚗 En Ruta</a>
    <a href="?tab=concluido" class="<?= tabClass('concluido', $tab_actual) ?>">📁 Histórico</a>
    <a href="?tab=citas" class="<?= tabClass('citas', $tab_actual) ?>">📅 Citas</a>
</div>

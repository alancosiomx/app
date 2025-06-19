<?php
$tab_actual = $_GET['tab'] ?? 'por_asignar';
?>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link <?= $tab_actual === 'por_asignar' ? 'active' : '' ?>" href="?tab=por_asignar">Por Asignar</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab_actual === 'en_ruta' ? 'active' : '' ?>" href="?tab=en_ruta">En Ruta</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab_actual === 'concluido' ? 'active' : '' ?>" href="?tab=concluido">Histórico</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab_actual === 'citas' ? 'active' : '' ?>" href="?tab=citas">Citas</a>
    </li>
</ul>

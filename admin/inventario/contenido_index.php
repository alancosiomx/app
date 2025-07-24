<?php
$vista = $_GET['vista'] ?? 'index';
$tipo = $_GET['tipo'] ?? 'tpv';

if (!in_array($tipo, ['tpv', 'sim'])) {
    echo "<p class='text-red-600'>❌ Tipo inválido.</p>";
    return;
}

if (!in_array($vista, ['index', 'asignar', 'editar'])) {
    echo "<p class='text-red-600'>❌ Vista inválida.</p>";
    return;
}

$archivo = __DIR__ . "/contenido_{$vista}.php";
if (!file_exists($archivo)) {
    echo "<p class='text-red-600'>❌ No se encontró contenido para esta vista.</p>";
    return;
}

require $archivo;

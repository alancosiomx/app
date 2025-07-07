<?php
echo "✅ Inicio<br>";

$init = __DIR__ . '/../../init.php';
echo "Incluyendo: $init<br>";

if (!file_exists($init)) {
    die("❌ init.php no encontrado");
}

require_once $init;

echo "✅ init.php incluido sin errores<br>";

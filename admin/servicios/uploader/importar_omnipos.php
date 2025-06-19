<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/funciones_mapeo.php';
require_once __DIR__ . '/../../layout.php';

$banco = $_POST['banco'] ?? '';
$resultado = '';

switch ($banco) {
    case 'bbva':
        $resultado = mapear_bbva($pdo);
        break;
    case 'banregio':
        $resultado = mapear_banregio($pdo); // puedes implementarlo después
        break;
    case 'azteca':
        $resultado = mapear_azteca($pdo); // igual
        break;
    default:
        $resultado = '❌ Banco no reconocido.';
        break;
}
?>

<div class="container mt-4">
    <h2>Resultado de la importación a OMNIPOS</h2>
    <div class="alert alert-info"><?= nl2br($resultado) ?></div>
    <a href="index.php" class="btn btn-secondary">← Volver al cargador</a>
</div>

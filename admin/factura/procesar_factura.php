<?php
require_once __DIR__ . '/../../init.php';
echo "✓ init cargado<br>";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Método incorrecto";
    exit();
}

echo "✓ POST detectado<br>";

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die("❌ CSRF inválido");
}

echo "✓ Token válido<br>";

$cliente_id = $_POST['cliente_id'] ?? null;
$concepto_ids = $_POST['concepto_id'] ?? [];
$cantidades = $_POST['cantidad'] ?? [];

if (!$cliente_id || empty($concepto_ids)) {
    die("❌ Datos incompletos");
}

echo "✓ Datos completos<br>";

$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    die("❌ Cliente no encontrado");
}

echo "✓ Cliente cargado<br>";

$stmt = $pdo->prepare("SELECT * FROM conceptos_factura WHERE id = ?");
$stmt->execute([$concepto_ids[0]]);
$c = $stmt->fetch();

if (!$c) {
    die("❌ Concepto no válido");
}

echo "✓ Concepto válido<br>";

exit("✅ Todo OK hasta aquí");

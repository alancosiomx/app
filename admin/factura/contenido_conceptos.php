<?php
require_once __DIR__ . '/../../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("❌ Sesión no válida.");
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio_unitario']) ?: 0;

    if (empty($descripcion) || $precio <= 0) {
        $mensaje = "❌ Todos los campos son obligatorios y el precio debe ser mayor a 0.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO conceptos_factura (descripcion, precio_unitario)
            VALUES (:descripcion, :precio)
        ");
        $stmt->execute([
            ':descripcion' => $descripcion,
            ':precio' => $precio
        ]);
        $mensaje = "✅ Concepto guardado correctamente.";
    }
}
?>

<div class="p-6 max-w-xl">
  <h2 class="text-2xl font-bold mb-4">Registrar Nuevo Concepto</h2>

  <?php if (!empty($mensaje)): ?>
    <div class="mb-4 p-3 rounded text-white <?= str_starts_with($mensaje, '✅') ? 'bg-green-600' : 'bg-red-600' ?>">
      <?= $mensaje ?>
    </div>
  <?php endif; ?>

  <form method="POST" class="space-y-4">
    <div>
      <label class="block font-semibold mb-1">Descripción del concepto</label>
      <input type="text" name="descripcion" class="w-full border p-2 rounded" required>
      <p class="text-sm text-gray-500">Ej: "Servicio de paquetería Cancún - Tulum"</p>
    </div>

    <div>
      <label class="block font-semibold mb-1">Precio unitario (MXN)</label>
      <input type="number" name="precio_unitario" step="0.01" class="w-full border p-2 rounded" required>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
      Guardar Concepto
    </button>
  </form>
</div>

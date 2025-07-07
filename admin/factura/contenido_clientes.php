<?php
require_once __DIR__ . '/../../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("<div class='p-4 bg-red-100 text-red-800 rounded'>⚠️ Sesión expirada. Vuelve a iniciar sesión.</div>");
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO clientes (
                razon_social, rfc, email, uso_cfdi, regimen_fiscal, codigo_postal
            ) VALUES (
                :razon_social, :rfc, :email, :uso_cfdi, :regimen_fiscal, :codigo_postal
            )
        ");

        $stmt->execute([
            ':razon_social'     => $_POST['razon_social'],
            ':rfc'              => strtoupper($_POST['rfc']),
            ':email'            => $_POST['email'],
            ':uso_cfdi'         => $_POST['uso_cfdi'],
            ':regimen_fiscal'   => $_POST['regimen_fiscal'],
            ':codigo_postal'    => $_POST['codigo_postal']
        ]);

        $mensaje = "✅ Cliente registrado correctamente.";
    } catch (PDOException $e) {
        $mensaje = "❌ Error al registrar cliente: " . $e->getMessage();
    }
}
?>

<div class="p-6 max-w-3xl">
  <h2 class="text-2xl font-bold mb-4">Registrar Nuevo Cliente</h2>

  <?php if (!empty($mensaje)): ?>
    <div class="mb-4 p-3 rounded text-white <?= str_starts_with($mensaje, '✅') ? 'bg-green-600' : 'bg-red-600' ?>">
      <?= $mensaje ?>
    </div>
  <?php endif; ?>

  <form method="POST" class="space-y-4">
    <div>
      <label class="block font-semibold mb-1">Razón Social</label>
      <input type="text" name="razon_social" class="w-full border p-2 rounded" required>
    </div>
    <div>
      <label class="block font-semibold mb-1">RFC</label>
      <input type="text" name="rfc" maxlength="13" class="w-full border p-2 rounded uppercase" required>
    </div>
    <div>
      <label class="block font-semibold mb-1">Correo Electrónico</label>
      <input type="email" name="email" class="w-full border p-2 rounded" required>
    </div>
    <div>
      <label class="block font-semibold mb-1">Uso CFDI</label>
      <select name="uso_cfdi" class="w-full border p-2 rounded" required>
        <option value="G03">G03 - Gastos en general</option>
        <option value="G01">G01 - Adquisición de mercancías</option>
      </select>
    </div>
    <div>
      <label class="block font-semibold mb-1">Régimen Fiscal</label>
      <select name="regimen_fiscal" class="w-full border p-2 rounded" required>
        <option value="601">601 - General de Ley Personas Morales</option>
        <option value="603">603 - Personas Morales con Fines no Lucrativos</option>
        <option value="605">605 - Sueldos y Salarios e Ingresos Asimilados</option>
        <option value="612">612 - Personas Físicas con Actividades Empresariales</option>
      </select>
    </div>
    <div>
      <label class="block font-semibold mb-1">Código Postal</label>
      <input type="text" name="codigo_postal" class="w-full border p-2 rounded" required>
    </div>
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
      Guardar Cliente
    </button>
  </form>
</div>

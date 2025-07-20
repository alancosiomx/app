<?php
require_once __DIR__ . '/../init.php';

$stmt = $pdo->query("SELECT nombre FROM usuarios WHERE activo = 1 ORDER BY nombre ASC");
$tecnicos = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<h2 class="text-2xl font-bold mb-4">📤 Subir archivos PDF a técnicos</h2>

<form action="guardar_pdf.php" method="POST" enctype="multipart/form-data" class="space-y-4">
  <div>
    <label class="block text-sm font-medium mb-1">Selecciona Técnico:</label>
    <select name="idc" required class="w-full border p-2 rounded">
      <option value="">-- Elige --</option>
      <?php foreach ($tecnicos as $nombre): ?>
        <option value="<?= htmlspecialchars($nombre) ?>"><?= htmlspecialchars($nombre) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Selecciona PDF(s):</label>
    <input type="file" name="archivos[]" accept="application/pdf" multiple required class="w-full">
  </div>

  <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
    Subir Archivos
  </button>
</form>

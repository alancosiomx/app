<?php
// app/admin/sims/contenido_asignar.php
require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/../../config.php';

$vista_actual = $_GET['vista'] ?? 'asignar';

echo '<div class="tabs mb-4">';
foreach ($tabs_sims as $key => $label) {
    $active = ($vista_actual === $key) ? 'font-bold underline text-blue-600' : 'text-gray-500';
    echo "<a href='index.php?vista=$key' class='mr-4 $active'>$label</a>";
}
echo '</div>';

// Obtener técnicos disponibles
$tecnicos = [];
$query = $conn->query("SELECT nombre FROM usuarios WHERE roles LIKE '%tecnico%' AND activo = 1 ORDER BY nombre ASC");
while ($row = $query->fetch_assoc()) {
    $tecnicos[] = $row['nombre'];
}
?>

<h2 class="text-xl font-bold mb-4">👤 Asignar SIMs a Técnico</h2>

<form method="POST" id="formAsignarSIM" class="space-y-4 max-w-xl">
  <div>
    <label class="block font-medium">Técnico:</label>
    <select name="tecnico" required class="w-full border rounded px-3 py-2">
      <option value="">-- Selecciona un técnico --</option>
      <?php foreach ($tecnicos as $tecnico): ?>
        <option value="<?= htmlspecialchars($tecnico) ?>"><?= htmlspecialchars($tecnico) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div>
    <label class="block font-medium">Series a Asignar (una por línea):</label>
    <textarea name="series[]" rows="6" placeholder="89014103211118510720\n89014103211118510721" class="w-full border rounded px-3 py-2"></textarea>
  </div>

  <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Asignar</button>
</form>

<div id="resultadoAsignacion" class="mt-4 text-sm"></div>

<script>
document.getElementById('formAsignarSIM').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = new FormData(this);
  const response = await fetch('backend_asignar.php', { method: 'POST', body: form });
  const data = await response.json();
  document.getElementById('resultadoAsignacion').innerHTML = data.resultado.map(r => `<p>${r}</p>`).join('');
});
</script>

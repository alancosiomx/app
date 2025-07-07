<?php
// app/admin/sims/contenido_alta.php
require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/../../config.php';

$vista_actual = $_GET['vista'] ?? 'alta';

echo '<div class="tabs mb-4">';
foreach ($tabs_sims as $key => $label) {
    $active = ($vista_actual === $key) ? 'font-bold underline text-blue-600' : 'text-gray-500';
    echo "<a href='index.php?vista=$key' class='mr-4 $active'>$label</a>";
}
echo '</div>';
?>

<h2 class="text-xl font-bold mb-4">🆕 Alta de SIMs</h2>

<form method="POST" id="formAltaSIM" class="space-y-4 max-w-xl">
  <div>
    <label class="block font-medium">Marca:</label>
    <input type="text" name="marca" required class="w-full border rounded px-3 py-2">
  </div>

  <div>
    <label class="block font-medium">Banco (opcional):</label>
    <input type="text" name="banco" class="w-full border rounded px-3 py-2">
  </div>

  <div>
    <label class="block font-medium">Tipo SIM:</label>
    <select name="tipo_sim" class="w-full border rounded px-3 py-2">
      <option value="">-- Seleccionar --</option>
      <option value="Normal">Normal</option>
      <option value="Nano">Nano</option>
      <option value="M2M">M2M</option>
    </select>
  </div>

  <div>
    <label class="block font-medium">Series (una por línea):</label>
    <textarea name="series[]" rows="6" placeholder="89014103211118510720\n89014103211118510721" class="w-full border rounded px-3 py-2"></textarea>
  </div>

  <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Registrar SIMs</button>
</form>

<div id="resultadoRegistro" class="mt-4 text-sm"></div>

<script>
document.getElementById('formAltaSIM').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = new FormData(this);
  const response = await fetch('backend_alta.php', { method: 'POST', body: form });
  const data = await response.json();
  document.getElementById('resultadoRegistro').innerHTML = data.resultado.map(r => `<p>${r}</p>`).join('');
});
</script>

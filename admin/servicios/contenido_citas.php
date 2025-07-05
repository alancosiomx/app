<?php include __DIR__ . '/../includes/tabs_servicios.php'; ?>

<h2 class="text-xl font-bold mb-4">📅 Programar Cita</h2>

<form method="post" action="agendar_cita.php" class="bg-white shadow rounded-xl p-6 space-y-4 max-w-xl">
  <div>
    <label for="tickets" class="block text-sm font-medium text-gray-700">Tickets (separados por coma):</label>
    <input type="text" name="tickets" id="tickets" required placeholder="Ej: 12345678, 87654321"
           class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm p-2">
  </div>

  <div>
    <label for="fecha_cita" class="block text-sm font-medium text-gray-700">Fecha de la cita:</label>
    <input type="date" name="fecha_cita" id="fecha_cita" required
           class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm p-2">
  </div>

  <div class="text-right">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
      Agendar Cita
    </button>
  </div>
</form>

<?php if (isset($_SESSION['mensaje'])): ?>
  <div class="bg-green-100 text-green-800 px-4 py-2 rounded mt-4"><?= $_SESSION['mensaje'] ?></div>
  <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

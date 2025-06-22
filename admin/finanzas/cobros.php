<div class="mb-4 border-b border-gray-200">
  <nav class="flex flex-wrap gap-2 text-sm font-medium text-gray-500" aria-label="Tabs">
    <?php foreach (TABS_FINANZAS as $clave => $titulo): ?>
      <a href="?vista=<?= $clave ?>"
         class="px-4 py-2 rounded-xl <?= ($_GET['vista'] ?? 'cobros') === $clave ? 'bg-blue-600 text-white' : 'hover:text-blue-700 bg-gray-100' ?>">
        <?= $titulo ?>
      </a>
    <?php endforeach; ?>
  </nav>
</div>

<div class="bg-white shadow p-6 rounded-2xl">
  <h1 class="text-xl font-bold mb-4 text-blue-700">💳 Reporte de Cobros</h1>

  <!-- Filtros -->
  <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <input type="hidden" name="vista" value="cobros">

    <div>
      <label class="block text-sm font-medium text-gray-600">Desde</label>
      <input type="date" name="desde" value="<?= $_GET['desde'] ?? '' ?>" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-600">Hasta</label>
      <input type="date" name="hasta" value="<?= $_GET['hasta'] ?? '' ?>" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-600">Técnico</label>
      <select name="tecnico" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        <option value="">Todos</option>
        <?php
        $tecnicos = $pdo->query("SELECT DISTINCT idc FROM servicios_omnipos WHERE idc IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tecnicos as $t):
          $selected = ($_GET['tecnico'] ?? '') === $t ? 'selected' : '';
          echo "<option value='$t' $selected>$t</option>";
        endforeach;
        ?>
      </select>
    </div>

    <div class="flex items-end">
      <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
        Generar Reporte
      </button>
    </div>
  </form>

  <!-- Aquí va la tabla (la armamos después) -->
  <div class="bg-gray-50 text-gray-600 p-4 rounded-lg border border-gray-200">
    <p class="italic">Aquí se mostrará el resultado del filtro: servicios con conclusión y visitas realizadas dentro del rango.</p>
  </div>
</div>

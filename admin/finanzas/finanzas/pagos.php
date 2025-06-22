<?php require_once __DIR__ . '/../../config/constants.php'; ?>

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
  <h1 class="text-xl font-bold mb-2 text-blue-700"><?= TABS_FINANZAS['pagos'] ?></h1>
  <p class="text-gray-500">Aquí irá el contenido del tab: <strong><?= TABS_FINANZAS['pagos'] ?></strong>.</p>
</div>

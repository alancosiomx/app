<?php
$stmt = $pdo->query("SELECT estatus, COUNT(*) as total FROM servicios_omnipos GROUP BY estatus");
$datos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$porAsignar = $datos['Por Asignar'] ?? 0;
$enRuta = $datos['En Ruta'] ?? 0;
$historico = $datos['Histórico'] ?? 0;
$total = $porAsignar + $enRuta + $historico;
?>

<div class="grid grid-cols-3 gap-4 mb-6">
  <div class="bg-white shadow p-4 rounded-lg text-center">
    <div class="text-gray-500 text-sm">Servicios Totales</div>
    <div class="text-2xl font-bold text-blue-600"><?= $total ?></div>
  </div>
  <div class="bg-white shadow p-4 rounded-lg text-center">
    <div class="text-gray-500 text-sm">Por Asignar</div>
    <div class="text-2xl font-bold text-yellow-600"><?= $porAsignar ?></div>
  </div>
  <div class="bg-white shadow p-4 rounded-lg text-center">
    <div class="text-gray-500 text-sm">En Ruta</div>
    <div class="text-2xl font-bold text-orange-600"><?= $enRuta ?></div>
  </div>
  <div class="bg-white shadow p-4 rounded-lg text-center">
    <div class="text-gray-500 text-sm">Histórico</div>
    <div class="text-2xl font-bold text-green-600"><?= $historico ?></div>
  </div>
</div>

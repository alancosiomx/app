<?php
// Obtener datos
$total = $pdo->query("SELECT COUNT(*) FROM servicios_omnipos")->fetchColumn();
$porAsignar = $pdo->query("SELECT COUNT(*) FROM servicios_omnipos WHERE estatus = 'Por Asignar'")->fetchColumn();
$enRuta = $pdo->query("SELECT COUNT(*) FROM servicios_omnipos WHERE estatus = 'En Ruta'")->fetchColumn();
$historico = $pdo->query("SELECT COUNT(*) FROM servicios_omnipos WHERE estatus = 'Histórico'")->fetchColumn();
?>

<h2 class="text-xl font-bold text-gray-800 mb-4">📊 Resumen General de Servicios</h2>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
  <div class="bg-white rounded-xl shadow p-4 text-center">
    <div class="text-sm text-gray-500">Total</div>
    <div class="text-2xl font-bold text-gray-800"><?= $total ?></div>
  </div>
  <div class="bg-white rounded-xl shadow p-4 text-center">
    <div class="text-sm text-yellow-600">Por Asignar</div>
    <div class="text-2xl font-bold text-yellow-600"><?= $porAsignar ?></div>
  </div>
  <div class="bg-white rounded-xl shadow p-4 text-center">
    <div class="text-sm text-blue-600">En Ruta</div>
    <div class="text-2xl font-bold text-blue-600"><?= $enRuta ?></div>
  </div>
  <div class="bg-white rounded-xl shadow p-4 text-center">
    <div class="text-sm text-green-600">Histórico</div>
    <div class="text-2xl font-bold text-green-600"><?= $historico ?></div>
  </div>
</div>

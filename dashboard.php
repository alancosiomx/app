// dashboard.php
<?php
require 'auth_check.php'; // Verifica sesión activa

// Obtener estadísticas
$servicios_pendientes = $pdo->query("SELECT COUNT(*) FROM servicios WHERE estado = 'por_asignar'")->fetchColumn();
$servicios_en_ruta = $pdo->query("SELECT COUNT(*) FROM servicios WHERE estado = 'en_ruta'")->fetchColumn();
?>

<!-- HTML: Tarjetas con métricas -->
<div class="row">
  <div class="col-md-4">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <h5>Por Asignar</h5>
        <h2><?= $servicios_pendientes ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-warning text-white">
      <div class="card-body">
        <h5>En Ruta</h5>
        <h2><?= $servicios_en_ruta ?></h2>
      </div>
    </div>
  </div>
</div>
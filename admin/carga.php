<?php
require_once __DIR__ . '/init.php';
if (!defined('HEAD') || !defined('MENU') || !defined('FOOT')) {
    require_once dirname(__DIR__) . '/config.php';
}
require HEAD;
require MENU;
?>

<div class="main-content">

<div class="container py-5">
  <h2 class="text-center mb-5">Carga de Servicios</h2>
  
  <div class="row justify-content-center g-4">
    
    <!-- Azteca -->
    <div class="col-md-4">
      <div class="card border-success shadow-sm h-100">
        <div class="card-body text-center">
          <div class="display-3">🟢</div>
          <h5 class="card-title mt-3 mb-4 text-success">Azteca</h5>
          <a href="carga_azteca.php" class="btn btn-success w-100">Cargar Servicios</a>
        </div>
      </div>
    </div>

    <!-- BBVA -->
    <div class="col-md-4">
      <div class="card border-primary shadow-sm h-100">
        <div class="card-body text-center">
          <div class="display-3">🔵</div>
          <h5 class="card-title mt-3 mb-4 text-primary">BBVA</h5>
          <a href="carga_bbva.php" class="btn btn-primary w-100">Cargar Servicios</a>
        </div>
      </div>
    </div>

    <!-- Banregio -->
    <div class="col-md-4">
      <div class="card border-warning shadow-sm h-100">
        <div class="card-body text-center">
          <div class="display-3">🟠</div>
          <h5 class="card-title mt-3 mb-4 text-warning">Banregio</h5>
          <a href="carga_banregio.php" class="btn btn-warning w-100 text-white">Cargar Servicios</a>
        </div>
      </div>
    </div>

  </div>
</div>

<?php require FOOT; ?>

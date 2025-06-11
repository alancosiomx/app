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

  <div class="row justify-content-center">
    <div class="col-md-8">
      
      <div class="card shadow-lg">
        <div class="card-body">
          
          <h3 class="card-title text-center mb-4 text-primary">🔵 Cargar Servicios BBVA</h3>
          
          <form action="importar_bbva.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="archivo_csv" class="form-label">Selecciona archivo CSV o Excel (.xlsx)</label>
              <input 
                class="form-control" 
                type="file" 
                id="archivo_csv" 
                name="archivo_csv" 
                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 
                required
              >
              <div class="form-text">Acepta archivos .csv o .xlsx</div>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg">Subir y Cargar</button>
            </div>
          </form>

          <div class="mt-4 text-center">
            <a href="carga.php" class="btn btn-link">⬅️ Volver al Menú de Carga</a>
          </div>

        </div>
      </div>

    </div>
  </div>

</div>

<?php require FOOT; ?>

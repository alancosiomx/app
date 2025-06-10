<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carga de Servicios - OMNIPOS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

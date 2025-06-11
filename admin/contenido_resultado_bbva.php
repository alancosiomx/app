<?php
$insertados = $_SESSION['resultado_bbva']['insertados'] ?? 0;
$errores    = $_SESSION['resultado_bbva']['errores'] ?? 0;
unset($_SESSION['resultado_bbva']);
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <div class="card shadow-lg">
        <div class="card-body text-center">
          
          <h3 class="card-title mb-4 text-primary">🔵 Resultado de la Carga BBVA</h3>

          <div class="alert alert-success">
            ✅ <strong><?= $insertados ?></strong> registros insertados correctamente.
          </div>

          <?php if ($errores > 0): ?>
          <div class="alert alert-danger">
            ❌ <strong><?= $errores ?></strong> errores al intentar insertar registros.
          </div>
          <?php endif; ?>

          <div class="mt-4 d-flex flex-column gap-2">
            <a href="carga_bbva.php" class="btn btn-primary">⬅️ Subir otro archivo</a>
            <a href="carga.php" class="btn btn-secondary">🏠 Volver al Menú de Carga</a>
          </div>

          <form method="POST" action="migrar_a_omnipos.php" class="mt-4">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <button type="submit" class="btn btn-success btn-lg">🚀 Copiar a Servicios OMNIPOS</button>
          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<div class="container py-5">

  <div class="row justify-content-center">
    <div class="col-md-8">

      <div class="card shadow-lg">
        <div class="card-body text-center">
          
          <h3 class="card-title mb-4 text-primary">🟢 Resultado de la Migración</h3>
          
          <div class="alert alert-success">
            ✅ <strong><?php echo $insertados; ?></strong> registros migrados a Servicios OMNIPOS.
          </div>

          <?php if ($errores > 0): ?>
          <div class="alert alert-danger">
            ❌ Hubo <strong><?php echo $errores; ?></strong> errores durante la migración.
          </div>
          <?php endif; ?>

          <div class="mt-4">
            <a href="carga_bbva.php" class="btn btn-primary">⬅️ Volver al Menú de Carga</a>
          </div>

        </div>
      </div>

    </div>
  </div>

</div>

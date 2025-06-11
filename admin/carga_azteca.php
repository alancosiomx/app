<?php
require_once __DIR__ . '/init.php';
if (!defined('INIT')) {
    require_once dirname(__DIR__) . '/config.php';
}
require INIT;
?>

<div class="main-content">
    <h2>Carga Servicios Azteca</h2>
    <form action="importar_azteca.php" method="post" enctype="multipart/form-data">
      <input type="file" name="archivo_csv" required>
      <button type="submit">Subir y Cargar</button>
    </form>
</div>



<?php
require_once __DIR__ . '/../config.php';
session_start();

$idc = $_SESSION['usuario_nombre'] ?? '';

\$dirPDF = __DIR__ . "/../../pdfs_hs/\$idc";
if (!is_dir(\$dirPDF)) {
  echo "<p class='text-red-600'>Aún no se han generado hojas de servicio.</p>";
  exit;
}

\$archivos = glob("\$dirPDF/*.pdf");

?>

<h2 class="text-xl font-bold mb-4">📥 Hojas de Servicio Generadas</h2>

<ul class="list-disc pl-6 space-y-2">
  <?php foreach (\$archivos as \$pdf): 
    \$nombreArchivo = basename(\$pdf);
  ?>
    <li>
      <a href="/pdfs_hs/<?= urlencode(\$idc) ?>/<?= urlencode(\$nombreArchivo) ?>" class="text-blue-600 underline" target="_blank">
        <?= htmlspecialchars(\$nombreArchivo) ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

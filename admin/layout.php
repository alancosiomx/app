<?php
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/menu.php';
?>

<div class="main-content">
    <?php
    if (isset($contenido) && file_exists($contenido)) {
        include $contenido;
    } else {
        echo '<div class="alert alert-danger">❌ Error: contenido no encontrado.</div>';
    }
    ?>
</div>

<?php require_once __DIR__ . '/includes/foot.php'; ?>

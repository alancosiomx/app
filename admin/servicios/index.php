<?php
require_once __DIR__ . '/../init.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login.php");
    exit();
}

$tab = $_GET['tab'] ?? 'por_asignar';

// Determina qué archivo incluir según el tab
$contenido = match($tab) {
    'en_ruta'    => __DIR__ . '/contenido_en_ruta.php',
    'concluido'  => __DIR__ . '/contenido_concluido.php',
    'citas'      => __DIR__ . '/contenido_citas.php',
    default      => __DIR__ . '/contenido_por_asignar.php'
};
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php include __DIR__ . '/../includes/tabs_servicios.php'; ?>
<?php include $contenido; ?>

<!-- Contenedor del modal que se inyectará dinámicamente -->
<div id="modal-container"></div>

<!-- Script exclusivo para el detalle 🔍 -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.body.addEventListener('click', function(e) {
    const btn = e.target.closest('.ver-detalle');
    if (!btn) return;

    e.preventDefault();
    const ticket = btn.dataset.ticket;

    fetch('detalle_servicio.php?ticket=' + encodeURIComponent(ticket))
      .then(res => res.text())
      .then(html => {
        const anterior = document.getElementById('modalDetalleServicio');
        if (anterior) anterior.remove();

        document.getElementById('modal-container').innerHTML = html;

        const modal = new bootstrap.Modal(document.getElementById('modalDetalleServicio'));
        modal.show();
      })
      .catch(err => {
        alert('Error al cargar el detalle del servicio.');
        console.error(err);
      });
  });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

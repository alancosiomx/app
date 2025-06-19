</div> <!-- cierra content-wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS para carga dinámica de modelos según fabricante -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fabricante = document.getElementById('fabricante');
    const modelo = document.getElementById('modelo');

    if (fabricante && modelo) {
        fabricante.addEventListener('change', function () {
            const fabricanteId = this.value;
            modelo.innerHTML = '<option value="">Cargando modelos...</option>';

            fetch(`modelos.php?fabricante_id=${fabricanteId}`)
                .then(response => response.json())
                .then(data => {
                    modelo.innerHTML = '<option value="">-- Selecciona modelo --</option>';
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.nombre;
                        modelo.appendChild(opt);
                    });
                });
        });
    }
});
</script>

</body>
</html>

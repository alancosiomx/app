<h2>📦 Inventario de SIMs</h2>
<table id="tabla_sims" class="display nowrap" style="width:100%">
    <thead>
        <tr>
            <th>Serie SIM</th>
            <th>Marca</th>
            <th>Banco</th>
            <th>Estado</th>
            <th>Técnico</th>
            <th>Fecha Entrada</th>
            <th>Días Sin Movimiento</th>
            <th>Acciones</th>
        </tr>
    </thead>
</table>

<script>
$(document).ready(function() {
    $('#tabla_sims').DataTable({
        ajax: 'sims/backend_inventario.php',
        columns: [ /* columnas y formatos */ ]
    });
});
</script>

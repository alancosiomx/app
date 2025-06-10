jQuery(document).ready(function($) {
    $('#cbi-services-table').DataTable({
        scrollX: true, // Desplazamiento horizontal
        scrollY: '50vh', // Desplazamiento vertical
        paging: true, // Paginación
        searching: true, // Filtro dinámico
        order: [[1, 'asc']], // Ordenar por la segunda columna (por defecto)
        columnDefs: [
            { orderable: false, targets: 0 } // Evitar ordenar por la columna de checkboxes
        ]
    });
});

<?php
// Definir una constante para la tabla completa de servicios
if (!defined('CBI_TABLA_SERVICIOS_COMPLETA')) {
    define('CBI_TABLA_SERVICIOS_COMPLETA', '
        <table class="tabla-servicios">
            <thead>
                <tr>
                    <th>Banco</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha Límite</th>
                    <th>Ticket</th>
                    <th>Afiliación</th>
                    <th>Comercio</th>
                    <th>Domicilio</th>
                    <th>Colonia</th>
                    <th>Ciudad</th>
                    <th>CP</th>
                    <th>Servicio</th>
                    <th>Cantidad Insumos</th>
                    <th>Teléfono Contacto 1</th>
                    <th>Referencia</th>
                    <th>Horario</th>
                    <th>Comentarios</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se generarán las filas dinámicamente -->
            </tbody>
        </table>
    ');
}

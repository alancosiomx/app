<?php
// =============================================
// permisos.php - Control de acceso por rol
// =============================================

/**
 * Verifica si el usuario autenticado tiene permiso para realizar una acción
 *
 * @param string $accion Nombre de la acción (clave de la matriz)
 * @return bool Verdadero si tiene permiso, falso si no
 */
function tienePermiso(string $accion): bool {
    if (empty($_SESSION['usuario_roles'])) return false;

    $matriz = [
        // Dashboard
        'ver_dashboard' => ['admin', 'coordinador', 'finanzas'],

        // Servicios
        'ver_servicios_propios' => ['admin', 'coordinador', 'idc'],
        'asignar_servicios' => ['admin', 'coordinador'],
        'generar_hs' => ['admin', 'coordinador', 'idc'],

        // Carga
        'subir_csv' => ['admin', 'coordinador'],
        'mapear_csv' => ['admin'],
        'ver_historial_cargas' => ['admin', 'coordinador'],

        // Inventario
        'ver_inventario' => ['admin', 'coordinador', 'idc', 'finanzas'],
        'registrar_inventario' => ['admin', 'coordinador'],
        'asignar_inventario' => ['admin', 'coordinador'],

        // Viáticos y Finanzas
        'ver_viaticos' => ['admin', 'coordinador', 'finanzas'],
        'aprobar_viaticos' => ['admin', 'coordinador'],
        'generar_reporte_cobros' => ['admin', 'finanzas'],
        'configurar_precios' => ['admin', 'finanzas'],

        // Configuración
        'ver_matriz_permisos' => ['admin'],
    ];

    foreach ($_SESSION['usuario_roles'] as $rol) {
        if (in_array($rol, $matriz[$accion] ?? [])) {
            return true;
        }
    }
    return false;
}

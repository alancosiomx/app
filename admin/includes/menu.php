<?php
$roles = $_SESSION['usuario_roles'] ?? []; // 'admin', 'idc', 'coordinador', 'finanzas'
            <?php if(in_array('admin', $roles) || in_array('finanzas', $roles)): ?>
            <?php if(in_array('admin', $roles) || in_array('coordinador', $roles)): ?>
            <?php if(in_array('admin', $roles)): ?>

            <?php if(in_array('admin', $roles)): ?>
    <div class="flex items-center justify-center h-16 border-b">
        <h1 class="text-2xl font-bold">OMNIPOS</h1>
    </div>
    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-2 px-4">
            <li>
                <a href="dashboard.php" class="flex items-center p-2 rounded hover:bg-gray-100">
                    <span class="material-icons mr-3">dashboard</span> Dashboard
                </a>
            </li>

            <!-- Servicios -->
            <li class="mt-4 text-xs text-gray-500 uppercase">Servicios</li>
            <li><a href="por_asignar.php" class="flex items-center p-2 rounded hover:bg-gray-100">Por Asignar</a></li>
            <li><a href="en_ruta.php" class="flex items-center p-2 rounded hover:bg-gray-100">En Ruta</a></li>
            <li><a href="concluido.php" class="flex items-center p-2 rounded hover:bg-gray-100">Concluido</a></li>
            <li><a href="agendar_cita.php" class="flex items-center p-2 rounded hover:bg-gray-100">Agendar Cita</a></li>
            <li><a href="generar_hs.php" class="flex items-center p-2 rounded hover:bg-gray-100">Hoja de Servicio</a></li>
            <li><a href="pdf_generator.php" class="flex items-center p-2 rounded hover:bg-gray-100">PDF Generador</a></li>

            <!-- Inventario -->
            <li class="mt-4 text-xs text-gray-500 uppercase">Inventario</li>
            <li><a href="registrar.php" class="flex items-center p-2 rounded hover:bg-gray-100">Registro de Equipos</a></li>
            <li><a href="asignar.php" class="flex items-center p-2 rounded hover:bg-gray-100">Asignar a T¨¦cnicos</a></li>
            <li><a href="movimientos.php" class="flex items-center p-2 rounded hover:bg-gray-100">Movimientos</a></li>
            <li><a href="log_inventario.php" class="flex items-center p-2 rounded hover:bg-gray-100">Log de Inventario</a></li>

            <!-- Finanzas -->
            <?php if($rol == 'admin' || $rol == 'finanzas'): ?>
            <li class="mt-4 text-xs text-gray-500 uppercase">Finanzas</li>
            <li><a href="reporte_cobros.php" class="flex items-center p-2 rounded hover:bg-gray-100">Reporte de Cobros</a></li>
            <li><a href="pagos_tecnico.php" class="flex items-center p-2 rounded hover:bg-gray-100">Pagos por T¨¦cnico</a></li>
            <li><a href="viaticos.php" class="flex items-center p-2 rounded hover:bg-gray-100">Vi¨¢ticos</a></li>
            <li><a href="historial_pagos.php" class="flex items-center p-2 rounded hover:bg-gray-100">Historial de Pagos</a></li>
            <?php endif; ?>

            <!-- Carga de Archivos -->
            <?php if($rol == 'admin' || $rol == 'coordinador'): ?>
            <li class="mt-4 text-xs text-gray-500 uppercase">Carga</li>
            <li><a href="cargar_bbva.php" class="flex items-center p-2 rounded hover:bg-gray-100">Cargar BBVA</a></li>
            <li><a href="cargar_banregio.php" class="flex items-center p-2 rounded hover:bg-gray-100">Cargar Banregio</a></li>
            <li><a href="cargar_azteca.php" class="flex items-center p-2 rounded hover:bg-gray-100">Cargar Azteca</a></li>
            <li><a href="historial_cargas.php" class="flex items-center p-2 rounded hover:bg-gray-100">Historial de Cargas</a></li>
            <?php endif; ?>

            <!-- Configuraci¨®n -->
            <?php if($rol == 'admin'): ?>
            <li class="mt-4 text-xs text-gray-500 uppercase">Configuraci¨®n</li>
            <li><a href="campos_mapeo.php" class="flex items-center p-2 rounded hover:bg-gray-100">Campos de Mapeo</a></li>
            <li><a href="opciones_globales.php" class="flex items-center p-2 rounded hover:bg-gray-100">Opciones Globales</a></li>
            <li><a href="roles_permisos.php" class="flex items-center p-2 rounded hover:bg-gray-100">Roles y Permisos</a></li>
            <li><a href="bancos.php" class="flex items-center p-2 rounded hover:bg-gray-100">Bancos</a></li>
            <li><a href="modelos_tpv.php" class="flex items-center p-2 rounded hover:bg-gray-100">Modelos TPV</a></li>
            <li><a href="precios_idc.php" class="flex items-center p-2 rounded hover:bg-gray-100">Precios IDC</a></li>
            <?php endif; ?>

            <!-- Usuarios -->
            <?php if($rol == 'admin'): ?>
            <li class="mt-4 text-xs text-gray-500 uppercase">Usuarios</li>
            <li><a href="usuarios.php" class="flex items-center p-2 rounded hover:bg-gray-100">Gesti¨®n de Usuarios</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>

<!-- Main content debe tener margen izquierdo -->
<div class="ml-64">
    <!-- Aqu¨ª tu contenido principal -->
</div>

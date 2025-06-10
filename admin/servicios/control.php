<?php
require '../auth.php';
require '../config.php';
require '../includes/header.php';
require '../includes/menu.php';
?>

<div class="main-content">
    <h3>Control de Servicios</h3>
    <ul class="nav nav-tabs mt-3" id="tabControlServicios" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="por-asignar-tab" data-bs-toggle="tab" data-bs-target="#por-asignar" type="button" role="tab">Por Asignar</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="en-ruta-tab" data-bs-toggle="tab" data-bs-target="#en-ruta" type="button" role="tab">En Ruta</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="programar-tab" data-bs-toggle="tab" data-bs-target="#programar" type="button" role="tab">Programar Cita</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="atendido-tab" data-bs-toggle="tab" data-bs-target="#atendido" type="button" role="tab">Atendido</button>
        </li>
    </ul>

    <div class="tab-content pt-3" id="tabContentServicios">
        <!-- POR ASIGNAR -->
        <div class="tab-pane fade show active" id="por-asignar" role="tabpanel">
            <?php include 'tabs/por_asignar.php'; ?>
        </div>

        <!-- EN RUTA -->
        <div class="tab-pane fade" id="en-ruta" role="tabpanel">
            <?php include 'tabs/en_ruta.php'; ?>
        </div>

        <!-- PROGRAMAR CITA -->
        <div class="tab-pane fade" id="programar" role="tabpanel">
            <?php include 'tabs/programar_cita.php'; ?>
        </div>

        <!-- ATENDIDO -->
        <div class="tab-pane fade" id="atendido" role="tabpanel">
            <?php include 'tabs/atendido.php'; ?>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>

<?php
// app/admin/finanzas/index.php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/constants.php'; // << Asegura que se carguen las tabs

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

$vista = $_GET['vista'] ?? 'cobros';

$contenido = match ($vista) {
  'pagos'     => __DIR__ . '/contenido_pagos.php',
  'viaticos'  => __DIR__ . '/contenido_viaticos.php',
  'historial' => __DIR__ . '/contenido_historial.php',
  'precios'   => __DIR__ . '/contenido_precios.php',
  default     => __DIR__ . '/contenido_cobros.php'
};

require_once __DIR__ . '/../layout.php';

<?php
// app/admin/facturacion/index.php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/tabs.php'; // si tienes tabs tipo constants.php

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$vista = $_GET['vista'] ?? 'nueva';

$contenido = match ($vista) {
  'nueva'     => __DIR__ . '/contenido_nueva.php',
  'clientes'  => __DIR__ . '/contenido_clientes.php',
  'historial' => __DIR__ . '/contenido_historial.php', // 🟢 Aquí se carga bien
  default     => __DIR__ . '/contenido_nueva.php'
};

require_once __DIR__ . '/../layout.php'; // 🟢 Asegura que use el layout completo

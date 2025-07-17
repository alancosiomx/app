<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
session_start();

if (!isset($_SESSION['usuario_username'])) {
    header("Location: /login.php");
    exit;
}

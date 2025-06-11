<?php
require_once dirname(__DIR__, 2) . '/admin/init.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

if (!defined('HEAD') || !defined('MENU') || !defined('FOOT')) {
    require_once dirname(__DIR__, 2) . '/config.php';
}
require HEAD;
require MENU;
?>

<div class="main-content">
<style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            padding: 30px;
        }
        .container {
            background: #fff;
            border-radius: 8px;
            padding: 25px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            text-align: center;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
            background: #f9f9f9;
            padding: 12px;
            border-left: 4px solid #007bff;
            border-radius: 5px;
        }
        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
</style>
<div class="container">
    <h2>⚙️ Ajustes del Sistema</h2>
    <ul>
        <li><a href="bancos.php">🏦 Gestión de Bancos</a></li>
        <li><a href="tecnicos.php">👨‍🔧 Gestión de Técnicos</a></li>
        <li><a href="tpvs.php">📟 Modelos y Fabricantes TPV</a></li>
        <li><a href="roles.php">🛡️ Roles y Permisos</a></li>
        <li><a href="servicios.php">📋 Tipos de Servicios</a></li>
    </ul>
</div>

</div>
<?php require FOOT; ?>

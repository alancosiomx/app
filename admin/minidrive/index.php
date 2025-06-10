<?php
$dir = __DIR__;
$archivos = array_diff(scandir($dir), ['.', '..', 'index.php']);

require HEAD;
require MENU;
?>

<div class="main-content">
    <style>
        body { font-family: Arial; padding: 20px; background: #f9f9f9; }
        h2 { color: #333; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; }
        a { text-decoration: none; color: #007bff; font-weight: bold; }
        a:hover { text-decoration: underline; }
    </style>
    <h2>📁 MiniDrive - Archivos Frecuentes</h2>
    <ul>
        <?php foreach ($archivos as $archivo): ?>
            <li>📄 <a href="<?= $archivo ?>" download><?= $archivo ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php require FOOT; ?>

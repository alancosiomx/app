<?php
function mapear_bbva($pdo)
{
    $insertados = 0;
    $duplicados = 0;

    // Obtener columnas válidas de destino
    $columnasDestino = obtener_columnas($pdo, 'servicios_omnipos');

    // Obtener registros desde staging
    $bbva = $pdo->query("SELECT * FROM servicios_bbva")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($bbva as $fila) {
        $ticket = $fila['ticket'] ?? null;
        if (!$ticket) continue;

        // Evitar duplicado en OMNIPOS
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM servicios_omnipos WHERE ticket = ?");
        $stmt->execute([$ticket]);
        if ($stmt->fetchColumn() > 0) {
            $duplicados++;
            continue;
        }

        // Preparar inserción
        $campos = [];
        $valores = [];

        foreach ($fila as $col => $val) {
            if (in_array($col, $columnasDestino)) {
                $campos[] = $col;
                $valores[$col] = $val;
            }
        }

        $campos[] = 'banco';
        $valores['banco'] = 'BBVA';

        $campos[] = 'estatus';
        $valores['estatus'] = 'Por Asignar';

        $campos[] = 'fecha_carga';
        $valores['fecha_carga'] = date('Y-m-d H:i:s');

        if (count($campos)) {
            $sql = "INSERT INTO servicios_omnipos (" . implode(',', $campos) . ") VALUES (:" . implode(', :', $campos) . ")";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($valores);
            $insertados++;
        }
    }

    return "✅ Servicios insertados: $insertados\n⚠️ Tickets duplicados: $duplicados";
}

function obtener_columnas($pdo, $tabla)
{
    $stmt = $pdo->query("DESCRIBE $tabla");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function mapear_banregio($pdo)
{
    return "🛠️ Función mapear_banregio aún no implementada.";
}

function mapear_azteca($pdo)
{
    return "🛠️ Función mapear_azteca aún no implementada.";
}

<?php
// your_project/lib/PdfGenerator.php

// La ruta a autoload.php desde 'lib/' debe subir un nivel para llegar a la raíz donde está 'vendor/'
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator {
    private $conn; // Propiedad para almacenar la conexión a la base de datos

    /**
     * Constructor de la clase PdfGenerator.
     * @param mysqli $dbConnection Objeto de conexión a la base de datos (mysqli).
     */
    public function __construct(mysqli $dbConnection) {
        $this->conn = $dbConnection;
    }

    /**
     * Genera un PDF de hoja de servicio a partir de un ID de ticket.
     * @param string $ticket_id El ID del ticket a buscar en la base de datos.
     * @return array Un array con 'status' ('success' o 'error'), 'message' (en caso de error),
     * 'pdf_output' (el contenido binario del PDF) y 'filename' (nombre sugerido para el PDF).
     */
    public function generateServiceSheetPdf($ticket_id) {
        // Validar el ticket_id
        if (empty($ticket_id)) {
            return ['status' => 'error', 'message' => 'No se proporcionó un ID de ticket.'];
        }

        // 1. Obtener Datos de la Base de Datos
        // Asume que la tabla es 'service_tickets' y la columna de ticket es 'Ticket'
        $sql = "SELECT * FROM service_tickets WHERE Ticket = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return ['status' => 'error', 'message' => 'Error en la preparación de la consulta SQL: ' . $this->conn->error];
        }

        // Usamos 's' para string, asumiendo que Ticket puede contener letras (como PAX6R995429)
        // Si siempre es un número puro (ej. 32665895), podrías usar 'i' para integer
        $stmt->bind_param("s", $ticket_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // 2. Mapeo de Datos para la Plantilla HTML
            // Asegúrate de que las claves aquí coincidan EXACTAMENTE con los placeholders ({{clave}}) en tu service_sheet_template.html
            // y que los valores provengan de tus nombres de columna en la base de datos.
            $data = [
                'Ticket' => $row['Ticket'] ?? '',
                'Afiliacion' => $row['Afiliacion'] ?? '',
                'Fecha' => $row['Fecha'] ?? '',
                'Telefono_contacto_1' => $row['Telefono_contacto_1'] ?? '',
                'Comercio' => $row['Comercio'] ?? '',
                'Referencia' => $row['Referencia'] ?? '',
                'Horario' => $row['Horario'] ?? '',
                'Domicilio' => $row['Domicilio'] ?? '',
                'Colonia' => $row['Colonia'] ?? '',
                'CP' => $row['CP'] ?? '',
                'Ciudad' => $row['Ciudad'] ?? '',
                'Servicio' => $row['Servicio'] ?? '',
                'Cantidad_Insumos' => $row['Cantidad_Insumos'] ?? '',
                'VIM' => $row['VIM'] ?? '',
                // Para los comentarios, nl2br() convierte saltos de línea a <br> y htmlspecialchars() para seguridad
                'Comentarios' => nl2br(htmlspecialchars($row['Comentarios'] ?? '')),
                'Senal_Porcentaje' => $row['Senal_Porcentaje'] ?? '',
                'Carrier' => $row['Carrier'] ?? '',
                'Rollos_Entregados' => $row['Rollos_Entregados'] ?? '',
                'Serie_Instalada' => $row['Serie_Instalada'] ?? '',
                'Serie_Retirada' => $row['Serie_Retirada'] ?? '',
                'Modelo_Instalado' => $row['Modelo_Instalado'] ?? '',
                'Modelo_Retirado' => $row['Modelo_Retirado'] ?? '',
                'Calificacion_IDC' => $row['Calificacion_IDC'] ?? '',
                'Calificacion_TPV' => $row['Calificacion_TPV'] ?? '',
                'IDC' => $row['IDC_Name'] ?? '', // Asumiendo que el nombre del IDC está en una columna llamada 'IDC_Name'

                // Manejo de Checkboxes:
                // Si tu columna en DB es 1/0 para booleano: (isset($row['Columna']) && $row['Columna'] == 1) ? 'checked' : ''
                // Si tu columna en DB es 'SI'/'NO' para string: (isset($row['Columna']) && $row['Columna'] == 'SI') ? 'checked' : ''

                'Instalacion_Checked' => (isset($row['Instalacion']) && $row['Instalacion'] == 1) ? 'checked' : '',
                'Sustitucion_Checked' => (isset($row['Sustitucion']) && $row['Sustitucion'] == 1) ? 'checked' : '',
                'Reprogramacion_Checked' => (isset($row['Reprogramacion']) && $row['Reprogramacion'] == 1) ? 'checked' : '',
                'Entrega_Rollos_Checked' => (isset($row['Entrega_Rollos']) && $row['Entrega_Rollos'] == 1) ? 'checked' : '',
                'Entrega_Publicidad_Checked' => (isset($row['Entrega_Publicidad']) && $row['Entrega_Publicidad'] == 1) ? 'checked' : '',
                'Cambio_SIM_Checked' => (isset($row['Cambio_SIM']) && $row['Cambio_SIM'] == 1) ? 'checked' : '',
                'Cambio_Eliminador_Checked' => (isset($row['Cambio_Eliminador']) && $row['Cambio_Eliminador'] == 1) ? 'checked' : '',
                'Cambio_Bateria_Checked' => (isset($row['Cambio_Bateria']) && $row['Cambio_Bateria'] == 1) ? 'checked' : '',

                'GPRS_SI_Checked' => (isset($row['GPRS']) && $row['GPRS'] == 'SI') ? 'checked' : '',
                'GPRS_NO_Checked' => (isset($row['GPRS']) && $row['GPRS'] == 'NO') ? 'checked' : '',
                'Movistar_Checked' => (isset($row['Carrier_Type']) && $row['Carrier_Type'] == 'MOVISTAR') ? 'checked' : '',
                'Ethernet_SI_Checked' => (isset($row['Ethernet']) && $row['Ethernet'] == 'SI') ? 'checked' : '',
                'Ethernet_NO_Checked' => (isset($row['Ethernet']) && $row['Ethernet'] == 'NO') ? 'checked' : '',
                'Telcel_Checked' => (isset($row['Carrier_Type']) && $row['Carrier_Type'] == 'TELCEL') ? 'checked' : '',
                'Wifi_SI_Checked' => (isset($row['Wifi']) && $row['Wifi'] == 'SI') ? 'checked' : '',
                'Wifi_NO_Checked' => (isset($row['Wifi']) && $row['Wifi'] == 'NO') ? 'checked' : '',
                'M2M_Global_Checked' => (isset($row['Carrier_Type']) && $row['Carrier_Type'] == 'M2M GLOBAL') ? 'checked' : '',

                'Capacitacion_Checked' => (isset($row['Capacitacion']) && $row['Capacitacion'] == 1) ? 'checked' : '',
                'Instalacion_SC_Checked' => (isset($row['Instalacion_SC']) && $row['Instalacion_SC'] == 1) ? 'checked' : '',
                'Kit_Instalacion_Checked' => (isset($row['Kit_Instalacion']) && $row['Kit_Instalacion'] == 1) ? 'checked' : '',
                'Retail_Checked' => (isset($row['Tipo_Comercio']) && $row['Tipo_Comercio'] == 'RETAIL') ? 'checked' : '',
                'Hotel_Checked' => (isset($row['Tipo_Comercio']) && $row['Tipo_Comercio'] == 'HOTEL') ? 'checked' : '',
                'Restaurante_Checked' => (isset($row['Tipo_Comercio']) && $row['Tipo_Comercio'] == 'RESTAURANTE') ? 'checked' : '',
                'Otro_Tipo_Checked' => (isset($row['Tipo_Comercio']) && $row['Tipo_Comercio'] == 'OTRO') ? 'checked' : '',

                'Move2500_Instalado_Checked' => (isset($row['Modelo_Instalado_Tipo']) && $row['Modelo_Instalado_Tipo'] == 'MOVE 2500') ? 'checked' : '',
                'VX520_Instalado_Checked' => (isset($row['Modelo_Instalado_Tipo']) && $row['Modelo_Instalado_Tipo'] == 'VX 520') ? 'checked' : '',
                'IWL_Instalado_Checked' => (isset($row['Modelo_Instalado_Tipo']) && $row['Modelo_Instalado_Tipo'] == 'IWL') ? 'checked' : '',
                'Otro_Modelo_Instalado_Checked' => (isset($row['Modelo_Instalado_Tipo']) && $row['Modelo_Instalado_Tipo'] == 'OTRO') ? 'checked' : '',
                'Move2500_Retirado_Checked' => (isset($row['Modelo_Retirado_Tipo']) && $row['Modelo_Retirado_Tipo'] == 'MOVE 2500') ? 'checked' : '',
                'VX520_Retirado_Checked' => (isset($row['Modelo_Retirado_Tipo']) && $row['Modelo_Retirado_Tipo'] == 'VX 520') ? 'checked' : '',
                'IWL_Retirado_Checked' => (isset($row['Modelo_Retirado_Tipo']) && $row['Modelo_Retirado_Tipo'] == 'IWL') ? 'checked' : '',
                'Otro_Modelo_Retirado_Checked' => (isset($row['Modelo_Retirado_Tipo']) && $row['Modelo_Retirado_Tipo'] == 'OTRO') ? 'checked' : '',

                'Problema_Solucionado_SI_Checked' => (isset($row['Problema_Solucionado']) && $row['Problema_Solucionado'] == 'SI') ? 'checked' : '',
                'Problema_Solucionado_NO_Checked' => (isset($row['Problema_Solucionado']) && $row['Problema_Solucionado'] == 'NO') ? 'checked' : '',
                'Calificacion_IDC_Malo_Checked' => (isset($row['Calificacion_IDC_Valor']) && $row['Calificacion_IDC_Valor'] == 'MALO') ? 'checked' : '',
                'Calificacion_IDC_Bueno_Checked' => (isset($row['Calificacion_IDC_Valor']) && $row['Calificacion_IDC_Valor'] == 'BUENO') ? 'checked' : '',
                'Calificacion_IDC_Excelente_Checked' => (isset($row['Calificacion_IDC_Valor']) && $row['Calificacion_IDC_Valor'] == 'EXCELENTE') ? 'checked' : '',
                'Calificacion_TPV_Malo_Checked' => (isset($row['Calificacion_TPV_Valor']) && $row['Calificacion_TPV_Valor'] == 'MALO') ? 'checked' : '',
                'Calificacion_TPV_Bueno_Checked' => (isset($row['Calificacion_TPV_Valor']) && $row['Calificacion_TPV_Valor'] == 'BUENO') ? 'checked' : '',
                'Afiliacion_Misma_TPV_SI_Checked' => (isset($row['Afiliacion_Misma_TPV']) && $row['Afiliacion_Misma_TPV'] == 'SI') ? 'checked' : '',
                'Afiliacion_Misma_TPV_NO_Checked' => (isset($row['Afiliacion_Misma_TPV']) && $row['Afiliacion_Misma_TPV'] == 'NO') ? 'checked' : '',
                'Comprobante_Digital_Checked' => (isset($row['Comprobante_Deseado']) && $row['Comprobante_Deseado'] == 'DIGITAL') ? 'checked' : '',
                'Comprobante_Fisico_Checked' => (isset($row['Comprobante_Deseado']) && $row['Comprobante_Deseado'] == 'FISICO') ? 'checked' : '',
                'Comprobante_No_Checked' => (isset($row['Comprobante_Deseado']) && $row['Comprobante_Deseado'] == 'NO') ? 'checked' : '',
            ];

            // 3. Leer la Plantilla HTML (ruta desde lib/ a templates/)
            $html = file_get_contents(__DIR__ . '/../templates/service_sheet_template.html');

            if ($html === false) {
                return ['status' => 'error', 'message' => 'Error: No se pudo cargar la plantilla HTML. Verifique la ruta: ' . __DIR__ . '/../templates/service_sheet_template.html'];
            }

            // 4. Rellenar los Placeholders
            foreach ($data as $key => $value) {
                $html = str_replace('{{' . $key . '}}', $value, $html);
            }

            // 5. Generación de PDF con Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true); // Necesario si tu plantilla usa el logo bbva_logo.png

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('letter', 'portrait');
            $dompdf->render();

            // Devuelve el contenido binario del PDF y el nombre de archivo sugerido
            return ['status' => 'success', 'pdf_output' => $dompdf->output(), 'filename' => "Hoja_Servicio_Ticket_" . $ticket_id . ".pdf"];

        } else {
            // Si no se encuentra el ticket
            return ['status' => 'error', 'message' => 'No se encontró ningún registro para el Ticket ID: ' . htmlspecialchars($ticket_id)];
        }
    }
}
<?php
require_once 'conexion.php';
require_once '../TCPDF/tcpdf.php';

// Verificar si hay sesión activa y permisos
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../html/login.html");
    exit();
}

// Obtener el ID del usuario
$usuario_id = $_SESSION['usuario_id'];

// Obtener las actividades del cronograma del usuario actual
$query = "SELECT a.nombre, a.descripcion, a.fecha 
          FROM actividad a
          JOIN cronograma c ON a.cronograma_id = c.cronograma_id
          JOIN planeacion p ON c.cronograma_id = p.cronograma_id
          WHERE p.solicitante_id = ?
          AND p.validez_id = 1
          ORDER BY a.fecha ASC";

$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

// Crear nueva instancia de TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configuración del documento
$pdf->SetCreator('Centro Comunitario Juan Diego');
$pdf->SetAuthor('Sistema de Planeación');
$pdf->SetTitle('Calendario de Actividades');
$pdf->SetSubject('Cronograma de Actividades');

// Configurar márgenes
$pdf->SetMargins(15, 25, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Configurar cabecera
$pdf->setHeaderData('', 0, 'Centro Comunitario Juan Diego I.A.P.', 'Calendario de Actividades');

// Fuentes
$pdf->setHeaderFont(Array('helvetica', '', 10));
$pdf->setFooterFont(Array('helvetica', '', 8));

$pdf->AddPage();

// Crear HTML para la tabla de actividades
$html = '<h1 style="text-align:center;">Cronograma de Actividades</h1>';
$html .= '<p style="text-align:center;">Fecha de generación: ' . date('d/m/Y') . '</p>';

// Obtener nombre del coordinador
$query_nombre = "SELECT nombre, apellido_paterno, apellido_materno, area_id 
                FROM usuario 
                WHERE usuario_id = ?";
$stmt_nombre = $conexion->prepare($query_nombre);
$stmt_nombre->bind_param("s", $usuario_id);
$stmt_nombre->execute();
$result_nombre = $stmt_nombre->get_result();
$nombre_coordinador = "";
$area_nombre = "";

if ($row_nombre = $result_nombre->fetch_assoc()) {
    $nombre_coordinador = $row_nombre['nombre'] . ' ' . $row_nombre['apellido_paterno'] . ' ' . $row_nombre['apellido_materno'];
    
    // Determinar el nombre del área
    switch ($row_nombre['area_id']) {
        case 4: $area_nombre = 'Academia de belleza'; break;
        case 5: $area_nombre = 'Academia de cuidado de la salud'; break;
        case 6: $area_nombre = 'Apoyo psicológico'; break;
        case 7: $area_nombre = 'Artículos de belleza y aseo personal'; break;
        case 8: $area_nombre = 'Banco de alimentos'; break;
        case 9: $area_nombre = 'Bazar'; break;
        case 10: $area_nombre = 'Clínica dental'; break;
        case 11: $area_nombre = 'Comedor comunitario'; break;
        case 12: $area_nombre = 'Consulta médica'; break;
        case 13: $area_nombre = 'Escuela de computación'; break;
        case 14: $area_nombre = 'Escuela de gastronomía'; break;
        case 15: $area_nombre = 'Estimulación temprana'; break;
        case 16: $area_nombre = 'Farmacia Similares'; break;
        case 17: $area_nombre = 'Guardería'; break;
        case 18: $area_nombre = 'Preescolar'; break;
        case 19: $area_nombre = 'Tortillería'; break;
        default: $area_nombre = 'Área no definida';
    }
}
$stmt_nombre->close();

$html .= '<p style="text-align:center;margin-bottom:20px;"><strong>Coordinador:</strong> ' . htmlspecialchars($nombre_coordinador) . ' - <strong>Área:</strong> ' . htmlspecialchars($area_nombre) . '</p>';

$html .= '<table border="1" cellpadding="5" style="width:100%;border-collapse:collapse;">';
$html .= '<thead>
            <tr style="background-color:#003366;color:white;text-align:center;">
                <th width="20%" style="text-align:center;color:white;font-weight:bold;">Fecha</th>
                <th width="30%" style="text-align:center;color:white;font-weight:bold;">Actividad</th>
                <th width="50%" style="text-align:center;color:white;font-weight:bold;">Descripción</th>
            </tr>
          </thead>';
$html .= '<tbody>';

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $fecha_formateada = date('d/m/Y', strtotime($fila['fecha']));
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($fecha_formateada) . '</td>';
        $html .= '<td>' . htmlspecialchars($fila['nombre']) . '</td>';
        $html .= '<td>' . htmlspecialchars($fila['descripcion']) . '</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="3" style="text-align:center;">No hay actividades registradas</td></tr>';
}

$html .= '</tbody>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Enviar el PDF al navegador
$pdf->Output('Calendario_Actividades.pdf', 'D');
$stmt->close();
$conexion->close();
?>
<?php
require_once 'conexion.php';
require_once '../TCPDF/tcpdf.php';

// Verificar si hay sesión activa y permisos
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_id'] != 2) {
    header("Location: ../html/login.html");
    exit();
}

// Obtener filtro de área si está presente
$area_filtro = isset($_GET['area']) ? intval($_GET['area']) : 0;

// Consulta para obtener las actividades de todos los coordinadores
$query = "SELECT a.nombre, a.descripcion, a.fecha,
                 u.nombre as usuario_nombre, u.apellido_paterno, u.apellido_materno, u.area_id
          FROM actividad a
          JOIN cronograma c ON a.cronograma_id = c.cronograma_id
          JOIN planeacion p ON c.cronograma_id = p.cronograma_id
          JOIN usuario u ON p.solicitante_id = u.usuario_id
          WHERE p.estatus_id = 4"; // Solo planeaciones aprobadas

// Agregar filtro de área si está presente
if ($area_filtro > 0) {
    $query .= " AND u.area_id = ?";
}

// Ordenar por fecha, área y nombre
$query .= " ORDER BY a.fecha ASC, u.area_id ASC, a.nombre ASC";

// Preparar y ejecutar consulta
$stmt = $conexion->prepare($query);

// Vincular parámetros si hay filtro
if ($area_filtro > 0) {
    $stmt->bind_param("i", $area_filtro);
}

$stmt->execute();
$resultado = $stmt->get_result();

// Crear nueva instancia de TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configuración del documento
$pdf->SetCreator('Centro Comunitario Juan Diego');
$pdf->SetAuthor('Sistema de Administración');
$pdf->SetTitle('Calendario de Actividades');
$pdf->SetSubject('Cronograma de Actividades de Coordinadores');

// Configurar márgenes
$pdf->SetMargins(15, 25, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Configurar cabecera
$pdf->setHeaderData('', 0, 'Centro Comunitario Juan Diego I.A.P.', 'Calendario de Actividades - Administración');

// Fuentes
$pdf->setHeaderFont(Array('helvetica', '', 10));
$pdf->setFooterFont(Array('helvetica', '', 8));

$pdf->AddPage();

// Título
$html = '<h1 style="text-align:center;">Calendario de Actividades</h1>';
$html .= '<p style="text-align:center;">Fecha de generación: ' . date('d/m/Y') . '</p>';

if ($area_filtro > 0) {
    // Determinar nombre del área seleccionada
    $area_nombre = '';
    switch ($area_filtro) {
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
    
    $html .= '<p style="text-align:center;">Filtrado por área: ' . htmlspecialchars($area_nombre) . '</p>';
}

// Crear tabla para el reporte
$html .= '<table border="1" cellpadding="5" style="width:100%;border-collapse:collapse;">';
$html .= '<thead>
            <tr style="background-color:#003366;color:white;text-align:center;">
                <th width="15%" style="text-align:center;color:white;font-weight:bold;">Fecha</th>
                <th width="20%" style="text-align:center;color:white;font-weight:bold;">Actividad</th>
                <th width="30%" style="text-align:center;color:white;font-weight:bold;">Descripción</th>
                <th width="20%" style="text-align:center;color:white;font-weight:bold;">Coordinador</th>
                <th width="15%" style="text-align:center;color:white;font-weight:bold;">Área</th>
            </tr>
          </thead>';
$html .= '<tbody>';

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        // Formatear la fecha
        $fecha_formateada = date('d/m/Y', strtotime($fila['fecha']));
        
        // Nombre completo del coordinador
        $nombre_coordinador = $fila['usuario_nombre'] . ' ' . $fila['apellido_paterno'] . ' ' . $fila['apellido_materno'];
        
        // Determinar el nombre del área
        $area_nombre = '';
        switch ($fila['area_id']) {
            case 4: $area_nombre = 'Academia de belleza'; break;
            case 5: $area_nombre = 'Academia de cuidado de la salud'; break;
            case 6: $area_nombre = 'Apoyo psicológico'; break;
            case 7: $area_nombre = 'Artículos de belleza y aseo'; break;
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
        
        // Agregar fila a la tabla
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($fecha_formateada) . '</td>';
        $html .= '<td>' . htmlspecialchars($fila['nombre']) . '</td>';
        $html .= '<td>' . htmlspecialchars($fila['descripcion']) . '</td>';
        $html .= '<td>' . htmlspecialchars($nombre_coordinador) . '</td>';
        $html .= '<td>' . htmlspecialchars($area_nombre) . '</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="5" style="text-align:center;">No hay actividades registradas</td></tr>';
}

$html .= '</tbody>';
$html .= '</table>';

// Añadir información de pie de página
$html .= '<p style="text-align:center; margin-top:20px; font-style:italic;">Este reporte fue generado por el administrador de planeación.</p>';

$pdf->writeHTML($html, true, false, true, false, '');

// Enviar el PDF al navegador
$pdf->Output('Calendario_Actividades_Administracion.pdf', 'D');
$stmt->close();
$conexion->close();
?>
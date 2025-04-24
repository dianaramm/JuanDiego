<?php
require_once 'conexion.php';
require_once '../TCPDF/tcpdf.php';

// Verificar si hay sesión activa y permisos
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../html/login.html");
    exit();
}

// Función para mostrar errores en formato JSON
function mostrarError($mensaje, $detalles = '') {
    header('Content-Type: application/json');
    error_log("Error en generación de PDF: $mensaje - $detalles");
    echo json_encode([
        'error' => true, 
        'mensaje' => $mensaje,
        'detalles' => $detalles
    ]);
    exit();
}

// Función para generar PDF directamente para descargar
function generarPDF($titulo, $html, $nombreArchivo) {
    try {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Configuración del documento
        $pdf->SetCreator('Centro Comunitario Juan Diego');
        $pdf->SetAuthor('Sistema de Planeación');
        $pdf->SetTitle($titulo);
        $pdf->SetSubject($titulo);
        
        // Configurar márgenes
        $pdf->SetMargins(15, 25, 15);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        
        // Configurar cabecera
        $pdf->setHeaderData('', 0, 'Centro Comunitario Juan Diego I.A.P.', $titulo);
        
        // Fuentes
        $pdf->setHeaderFont(Array('helvetica', '', 10));
        $pdf->setFooterFont(Array('helvetica', '', 8));
        
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Devolver el PDF como salida directa
        $pdf->Output($nombreArchivo, 'D');
        exit();
    } catch (Exception $e) {
        throw new Exception('Error al generar PDF: ' . $e->getMessage());
    }
}

// Procesar solicitud de PDF
try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('No se especificó una planeación para generar el PDF');
    }
    
    $id = intval($_GET['id']);
    
    // Consultar los datos de la planeación
    $query = "SELECT p.*, u.nombre as usuario_nombre, u.apellido_paterno, u.apellido_materno, u.area_id 
              FROM planeacion p 
              JOIN usuario u ON p.solicitante_id = u.usuario_id 
              WHERE p.planeacion_id = ?";
    
    $stmt = $conexion->prepare($query);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conexion->error);
    }
    
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('No se encontró la planeación solicitada');
    }
    
    $planeacion = $result->fetch_assoc();
    
    // Determinar el nombre del área basado en area_id
    $area_nombre = '';
    switch ($planeacion['area_id']) {
        case 1: $area_nombre = 'Sistemas'; break;
        case 2: $area_nombre = 'Planeación'; break;
        case 3: $area_nombre = 'Finanzas'; break;
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
    
    // Formatear la fecha de creación
    $fecha_creacion = date('d/m/Y', strtotime($planeacion['fecha_creacion']));
    
    // Generar HTML para el PDF
    $nombreCompleto = $planeacion['usuario_nombre'] . ' ' . $planeacion['apellido_paterno'] . ' ' . $planeacion['apellido_materno'];
    $tituloDocumento = "Carta Descriptiva - " . $planeacion['nombre'];
    
    $html = '<h1 style="text-align:center;">Carta Descriptiva</h1>';
    $html .= '<p style="text-align:center;">Fecha de creación: '.$fecha_creacion.'</p>';
    
    $html .= '<table border="1" cellpadding="5" style="width:100%;border-collapse:collapse;margin-bottom:20px;">';
    $html .= '<tr><th style="width:30%;background-color:#f2f2f2;">Nombre de la planeación:</th><td>' . htmlspecialchars($planeacion['nombre']) . '</td></tr>';
    $html .= '<tr><th style="width:30%;background-color:#f2f2f2;">Tipo de planeación:</th><td>' . htmlspecialchars($planeacion['tipo']) . '</td></tr>';
    $html .= '<tr><th style="width:30%;background-color:#f2f2f2;">Importancia:</th><td>' . htmlspecialchars($planeacion['importancia']) . '</td></tr>';
    $html .= '<tr><th style="width:30%;background-color:#f2f2f2;">Solicitante:</th><td>' . htmlspecialchars($nombreCompleto) . '</td></tr>';
    $html .= '<tr><th style="width:30%;background-color:#f2f2f2;">Área:</th><td>' . htmlspecialchars($area_nombre) . '</td></tr>';
    $html .= '</table>';
    
    $html .= '<h3 style="color:#003366;padding-bottom:5px;border-bottom:1px solid #ddd;">Descripción</h3>';
    $html .= '<div style="margin-bottom:20px;">' . nl2br(htmlspecialchars($planeacion['descripcion'])) . '</div>';
    
    $html .= '<h3 style="color:#003366;padding-bottom:5px;border-bottom:1px solid #ddd;">Objetivo</h3>';
    $html .= '<div style="margin-bottom:20px;">' . nl2br(htmlspecialchars($planeacion['objetivo'])) . '</div>';
    
    // Si hay campos adicionales en la planeación anual, se pueden agregar aquí
    
    // Generar y enviar el PDF
    $nombreArchivo = 'Carta_Descriptiva_' . $id . '_' . date('YmdHis') . '.pdf';
    generarPDF($tituloDocumento, $html, $nombreArchivo);
    
    $stmt->close();
    $conexion->close();
    
} catch (Exception $e) {
    mostrarError('Error al generar la carta descriptiva', $e->getMessage());
}
?>
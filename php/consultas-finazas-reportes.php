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
    error_log("Error en reportes: $mensaje - $detalles");
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
        $pdf->SetAuthor('Sistema de Reportes');
        $pdf->SetTitle($titulo);
        $pdf->SetSubject($titulo);
        
        // Configurar márgenes
        $pdf->SetMargins(15, 25, 15);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        
        // Configurar cabecera sin imagen
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

// Procesar solicitud de reporte
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido. Se esperaba POST');
    }

    $tipoReporte = $_POST['tipo_reporte'] ?? '';
    $fechaInicio = $_POST['fecha_inicio'] ?? '';
    $fechaFin = $_POST['fecha_fin'] ?? '';
    $areaId = $_POST['area'] ?? '';
    $tipoEmpleado = $_POST['coordinador'] ?? '';
    $formato = $_POST['formato'] ?? 'visualizar';
    
    // Validaciones básicas con mensajes detallados
    if (empty($tipoReporte)) {
        throw new Exception('Debe seleccionar un tipo de reporte');
    }
    
    if (empty($fechaInicio)) {
        throw new Exception('Debe especificar una fecha de inicio');
    }
    
    if (empty($fechaFin)) {
        throw new Exception('Debe especificar una fecha de fin');
    }
    
    if (strtotime($fechaInicio) > strtotime($fechaFin)) {
        throw new Exception('La fecha de inicio no puede ser mayor a la fecha de fin');
    }
    
    // Construir consulta según tipo de reporte
    $query = "";
    $tituloReporte = "";
    
    if ($tipoReporte === 'nomina') {
        $tituloReporte = "Reporte de Nómina";
        // Verificamos si la tabla nomina tiene registros
        $checkQuery = "SELECT COUNT(*) as count FROM nomina";
        $checkStmt = $conexion->prepare($checkQuery);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $hasRecords = $checkResult->fetch_assoc()['count'] > 0;
        
        if (!$hasRecords) {
            // Consulta sin usar la tabla nómina ya que no hay registros
            $query = "SELECT u.usuario_id, u.nombre, u.apellido_paterno, u.apellido_materno, 
                      u.correo, u.telefono, a.tipo as area, u.cargo, u.tipo_id
                      FROM usuario u
                      LEFT JOIN area a ON u.area_id = a.area_id
                      WHERE u.estatus_id = 1";
                      
            if (!empty($areaId)) {
                $query .= " AND u.area_id = ?";
            }
            
            if ($tipoEmpleado !== 'todos') {
                switch($tipoEmpleado) {
                    case 'super': $query .= " AND u.tipo_id = 1"; break;
                    case 'administrador': $query .= " AND u.tipo_id = 2"; break;
                    case 'finanzas': $query .= " AND u.tipo_id = 3"; break;
                    case 'coordinador': $query .= " AND u.tipo_id = 4"; break;
                    case 'empleados': $query .= " AND u.tipo_id = 5"; break;
                }
            }
        } else {
            // Consulta normal con la tabla nómina
            $query = "SELECT u.usuario_id, u.nombre, u.apellido_paterno, u.apellido_materno, 
                      u.correo, u.telefono, a.tipo as area, u.cargo, 
                      n.sueldo, n.fecha_ingreso, n.imss, n.sar, n.infonavit,
                      TIMESTAMPDIFF(MONTH, n.fecha_ingreso, ?) as meses_transcurridos,
                      (n.sueldo * TIMESTAMPDIFF(MONTH, n.fecha_ingreso, ?)) as total_pago
                      FROM usuario u
                      LEFT JOIN nomina n ON u.usuario_id = n.usuario_id
                      LEFT JOIN area a ON u.area_id = a.area_id
                      WHERE u.estatus_id = 1";
            
            if (!empty($areaId)) {
                $query .= " AND u.area_id = ?";
            }
            
            if ($tipoEmpleado !== 'todos') {
                switch($tipoEmpleado) {
                    case 'super': $query .= " AND u.tipo_id = 1"; break;
                    case 'administrador': $query .= " AND u.tipo_id = 2"; break;
                    case 'finanzas': $query .= " AND u.tipo_id = 3"; break;
                    case 'coordinador': $query .= " AND u.tipo_id = 4"; break;
                    case 'empleados': $query .= " AND u.tipo_id = 5"; break;
                }
            }
        }
        
        $query .= " ORDER BY u.nombre, u.apellido_paterno, u.apellido_materno";
    } elseif ($tipoReporte === 'recursos_humanos') {
        $tituloReporte = "Reporte de Recursos Humanos";
        $query = "SELECT u.usuario_id, u.nombre, u.apellido_paterno, u.apellido_materno, 
                  u.correo, u.telefono, a.tipo as area, u.cargo, 
                  tu.tipo as tipo_usuario, e.tipo as estatus,
                  n.fecha_ingreso
                  FROM usuario u
                  LEFT JOIN area a ON u.area_id = a.area_id
                  LEFT JOIN tipo_usuario tu ON u.tipo_id = tu.tipo_id
                  LEFT JOIN estatus e ON u.estatus_id = e.estatus_id
                  LEFT JOIN nomina n ON u.usuario_id = n.usuario_id
                  WHERE (n.fecha_ingreso IS NULL OR (n.fecha_ingreso BETWEEN ? AND ?))";
        
        // Aplicar filtros adicionales
        if (!empty($areaId)) {
            $query .= " AND u.area_id = ?";
        }
        
        if ($tipoEmpleado !== 'todos') {
            switch($tipoEmpleado) {
                case 'super': $query .= " AND u.tipo_id = 1"; break;
                case 'administrador': $query .= " AND u.tipo_id = 2"; break;
                case 'finanzas': $query .= " AND u.tipo_id = 3"; break;
                case 'coordinador': $query .= " AND u.tipo_id = 4"; break;
                case 'empleados': $query .= " AND u.tipo_id = 5"; break;
            }
        }
        
        $query .= " ORDER BY n.fecha_ingreso, a.tipo, u.apellido_paterno, u.apellido_materno";
    } else {
        throw new Exception('Tipo de reporte no válido');
    }
    
    // Preparar y ejecutar consulta
    $stmt = $conexion->prepare($query);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conexion->error);
    }
    
    // Vincular parámetros de forma condicional
    if ($tipoReporte === 'nomina') {
        // Verificamos si estamos usando la consulta con o sin tabla nómina
        $checkQuery = "SELECT COUNT(*) as count FROM nomina";
        $checkStmt = $conexion->prepare($checkQuery);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $hasRecords = $checkResult->fetch_assoc()['count'] > 0;
        
        if (!$hasRecords) {
            // Sin tabla nómina
            if (!empty($areaId)) {
                $stmt->bind_param("i", $areaId);
            }
        } else {
            // Con tabla nómina
            if (!empty($areaId)) {
                $stmt->bind_param("ssi", $fechaFin, $fechaFin, $areaId);
            } else {
                $stmt->bind_param("ss", $fechaFin, $fechaFin);
            }
        }
    } else { // recursos_humanos
        if (!empty($areaId)) {
            $stmt->bind_param("ssi", $fechaInicio, $fechaFin, $areaId);
        } else {
            $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        }
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $sinDatos = $result->num_rows === 0;
    
    // Generar HTML para el reporte
    $html = '<h1 style="text-align:center;">'.$tituloReporte.'</h1>';
    $html .= '<p style="text-align:center;">Periodo: '.date('d/m/Y', strtotime($fechaInicio)).' al '.date('d/m/Y', strtotime($fechaFin)).'</p>';
    
    $html .= '<table border="1" cellpadding="5" style="width:100%;border-collapse:collapse;">';
    
    // Encabezados de tabla según tipo de reporte
    if ($tipoReporte === 'nomina') {
        // Verificamos si estamos usando la consulta con o sin tabla nómina
        $checkQuery = "SELECT COUNT(*) as count FROM nomina";
        $checkStmt = $conexion->prepare($checkQuery);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $hasRecords = $checkResult->fetch_assoc()['count'] > 0;
        
        if (!$hasRecords) {
            $html .= '<tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Área</th>
                <th>Cargo</th>
                <th>Tipo</th>
            </tr>';
        } else {
            $html .= '<tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Área</th>
                <th>Cargo</th>
                <th>Sueldo</th>
                <th>Meses</th>
                <th>Total</th>
            </tr>';
        }
    } else {
        $html .= '<tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Área</th>
            <th>Cargo</th>
            <th>Tipo</th>
            <th>Estatus</th>
            <th>Fecha Ingreso</th>
        </tr>';
    }
    
    // Variables para totales
    $totalGeneral = 0;
    $totalEmpleados = 0;
    
    if ($sinDatos) {
        $colspan = $tipoReporte === 'nomina' ? 7 : 7;
        $html .= '<tr><td colspan="'.$colspan.'" style="text-align:center;">No se encontraron registros para los filtros aplicados</td></tr>';
    } else {
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>';
            
            if ($tipoReporte === 'nomina') {
                // Verificamos si estamos usando la consulta con o sin tabla nómina
                $checkQuery = "SELECT COUNT(*) as count FROM nomina";
                $checkStmt = $conexion->prepare($checkQuery);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                $hasRecords = $checkResult->fetch_assoc()['count'] > 0;
                
                if (!$hasRecords) {
                    $html .= '<td>'.$row['usuario_id'].'</td>';
                    $html .= '<td>'.$row['nombre'].' '.$row['apellido_paterno'].' '.$row['apellido_materno'].'</td>';
                    $html .= '<td>'.($row['area'] ?? 'No asignada').'</td>';
                    $html .= '<td>'.($row['cargo'] ?? 'No asignado').'</td>';
                    $html .= '<td>'.getTipoUsuario($row['tipo_id']).'</td>';
                    
                    $totalEmpleados++;
                } else {
                    $totalPago = $row['total_pago'] ?? 0;
                    $totalGeneral += $totalPago;
                    $totalEmpleados++;
                    
                    $html .= '<td>'.$row['usuario_id'].'</td>';
                    $html .= '<td>'.$row['nombre'].' '.$row['apellido_paterno'].' '.$row['apellido_materno'].'</td>';
                    $html .= '<td>'.($row['area'] ?? 'No asignada').'</td>';
                    $html .= '<td>'.($row['cargo'] ?? 'No asignado').'</td>';
                    $html .= '<td>$'.number_format(floatval($row['sueldo'] ?? 0), 2).'</td>';
                    $html .= '<td>'.($row['meses_transcurridos'] ?? 0).'</td>';
                    $html .= '<td>$'.number_format(floatval($totalPago), 2).'</td>';
                }
            } else {
                $html .= '<td>'.$row['usuario_id'].'</td>';
                $html .= '<td>'.$row['nombre'].' '.$row['apellido_paterno'].' '.$row['apellido_materno'].'</td>';
                $html .= '<td>'.($row['area'] ?? 'No asignada').'</td>';
                $html .= '<td>'.($row['cargo'] ?? 'No asignado').'</td>';
                $html .= '<td>'.($row['tipo_usuario'] ?? 'No definido').'</td>';
                $html .= '<td>'.($row['estatus'] ?? 'No definido').'</td>';
                
                $fechaIngreso = $row['fecha_ingreso'] ? date('d/m/Y', strtotime($row['fecha_ingreso'])) : 'No registrada';
                $html .= '<td>'.$fechaIngreso.'</td>';
            }
            
            $html .= '</tr>';
        }
    }
    
    // Agregar totales si es reporte de nómina, hay registros y existen registros en la tabla nomina
    if ($tipoReporte === 'nomina' && !$sinDatos) {
        $checkQuery = "SELECT COUNT(*) as count FROM nomina";
        $checkStmt = $conexion->prepare($checkQuery);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $hasRecords = $checkResult->fetch_assoc()['count'] > 0;
        
        if ($hasRecords) {
            $html .= '<tr style="font-weight:bold;">
                <td colspan="6" style="text-align:right;">Total General:</td>
                <td>$'.number_format($totalGeneral, 2).'</td>
            </tr>';
        }
        
        $html .= '<tr style="font-weight:bold;">
            <td colspan="'.($hasRecords ? '6' : '4').'" style="text-align:right;">Total Empleados:</td>
            <td>'.$totalEmpleados.'</td>
        </tr>';
    }
    
    $html .= '</table>';
    
    // Generar PDF o devolver HTML según formato solicitado
    if ($formato === 'pdf') {
        // Generamos el PDF directamente sin enviar primero un JSON
        $nombreArchivo = 'Reporte_'.$tipoReporte.'_'.date('YmdHis').'.pdf';
        generarPDF($tituloReporte, $html, $nombreArchivo);
        // No se debe llegar aquí ya que generarPDF() tiene un exit()
    } else {
        // Si es visualización en pantalla, devolver el HTML
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'html' => $html,
            'sinDatos' => $sinDatos
        ]);
    }
    
    $stmt->close();
    $conexion->close();
    
} catch (Exception $e) {
    mostrarError('Error inesperado', $e->getMessage());
}

// Función auxiliar para obtener el tipo de usuario a partir del ID
function getTipoUsuario($tipoId) {
    switch($tipoId) {
        case 1: return 'Super Usuario';
        case 2: return 'Administrador Planeación';
        case 3: return 'Administrador Finanzas';
        case 4: return 'Coordinador';
        case 5: return 'Empleado General';
        default: return 'No definido';
    }
}
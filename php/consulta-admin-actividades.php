<?php
header('Content-Type: application/json');
require_once 'conexion.php';
session_start();

// Verificar que sea un administrador (tipo_id = 2)
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_id'] != 2) {
    echo json_encode(['success' => false, 'error' => 'Acceso no autorizado']);
    exit;
}

// Obtener filtro de área si está presente
$area_filtro = isset($_GET['area']) ? intval($_GET['area']) : 0;

// Consulta base que obtiene actividades de todas las planeaciones aprobadas
$query = "SELECT a.id_actividad, a.nombre, a.descripcion, a.fecha, 
                 u.nombre as usuario_nombre, u.apellido_paterno, u.apellido_materno, 
                 u.area_id, u.usuario_id
          FROM actividad a
          JOIN cronograma c ON a.cronograma_id = c.cronograma_id
          JOIN planeacion p ON c.cronograma_id = p.cronograma_id
          JOIN usuario u ON p.solicitante_id = u.usuario_id
          WHERE p.estatus_id = 4"; // Sólo planeaciones aprobadas

// Agregar filtro de área si está presente
if ($area_filtro > 0) {
    $query .= " AND u.area_id = ?";
}

// Ordenar por fecha y nombre
$query .= " ORDER BY a.fecha ASC, u.area_id ASC, a.nombre ASC";

// Preparar y ejecutar consulta
$stmt = $conexion->prepare($query);

// Vincular parámetros si hay filtro
if ($area_filtro > 0) {
    $stmt->bind_param("i", $area_filtro);
}

$stmt->execute();
$resultado = $stmt->get_result();

// Procesar resultados
$eventos = [];
while ($fila = $resultado->fetch_assoc()) {
    // Crear nombre completo
    $nombre_completo = $fila['usuario_nombre'] . ' ' . $fila['apellido_paterno'] . ' ' . $fila['apellido_materno'];
    
    // Determinar color según área
    $color = '#003366'; // Color por defecto
    $textColor = '#FFFFFF';
    
    // Asignar colores distintos según el área
    switch ($fila['area_id']) {
        case 4: $color = '#3498db'; break; // Academia de belleza
        case 5: $color = '#2ecc71'; break; // Academia de cuidado de la salud
        case 6: $color = '#9b59b6'; break; // Apoyo psicológico
        case 7: $color = '#34495e'; break; // Artículos de belleza
        case 8: $color = '#f1c40f'; $textColor = '#000000'; break; // Banco de alimentos
        case 9: $color = '#e67e22'; break; // Bazar
        case 10: $color = '#e74c3c'; break; // Clínica dental
        case 11: $color = '#1abc9c'; break; // Comedor comunitario
        case 12: $color = '#d35400'; break; // Consulta médica
        case 13: $color = '#8e44ad'; break; // Escuela de computación
        case 14: $color = '#27ae60'; break; // Escuela de gastronomía
        case 15: $color = '#3498db'; break; // Estimulación temprana
        case 16: $color = '#f39c12'; break; // Farmacia Similares
        case 17: $color = '#c0392b'; break; // Guardería
        case 18: $color = '#16a085'; break; // Preescolar
        case 19: $color = '#7f8c8d'; break; // Tortillería
        default: $color = '#003366'; break;
    }
    
    // Obtener nombre del área
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
        default: $area_nombre = 'Área no especificada';
    }
    
    // Formatear la información para FullCalendar
    $eventos[] = [
        'id' => $fila['id_actividad'],
        'title' => $fila['nombre'],
        'description' => $fila['descripcion'],
        'start' => $fila['fecha'],
        'backgroundColor' => $color,
        'borderColor' => $color,
        'textColor' => $textColor,
        'extendedProps' => [
            'coordinador' => $nombre_completo,
            'area' => $area_nombre,
            'usuario_id' => $fila['usuario_id']
        ]
    ];
}

$stmt->close();
$conexion->close();

echo json_encode([
    'success' => true,
    'eventos' => $eventos
]);
?>
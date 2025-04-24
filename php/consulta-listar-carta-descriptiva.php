<?php
require_once 'conexion.php';

// Obtener el área seleccionada del filtro
$area_filtro = isset($_GET['area']) ? $_GET['area'] : '';

// Construir la consulta base
$query = "SELECT p.planeacion_id, p.nombre as planeacion_nombre, 
                 u.nombre, u.apellido_paterno, u.apellido_materno, 
                 u.area_id, p.fecha_creacion
          FROM planeacion p
          INNER JOIN usuario u ON p.solicitante_id = u.usuario_id 
          WHERE p.estatus_id = 4"; // Solo planeaciones aprobadas

// Agregar filtro de área si se ha seleccionado una
if (!empty($area_filtro)) {
    $query .= " AND u.area_id = '" . $conexion->real_escape_string($area_filtro) . "'";
}

$resultado = $conexion->query($query);

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $nombre_completo = htmlspecialchars($fila['nombre'] . ' ' . $fila['apellido_paterno'] . ' ' . $fila['apellido_materno']);
        $fecha = date('d/m/Y', strtotime($fila['fecha_creacion']));
        
        // Convertir area_id a nombre
        $area_nombre = '';
        switch ($fila['area_id']) {
            case 1: $area_nombre = 'Sistemas'; break;
            case 2: $area_nombre = 'Planeación'; break;
            case 3: $area_nombre = 'Finanzas'; break;
            case 4: $area_nombre = 'Academia de belleza'; break;
            case 5: $area_nombre = 'Academia cuidado de salud'; break;
            case 6: $area_nombre = 'Apoyo psicológico'; break;
            case 7: $area_nombre = 'Artículos de belleza y aseo'; break;
            case 8: $area_nombre = 'Banco de alimentos'; break;
            case 9: $area_nombre = 'Bazaar'; break;
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
        
        echo "<tr>
                <td class='text-center'>
                    <input type='radio' name='planeacion_seleccionada' value='{$fila['planeacion_id']}'>
                </td>
                <td>{$fila['planeacion_nombre']}</td>
                <td>{$fecha}</td>
                <td>{$area_nombre}</td>
                <td>Aprobado</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No se encontraron registros de planeación aprobados.</td></tr>";
}

$conexion->close();
?>
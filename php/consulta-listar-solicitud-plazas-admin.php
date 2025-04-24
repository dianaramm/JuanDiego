<?php
require_once 'conexion.php';

// Obtener el área seleccionada del filtro (si existe)
$area_filtro = isset($_GET['area']) ? $_GET['area'] : '';

// Construir la consulta SQL
$query = "SELECT DISTINCT u.usuario_id, u.nombre, u.apellido_paterno, u.apellido_materno, u.area_id, 
          sp.puesto, a.fecha, sp.justificacion, a.aprobacion_id, 'Pendiente' AS estatus
          FROM aprobacion a
          INNER JOIN solicitud_plaza sp ON a.aprobacion_id = sp.aprobacion_id
          INNER JOIN usuario u ON a.solicitante_usuario = u.usuario_id
          WHERE a.estatus_id = 3";

// Agregar filtro de área si se ha seleccionado
if (!empty($area_filtro)) {
    $query .= " AND u.area_id = " . intval($area_filtro);
}

$resultado = $conexion->query($query);

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $nombre_completo = htmlspecialchars($fila['nombre'] . ' ' . $fila['apellido_paterno'] . ' ' . $fila['apellido_materno']);
        $area_id = $fila['area_id'];
        $fecha = htmlspecialchars($fila['fecha']);
        $estatus = htmlspecialchars($fila['estatus']);
        $puesto = htmlspecialchars($fila['puesto']);
        $justificacion = htmlspecialchars($fila['justificacion']);
        $aprobacion_id = $fila['aprobacion_id'];
        
        // Convertir el área_id a nombre de área
        $nombre_area = '';
        switch ($area_id) {
            case 1: $nombre_area = 'Sistemas'; break;
            case 2: $nombre_area = 'Planeación'; break;
            case 3: $nombre_area = 'Finanzas'; break;
            case 4: $nombre_area = 'Academia de belleza'; break;
            case 5: $nombre_area = 'Academia cuidado de salud'; break;
            case 6: $nombre_area = 'Apoyo psicológico'; break;
            case 7: $nombre_area = 'Artículos de belleza y aseo'; break;
            case 8: $nombre_area = 'Banco de alimentos'; break;
            case 9: $nombre_area = 'Bazaar'; break;
            case 10: $nombre_area = 'Clínica dental'; break;
            case 11: $nombre_area = 'Comedor comunitario'; break;
            case 12: $nombre_area = 'Consulta médica'; break;
            case 13: $nombre_area = 'Escuela de computación'; break;
            case 14: $nombre_area = 'Escuela de gastronomía'; break;
            case 15: $nombre_area = 'Estimulación temprana'; break;
            case 16: $nombre_area = 'Farmacia Similares'; break;
            case 17: $nombre_area = 'Guardería'; break;
            case 18: $nombre_area = 'Preescolar'; break;
            case 19: $nombre_area = 'Tortillería'; break;
            default: $nombre_area = 'Área no definida';
        }
        
        echo "<tr data-id='{$aprobacion_id}'>";
        echo "<td class='text-center'><input type='radio' name='solicitud' value='{$aprobacion_id}'></td>";
        echo "<td>{$nombre_completo}</td>";
        echo "<td>{$nombre_area}</td>";
        echo "<td>{$puesto}</td>";
        echo "<td>{$fecha}</td>";
        echo "<td>{$justificacion}</td>";
        echo "<td>{$estatus}</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No se encontraron solicitudes de plazas pendientes.</td></tr>";
}

$conexion->close();
?>
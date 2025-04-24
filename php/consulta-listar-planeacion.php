<?php
require_once 'conexion.php';

$area_filtro = isset($_GET['area']) ? $_GET['area'] : '';

$query = "SELECT 
    p.planeacion_id,
    u.nombre, 
    u.apellido_paterno, 
    u.apellido_materno,
    u.area_id,
    p.estatus_id,
    p.fecha_creacion
FROM planeacion p
INNER JOIN usuario u ON p.solicitante_id = u.usuario_id
WHERE p.estatus_id = 3";

if (!empty($area_filtro)) {
    $query .= " AND u.area_id = " . intval($area_filtro);
}

$resultado = $conexion->query($query);

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $nombre_completo = htmlspecialchars($fila['nombre'] . ' ' . $fila['apellido_paterno'] . ' ' . $fila['apellido_materno']);
        $fecha = htmlspecialchars($fila['fecha_creacion']);
        $planeacion_id = $fila['planeacion_id'];
        
        // Determina el nombre del área basado en area_id
        $area_nombre = '';
        switch ($fila['area_id']) {
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
            default: $area_nombre = 'Área no especificada';
        }

        // Determinar el estatus
        $estatus = ($fila['estatus_id'] == 3) ? 'Pendiente' : 'Inactivo';

        echo "<tr>
                <td>{$nombre_completo}</td>
                <td>{$fecha}</td>
                <td>{$area_nombre}</td>
                <td>{$estatus}</td>
                <td class='acciones'>
                    <button class='boton-accion boton-ver' data-id='{$planeacion_id}'>
                        <img src='../img/icono-ver.png' alt='Ver' class='icono-accion'>
                    </button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No se encontraron registros de planeación.</td></tr>";
}

$conexion->close();
?>
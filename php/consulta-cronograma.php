<?php
require_once 'conexion.php';

$query = "SELECT p.solicitante_id, u.nombre, u.apellido_paterno, u.apellido_materno, 
                 u.area_id, a.nombre as actividad_nombre, a.fecha
          FROM planeacion p
          INNER JOIN usuario u ON p.solicitante_id = u.usuario_id
          INNER JOIN actividad a ON p.cronograma_id = a.cronograma_id
          WHERE p.estatus_id = 4";

$resultado = $conexion->query($query);

$eventos = array();

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $nombre_completo = $fila['nombre'] . ' ' . $fila['apellido_paterno'] . ' ' . $fila['apellido_materno'];
        $area_id = $fila['area_id'];
        $actividad = $fila['actividad_nombre'];
        $fecha = $fila['fecha'];

        $eventos[] = array(
            'nombre' => $nombre_completo,
            'area_id' => $area_id,
            'actividad' => $actividad,
            'fecha' => $fecha
        );
    }
}

echo json_encode($eventos);

$conexion->close();
?>
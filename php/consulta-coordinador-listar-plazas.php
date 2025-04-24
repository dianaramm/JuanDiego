<?php
// Iniciar sesión
session_start();
require_once 'conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo '<tr><td colspan="5">No hay una sesión activa</td></tr>';
    exit;
}

// Obtener el ID del usuario actual (coordinador)
$usuario_id = $_SESSION['usuario_id'];

// Consulta para obtener SOLO las solicitudes en estado BORRADOR del coordinador actual
$query = "SELECT sp.plaza_id, sp.puesto, sp.justificacion, a.aprobacion_id, a.fecha, 
                 CASE 
                    WHEN a.estatus_id = 3 THEN 'Pendiente'
                    WHEN a.estatus_id = 4 THEN 'Aprobada'
                    WHEN a.estatus_id = 5 THEN 'Rechazada'
                    WHEN a.estatus_id = 6 THEN 'Borrador'
                    ELSE 'Desconocido'
                 END as estatus,
                 a.estatus_id
          FROM solicitud_plaza sp
          INNER JOIN aprobacion a ON sp.aprobacion_id = a.aprobacion_id
          WHERE a.solicitante_usuario = ? AND a.estatus_id = 6
          ORDER BY a.fecha DESC";

// Preparar y ejecutar la consulta con el ID del usuario como parámetro
$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        // Escapar datos para prevenir XSS
        $puesto = htmlspecialchars($fila['puesto']);
        $justificacion = htmlspecialchars($fila['justificacion']);
        $fecha = htmlspecialchars($fila['fecha']);
        $estatus = htmlspecialchars($fila['estatus']);
        $plaza_id = intval($fila['plaza_id']);
        $aprobacion_id = intval($fila['aprobacion_id']);
        $estatus_id = intval($fila['estatus_id']);
        
        // Determinar si se pueden mostrar botones de edición (solo en estado borrador)
        $puedeEditar = ($estatus_id == 6); // Estatus 6 = Borrador
        $disabled = $puedeEditar ? '' : 'disabled';
        
        echo "<tr>";
        
        // Añadir checkbox para selección (solo activo si está en borrador)
        echo "<td><input type='radio' name='seleccion' value='{$aprobacion_id}' data-plaza='{$plaza_id}' {$disabled} class='seleccion-plaza'></td>";
        
        echo "<td>{$puesto}</td>";
        echo "<td>{$justificacion}</td>";
        echo "<td>{$fecha}</td>";
        echo "<td>{$estatus}</td>";
        echo "</tr>";
    }
} else {
    echo '<tr><td colspan="5">No hay solicitudes registradas para este usuario</td></tr>';
}

$stmt->close();
$conexion->close();
?>
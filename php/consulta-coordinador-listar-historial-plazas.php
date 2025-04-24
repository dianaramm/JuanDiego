<?php
// Iniciar sesión
//session_start();
require_once 'conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo '<tr><td colspan="4">No hay una sesión activa</td></tr>';
    exit;
}

// Obtener el ID del usuario actual (coordinador)
$usuario_id = $_SESSION['usuario_id'];

// Consulta para obtener SOLO las solicitudes NO BORRADOR del coordinador actual
// Es decir, solicitudes en estado pendiente (3), aprobado (4) o rechazado (5)
$query = "SELECT sp.plaza_id, sp.puesto, sp.justificacion, a.aprobacion_id, a.fecha, 
                 CASE 
                    WHEN a.estatus_id = 3 THEN 'Pendiente'
                    WHEN a.estatus_id = 4 THEN 'Aprobada'
                    WHEN a.estatus_id = 5 THEN 'Rechazada'
                    ELSE 'Desconocido'
                 END as estatus,
                 a.estatus_id
          FROM solicitud_plaza sp
          INNER JOIN aprobacion a ON sp.aprobacion_id = a.aprobacion_id
          WHERE a.solicitante_usuario = ? AND a.estatus_id IN (3, 4, 5)
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
        $estatus_id = intval($fila['estatus_id']);
        
        // Definir clase de estilo según el estatus
        $clase_estatus = '';
        $texto_adicional = '';
        
        switch ($estatus_id) {
            case 3: // Pendiente
                $clase_estatus = 'color: #003366; font-weight: bold;';
                $texto_adicional = '<span style="font-style: italic; display: block; font-size: 0.9em;">En revisión</span>';
                break;
            case 4: // Aprobada
                $clase_estatus = 'color: #4caf50; font-weight: bold;';
                break;
            case 5: // Rechazada
                $clase_estatus = 'color: #f44336; font-weight: bold;';
                break;
        }
        
        echo "<tr>";
        echo "<td>{$puesto}</td>";
        echo "<td>{$justificacion}</td>";
        echo "<td>" . date('d/m/Y', strtotime($fecha)) . "</td>";
        echo "<td style='{$clase_estatus}'>{$estatus} {$texto_adicional}</td>";
        echo "</tr>";
    }
} else {
    echo '<tr><td colspan="4">No hay solicitudes enviadas para revisar</td></tr>';
}

$stmt->close();
$conexion->close();
?>
<?php
require_once 'conexion.php';
//session_start();

if (!isset($_SESSION['usuario_id'])) {
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$query = "SELECT 
    planeacion_id,
    nombre,
    fecha_creacion,
    estatus_id,
    CASE 
        WHEN estatus_id = 6 THEN 'Borrador'
        WHEN estatus_id = 3 THEN 'Pendiente'
        WHEN estatus_id = 4 THEN 'Aprobado'
        WHEN estatus_id = 5 THEN 'Rechazado'
    END as estatus
FROM planeacion 
WHERE solicitante_id = ? 
AND validez_id = 1";  // Removido el filtro de estatus_id = 6

$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    // El botón de editar solo se muestra si el estatus es 6 (Borrador)
    $botonEditar = $fila['estatus_id'] == 6 ? 
        "<button class='boton-accion' onclick='window.location.href=\"coordinador-editar-planeacion-anual.php?id=" . $fila['planeacion_id'] . "\"' style='background: none; border: none; cursor: pointer; padding: 0;'>
            <img src='../img/icono-editar.png' alt='Editar' class='icono-accion'>
        </button>" :
        "<button class='boton-accion' disabled style='background: none; border: none; cursor: not-allowed; padding: 0; opacity: 0.5;'>
            <img src='../img/icono-editar.png' alt='Editar' class='icono-accion'>
        </button>";

    echo "<tr>
            <td>" . htmlspecialchars($fila['nombre']) . "</td>
            <td>" . date('d/m/Y', strtotime($fila['fecha_creacion'])) . "</td>
            <td>" . htmlspecialchars($fila['estatus']) . "</td>
            <td class='acciones'>
                {$botonEditar}
            </td>
          </tr>";
}
$stmt->close();
$conexion->close();
?>
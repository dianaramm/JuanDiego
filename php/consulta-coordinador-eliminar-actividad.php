<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_POST['actividad_id'])) {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

$actividad_id = $_POST['actividad_id'];

// Validar permisos
$validacion = $conexion->prepare("SELECT p.planeacion_id 
                                 FROM planeacion p 
                                 JOIN actividad a ON p.cronograma_id = a.cronograma_id 
                                 WHERE a.actividad_id = ? AND p.solicitante_id = ? 
                                 AND p.estatus_id = 6");
$validacion->bind_param("is", $actividad_id, $_SESSION['usuario_id']);
$validacion->execute();
if ($validacion->get_result()->num_rows === 0) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$query = "DELETE FROM actividad WHERE actividad_id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $actividad_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Error al eliminar']);
}

$stmt->close();
$conexion->close();
?>
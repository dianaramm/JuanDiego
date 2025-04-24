<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_POST['cronograma_id'])) {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

$cronograma_id = $_POST['cronograma_id'];
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$fecha = $_POST['fecha'];

// Validar que el cronograma pertenece al usuario
$validacion = $conexion->prepare("SELECT p.planeacion_id FROM planeacion p 
                                 WHERE p.cronograma_id = ? AND p.solicitante_id = ? 
                                 AND p.estatus_id = 6");
$validacion->bind_param("is", $cronograma_id, $_SESSION['usuario_id']);
$validacion->execute();
if ($validacion->get_result()->num_rows === 0) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Validar fecha
$fecha_actual = date('Y');
$fecha_actividad = date('Y', strtotime($fecha));
if ($fecha_actividad != $fecha_actual) {
    echo json_encode(['error' => 'La fecha debe ser del año actual']);
    exit();
}

$query = "INSERT INTO actividad (nombre, descripcion, fecha, cronograma_id) 
          VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($query);
$stmt->bind_param("sssi", $nombre, $descripcion, $fecha, $cronograma_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Actividad guardada']);
} else {
    echo json_encode(['error' => 'Error al guardar']);
}

$stmt->close();
$conexion->close();
?>
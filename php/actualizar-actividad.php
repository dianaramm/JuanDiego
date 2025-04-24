<?php
header('Content-Type: application/json');
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_POST['actividad_id'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o sesión no iniciada']);
    exit();
}

$actividad_id = $_POST['actividad_id'];
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$fecha = $_POST['fecha'];
$usuario_id = $_SESSION['usuario_id'];

// Validar que la actividad existe y pertenece al usuario actual
// (Podríamos hacer una validación más completa verificando que la actividad pertenece a un cronograma del usuario)
$query_validacion = "SELECT a.id_actividad
                    FROM actividad a
                    JOIN cronograma c ON a.cronograma_id = c.cronograma_id
                    JOIN planeacion p ON c.cronograma_id = p.cronograma_id
                    WHERE a.id_actividad = ? AND p.solicitante_id = ?";

$stmt_validacion = $conexion->prepare($query_validacion);
$stmt_validacion->bind_param("is", $actividad_id, $usuario_id);
$stmt_validacion->execute();
$resultado = $stmt_validacion->get_result();

if ($resultado->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No tiene permiso para editar esta actividad']);
    $stmt_validacion->close();
    $conexion->close();
    exit();
}

$stmt_validacion->close();

// Actualizar la actividad
$query = "UPDATE actividad SET 
          nombre = ?, 
          descripcion = ?, 
          fecha = ? 
          WHERE id_actividad = ?";

$stmt = $conexion->prepare($query);
$stmt->bind_param("sssi", $nombre, $descripcion, $fecha, $actividad_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Actividad actualizada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la actividad: ' . $stmt->error]);
}

$stmt->close();
$conexion->close();
?>
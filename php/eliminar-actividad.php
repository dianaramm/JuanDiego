<?php
header('Content-Type: application/json');
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o sesión no iniciada']);
    exit();
}

$actividad_id = $_POST['id'];
$usuario_id = $_SESSION['usuario_id'];

// Validar que la actividad existe y pertenece al usuario actual
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
    echo json_encode(['success' => false, 'message' => 'No tiene permiso para eliminar esta actividad']);
    $stmt_validacion->close();
    $conexion->close();
    exit();
}

$stmt_validacion->close();

// Eliminar la actividad
$query = "DELETE FROM actividad WHERE id_actividad = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $actividad_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Actividad eliminada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar la actividad: ' . $stmt->error]);
}

$stmt->close();
$conexion->close();
?>
<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once 'conexion.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception('Sesión no iniciada');
    }

    if (!isset($_GET['id'])) {
        throw new Exception('ID de actividad no proporcionado');
    }

    $actividad_id = $_GET['id'];
    $usuario_id = $_SESSION['usuario_id'];

    // Consulta usando el nombre de columna correcto id_actividad
    $query = "SELECT id_actividad, nombre, descripcion, fecha 
              FROM actividad 
              WHERE id_actividad = ?";

    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $actividad_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al obtener la actividad');
    }

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        throw new Exception('Actividad no encontrada');
    }

    $actividad = $resultado->fetch_assoc();

    $stmt->close();
    $conexion->close();

    echo json_encode([
        'success' => true,
        'actividad' => $actividad
    ]);

} catch (Exception $e) {
    error_log("Error en obtener-actividad.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
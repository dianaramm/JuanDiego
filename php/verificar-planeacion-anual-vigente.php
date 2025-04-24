<?php
header('Content-Type: application/json');
require_once 'conexion.php';
session_start();

try {
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception('Usuario no autenticado');
    }

    $usuario_id = $_SESSION['usuario_id'];

    $query = "SELECT planeacion_id 
              FROM planeacion 
              WHERE solicitante_id = ? 
              AND validez_id = 1 
              AND estatus_id IN (1,2,3,4,5,6)";

    $stmt = $conexion->prepare($query);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta');
    }

    $stmt->bind_param("s", $usuario_id);
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta');
    }

    $resultado = $stmt->get_result();
    
    echo json_encode([
        'exists' => $resultado->num_rows > 0,
        'success' => true
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conexion)) {
        $conexion->close();
    }
}
?>
<?php
require_once 'conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['estatus'])) {
    $id = intval($_POST['id']);
    $estatus = intval($_POST['estatus']);
    
    $query = "UPDATE planeacion SET estatus_id = ? WHERE planeacion_id = ? AND estatus_id = 3";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ii", $estatus, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos']);
}

$conexion->close();
?>
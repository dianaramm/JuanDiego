<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_POST['planeacion_id'])) {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

$planeacion_id = $_POST['planeacion_id'];
$usuario_id = $_SESSION['usuario_id'];

// Verificar propiedad y estado
$verificacion = $conexion->prepare("SELECT planeacion_id FROM planeacion 
                                   WHERE planeacion_id = ? AND solicitante_id = ? 
                                   AND estatus_id = 6");
$verificacion->bind_param("is", $planeacion_id, $usuario_id);
$verificacion->execute();

if ($verificacion->get_result()->num_rows === 0) {
    echo json_encode(['error' => 'No autorizado para editar esta planeación']);
    exit();
}

// Determinar si es una actualización normal o un envío
$estatus_id = isset($_POST['accion']) && $_POST['accion'] === 'enviar' ? 3 : 6;

// Actualizar datos
$query = "UPDATE planeacion SET 
          nombre = ?,
          tipo = ?,
          importancia = ?,
          descripcion = ?,
          objetivo = ?,
          estatus_id = ?
          WHERE planeacion_id = ?";

$stmt = $conexion->prepare($query);
$stmt->bind_param("sssssii", 
    $_POST['nombre-planeacion'],
    $_POST['tipo-planeacion'],
    $_POST['importancia'],
    $_POST['descripcion'],
    $_POST['objetivo-general'],
    $estatus_id,
    $planeacion_id
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Error al actualizar la planeación']);
}

$stmt->close();
$conexion->close();
?>
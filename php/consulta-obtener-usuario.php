<?php
require_once 'conexion.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
    exit;
}

$usuario_id = $_GET['id'];

$query = "SELECT 
    u.usuario_id,
    u.nombre,
    u.apellido_paterno,
    u.apellido_materno,
    u.correo,
    u.telefono,
    u.area_id,
    u.cargo,
    l.contraseña
FROM usuario u
LEFT JOIN login l ON u.usuario_id = l.usuario_id
WHERE u.usuario_id = ?";

$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();
    echo json_encode(['success' => true, 'usuario' => $usuario]);
} else {
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
}

$stmt->close();
$conexion->close();
?>
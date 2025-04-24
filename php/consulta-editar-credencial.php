<?php
require_once 'conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

try {
    $conexion->begin_transaction();

    // Obtener y validar los datos POST
    if (!isset($_POST['usuario_id']) || empty($_POST['usuario_id'])) {
        throw new Exception("ID de usuario no proporcionado");
    }

    $usuario_id = $_POST['usuario_id'];
    $nombre = $_POST['nombre'] ?? '';
    $apellido_paterno = $_POST['apellido-paterno'] ?? '';
    $apellido_materno = $_POST['apellido-materno'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $area = $_POST['area'] ?? '';
    $cargo = $_POST['tipo-usuario'] ?? '';
    $contraseña = $_POST['password'] ?? '';

    // Validaciones básicas
    if (empty($nombre) || empty($apellido_paterno) || empty($correo)) {
        throw new Exception("Faltan campos requeridos");
    }
    
    // Validar formato de teléfono (10 dígitos)
    if (!preg_match('/^\d{10}$/', $telefono)) {
        throw new Exception("El teléfono debe contener exactamente 10 dígitos");
    }
    
    // Validar formato de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Formato de correo electrónico inválido");
    }

    // Actualizar tabla usuario
    $query_usuario = "UPDATE usuario SET 
        nombre = ?,
        apellido_paterno = ?,
        apellido_materno = ?,
        telefono = ?,
        correo = ?,
        area_id = ?,
        cargo = ?
        WHERE usuario_id = ?";

    $stmt = $conexion->prepare($query_usuario);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta de usuario: " . $conexion->error);
    }

    $stmt->bind_param("ssssssss", 
        $nombre, 
        $apellido_paterno, 
        $apellido_materno, 
        $telefono, 
        $correo, 
        $area, 
        $cargo, 
        $usuario_id
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al actualizar usuario: " . $stmt->error);
    }

    // Actualizar contraseña si se proporcionó una nueva
    if (!empty($contraseña)) {
        $query_login = "UPDATE login SET contraseña = ? WHERE usuario_id = ?";
        $stmt_login = $conexion->prepare($query_login);
        if (!$stmt_login) {
            throw new Exception("Error en la preparación de la consulta de login: " . $conexion->error);
        }

        $stmt_login->bind_param("ss", $contraseña, $usuario_id);
        if (!$stmt_login->execute()) {
            throw new Exception("Error al actualizar contraseña: " . $stmt_login->error);
        }
        
        $stmt_login->close();
    }

    $conexion->commit();
    echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conexion->close();
}
?>
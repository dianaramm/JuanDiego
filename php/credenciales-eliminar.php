<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario_id'])) {
    $usuario_id = $_POST['usuario_id'];
    
    // Comenzar transacción
    $conexion->begin_transaction();
    
    try {
        // Eliminar de login
        $sql_login = "DELETE FROM login WHERE usuario_id = ?";
        $stmt_login = $conexion->prepare($sql_login);
        $stmt_login->bind_param("s", $usuario_id);
        $stmt_login->execute();
        
        // Actualizar estatus en usuario a inactivo (2)
        $sql_usuario = "UPDATE usuario SET estatus_id = 2 WHERE usuario_id = ?";
        $stmt_usuario = $conexion->prepare($sql_usuario);
        $stmt_usuario->bind_param("s", $usuario_id);
        $stmt_usuario->execute();
        
        // Confirmar transacción
        $conexion->commit();
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        // Revertir cambios si hay error
        $conexion->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } finally {
        // Cerrar las conexiones
        if (isset($stmt_login)) $stmt_login->close();
        if (isset($stmt_usuario)) $stmt_usuario->close();
        $conexion->close();
    }
}
?>
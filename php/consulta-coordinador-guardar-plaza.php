<?php
/**
 * Script para gestionar solicitudes de plazas (guardar, actualizar, enviar, eliminar)
 */
header('Content-Type: application/json');
session_start();
require_once 'conexion.php';

try {
    // Verificar que el usuario esté autenticado
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception('Usuario no autenticado');
    }
    
    // Verificar que exista una acción
    if (!isset($_POST['accion'])) {
        throw new Exception('No se especificó ninguna acción');
    }
    
    $accion = $_POST['accion'];
    $usuario_id = $_SESSION['usuario_id'];
    
    // Iniciar transacción para garantizar la integridad de datos
    $conexion->begin_transaction();
    
    switch ($accion) {
        case 'guardar':
            procesarGuardar($conexion, $usuario_id);
            break;
            
        case 'actualizar':
            procesarActualizar($conexion, $usuario_id);
            break;
            
        case 'enviar':
            procesarEnviar($conexion, $usuario_id);
            break;
            
        case 'eliminar':
            procesarEliminar($conexion, $usuario_id);
            break;
            
        default:
            throw new Exception('Acción no reconocida');
    }
    
    // Si llegamos aquí, todo ha ido bien
    $conexion->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Operación completada con éxito'
    ]);
    
} catch (Exception $e) {
    // En caso de error, revertir cambios
    if (isset($conexion) && $conexion->connect_errno === 0) {
        $conexion->rollback();
    }
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    // Cerrar conexión
    if (isset($conexion) && $conexion->connect_errno === 0) {
        $conexion->close();
    }
}

/**
 * Procesa la acción de guardar una nueva solicitud
 */
function procesarGuardar($conexion, $usuario_id) {
    // Validar datos requeridos
    if (!isset($_POST['puesto']) || !isset($_POST['justificacion'])) {
        throw new Exception('Faltan datos requeridos');
    }
    
    // Obtener y sanear los datos
    $puesto = trim(htmlspecialchars($_POST['puesto']));
    $justificacion = trim(htmlspecialchars($_POST['justificacion']));
    
    // Validaciones adicionales
    if (empty($puesto) || empty($justificacion)) {
        throw new Exception('Todos los campos son obligatorios');
    }
    
    if (strlen($puesto) < 3 || strlen($puesto) > 100) {
        throw new Exception('El puesto debe tener entre 3 y 100 caracteres');
    }
    
    if (strlen($justificacion) < 10) {
        throw new Exception('La justificación debe tener al menos 10 caracteres');
    }
    
    // 1. Crear registro de aprobación (estatus 6 = borrador)
    $query_aprobacion = "INSERT INTO aprobacion (fecha, solicitante_usuario, estatus_id) 
                         VALUES (CURDATE(), ?, 6)";
    
    $stmt_aprobacion = $conexion->prepare($query_aprobacion);
    if (!$stmt_aprobacion) {
        throw new Exception('Error en la preparación de la consulta de aprobación: ' . $conexion->error);
    }
    
    $stmt_aprobacion->bind_param("s", $usuario_id);
    if (!$stmt_aprobacion->execute()) {
        throw new Exception('Error al guardar la aprobación: ' . $stmt_aprobacion->error);
    }
    
    $aprobacion_id = $conexion->insert_id;
    $stmt_aprobacion->close();
    
    // 2. Crear registro de solicitud de plaza
    $query_plaza = "INSERT INTO solicitud_plaza (puesto, justificacion, aprobacion_id) 
                    VALUES (?, ?, ?)";
    
    $stmt_plaza = $conexion->prepare($query_plaza);
    if (!$stmt_plaza) {
        throw new Exception('Error en la preparación de la consulta de plaza: ' . $conexion->error);
    }
    
    $stmt_plaza->bind_param("ssi", $puesto, $justificacion, $aprobacion_id);
    if (!$stmt_plaza->execute()) {
        throw new Exception('Error al guardar la solicitud de plaza: ' . $stmt_plaza->error);
    }
    
    $stmt_plaza->close();
}

/**
 * Procesa la acción de actualizar una solicitud existente
 */
function procesarActualizar($conexion, $usuario_id) {
    // Validar datos requeridos
    if (!isset($_POST['plaza_id']) || !isset($_POST['aprobacion_id']) || 
        !isset($_POST['puesto']) || !isset($_POST['justificacion'])) {
        throw new Exception('Faltan datos requeridos');
    }
    
    // Obtener y sanear los datos
    $plaza_id = intval($_POST['plaza_id']);
    $aprobacion_id = intval($_POST['aprobacion_id']);
    $puesto = trim(htmlspecialchars($_POST['puesto']));
    $justificacion = trim(htmlspecialchars($_POST['justificacion']));
    
    // Validaciones adicionales
    if (empty($puesto) || empty($justificacion) || $plaza_id <= 0 || $aprobacion_id <= 0) {
        throw new Exception('Todos los campos son obligatorios');
    }
    
    if (strlen($puesto) < 3 || strlen($puesto) > 100) {
        throw new Exception('El puesto debe tener entre 3 y 100 caracteres');
    }
    
    if (strlen($justificacion) < 10) {
        throw new Exception('La justificación debe tener al menos 10 caracteres');
    }
    
    // Verificar que la solicitud pertenezca al usuario y esté en estado borrador
    $query_verificar = "SELECT a.aprobacion_id 
                        FROM aprobacion a
                        INNER JOIN solicitud_plaza sp ON a.aprobacion_id = sp.aprobacion_id  
                        WHERE sp.plaza_id = ? 
                        AND a.solicitante_usuario = ? 
                        AND a.estatus_id = 6";
    
    $stmt_verificar = $conexion->prepare($query_verificar);
    if (!$stmt_verificar) {
        throw new Exception('Error en la preparación de la consulta de verificación: ' . $conexion->error);
    }
    
    $stmt_verificar->bind_param("is", $plaza_id, $usuario_id);
    $stmt_verificar->execute();
    $resultado = $stmt_verificar->get_result();
    
    if ($resultado->num_rows === 0) {
        throw new Exception('No tiene permisos para editar esta solicitud o la solicitud no está en estado borrador');
    }
    
    $stmt_verificar->close();
    
    // Actualizar la solicitud de plaza
    $query_actualizar = "UPDATE solicitud_plaza SET puesto = ?, justificacion = ? WHERE plaza_id = ?";
    
    $stmt_actualizar = $conexion->prepare($query_actualizar);
    if (!$stmt_actualizar) {
        throw new Exception('Error en la preparación de la consulta de actualización: ' . $conexion->error);
    }
    
    $stmt_actualizar->bind_param("ssi", $puesto, $justificacion, $plaza_id);
    if (!$stmt_actualizar->execute()) {
        throw new Exception('Error al actualizar la solicitud: ' . $stmt_actualizar->error);
    }
    
    $stmt_actualizar->close();
}

/**
 * Procesa la acción de enviar una solicitud para aprobación
 */
function procesarEnviar($conexion, $usuario_id) {
    // Validar datos requeridos
    if (!isset($_POST['id'])) {
        throw new Exception('Falta ID de aprobación');
    }
    
    $aprobacion_id = intval($_POST['id']);
    
    // Verificar que la solicitud pertenezca al usuario y esté en estado borrador
    $query_verificar = "SELECT aprobacion_id 
                       FROM aprobacion 
                       WHERE aprobacion_id = ? 
                       AND solicitante_usuario = ? 
                       AND estatus_id = 6";
    
    $stmt_verificar = $conexion->prepare($query_verificar);
    if (!$stmt_verificar) {
        throw new Exception('Error en la preparación de la consulta de verificación: ' . $conexion->error);
    }
    
    $stmt_verificar->bind_param("is", $aprobacion_id, $usuario_id);
    $stmt_verificar->execute();
    $resultado = $stmt_verificar->get_result();
    
    if ($resultado->num_rows === 0) {
        throw new Exception('No tiene permisos para enviar esta solicitud o la solicitud no está en estado borrador');
    }
    
    $stmt_verificar->close();
    
    // Cambiar el estatus a pendiente (3)
    $query_actualizar = "UPDATE aprobacion SET estatus_id = 3 WHERE aprobacion_id = ?";
    
    $stmt_actualizar = $conexion->prepare($query_actualizar);
    if (!$stmt_actualizar) {
        throw new Exception('Error en la preparación de la consulta de actualización: ' . $conexion->error);
    }
    
    $stmt_actualizar->bind_param("i", $aprobacion_id);
    if (!$stmt_actualizar->execute()) {
        throw new Exception('Error al enviar la solicitud: ' . $stmt_actualizar->error);
    }
    
    $stmt_actualizar->close();
}

/**
 * Procesa la acción de eliminar una solicitud
 */
function procesarEliminar($conexion, $usuario_id) {
    // Validar datos requeridos
    if (!isset($_POST['id'])) {
        throw new Exception('Falta ID de aprobación');
    }
    
    $aprobacion_id = intval($_POST['id']);
    
    // Verificar que la solicitud pertenezca al usuario y esté en estado borrador
    $query_verificar = "SELECT aprobacion_id 
                       FROM aprobacion 
                       WHERE aprobacion_id = ? 
                       AND solicitante_usuario = ? 
                       AND estatus_id = 6";
    
    $stmt_verificar = $conexion->prepare($query_verificar);
    if (!$stmt_verificar) {
        throw new Exception('Error en la preparación de la consulta de verificación: ' . $conexion->error);
    }
    
    $stmt_verificar->bind_param("is", $aprobacion_id, $usuario_id);
    $stmt_verificar->execute();
    $resultado = $stmt_verificar->get_result();
    
    if ($resultado->num_rows === 0) {
        throw new Exception('No tiene permisos para eliminar esta solicitud o la solicitud no está en estado borrador');
    }
    
    $stmt_verificar->close();
    
    // Eliminar la solicitud (primero la solicitud_plaza y luego la aprobación)
    $query_eliminar_plaza = "DELETE FROM solicitud_plaza WHERE aprobacion_id = ?";
    
    $stmt_eliminar_plaza = $conexion->prepare($query_eliminar_plaza);
    if (!$stmt_eliminar_plaza) {
        throw new Exception('Error en la preparación de la consulta de eliminación: ' . $conexion->error);
    }
    
    $stmt_eliminar_plaza->bind_param("i", $aprobacion_id);
    if (!$stmt_eliminar_plaza->execute()) {
        throw new Exception('Error al eliminar la solicitud de plaza: ' . $stmt_eliminar_plaza->error);
    }
    
    $stmt_eliminar_plaza->close();
    
    // Eliminar la aprobación
    $query_eliminar_aprobacion = "DELETE FROM aprobacion WHERE aprobacion_id = ?";
    
    $stmt_eliminar_aprobacion = $conexion->prepare($query_eliminar_aprobacion);
    if (!$stmt_eliminar_aprobacion) {
        throw new Exception('Error en la preparación de la consulta de eliminación: ' . $conexion->error);
    }
    
    $stmt_eliminar_aprobacion->bind_param("i", $aprobacion_id);
    if (!$stmt_eliminar_aprobacion->execute()) {
        throw new Exception('Error al eliminar la aprobación: ' . $stmt_eliminar_aprobacion->error);
    }
    
    $stmt_eliminar_aprobacion->close();
}
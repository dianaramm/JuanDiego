<?php
header('Content-Type: application/json');
require_once 'conexion.php';

// Iniciar sesión para verificar usuario autenticado
session_start();

try {
    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception('Usuario no autenticado');
    }
    
    // Verificar que los datos POST necesarios estén presentes
    if (!isset($_POST['aprobacion_id']) || !isset($_POST['accion'])) {
        throw new Exception('Faltan parámetros requeridos');
    }
    
    $aprobacion_id = intval($_POST['aprobacion_id']);
    $accion = $_POST['accion'];
    $usuario_id = $_SESSION['usuario_id'];
    
    // Determinar el estatus_id basado en la acción
    $estatus_id = ($accion === 'aprobar') ? 4 : 5;
    
    // Iniciar transacción para garantizar integridad
    $conexion->begin_transaction();
    
    // Verificar que la aprobación exista y tenga estatus pendiente (3)
    $verificacion = $conexion->prepare("SELECT aprobacion_id FROM aprobacion WHERE aprobacion_id = ? AND estatus_id = 3");
    $verificacion->bind_param("i", $aprobacion_id);
    $verificacion->execute();
    $resultado = $verificacion->get_result();
    
    if ($resultado->num_rows === 0) {
        throw new Exception('La solicitud no existe o ya ha sido procesada');
    }
    
    // Actualizar el estatus de la aprobación
    $stmt = $conexion->prepare("UPDATE aprobacion SET estatus_id = ?, aprobador_usuario = ? WHERE aprobacion_id = ?");
    $stmt->bind_param("isi", $estatus_id, $usuario_id, $aprobacion_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al actualizar la solicitud: ' . $stmt->error);
    }
    
    // Confirmar la transacción
    $conexion->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Solicitud procesada correctamente'
    ]);
    
} catch (Exception $e) {
    // Revertir cambios en caso de error
    if (isset($conexion)) {
        $conexion->rollback();
    }
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    // Cerrar conexiones
    if (isset($verificacion)) $verificacion->close();
    if (isset($stmt)) $stmt->close();
    if (isset($conexion)) $conexion->close();
}
?>
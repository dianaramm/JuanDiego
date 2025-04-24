<?php
// Evitar cualquier salida antes de los headers
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Asegurarnos de que solo se envíe JSON
header('Content-Type: application/json');

try {
    require_once 'conexion.php';
    
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar sesión
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception('Sesión no iniciada');
    }

    $usuario_id = $_SESSION['usuario_id'];

    // Verificar que los datos POST existan
    if (!isset($_POST['nombre']) || !isset($_POST['descripcion']) || !isset($_POST['fecha'])) {
        throw new Exception('Faltan datos requeridos');
    }

    // Obtener y validar datos
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $fecha = trim($_POST['fecha']);

    if (empty($nombre) || empty($descripcion) || empty($fecha)) {
        throw new Exception('Todos los campos son obligatorios');
    }

    // Obtener cronograma_id
    $query_cronograma = "SELECT cronograma_id 
                        FROM planeacion 
                        WHERE solicitante_id = ? 
                        AND validez_id = 1
                        ORDER BY fecha_creacion DESC
                        LIMIT 1";

    $stmt_cronograma = $conexion->prepare($query_cronograma);
    $stmt_cronograma->bind_param("i", $usuario_id);
    
    if (!$stmt_cronograma->execute()) {
        throw new Exception('Error al consultar el cronograma: ' . $stmt_cronograma->error);
    }

    $resultado = $stmt_cronograma->get_result();

    if ($resultado->num_rows === 0) {
        throw new Exception('No se encontró un cronograma válido para este usuario');
    }

    $row = $resultado->fetch_assoc();
    $cronograma_id = $row['cronograma_id'];
    $stmt_cronograma->close();

    // Insertar actividad
    $query = "INSERT INTO actividad (nombre, descripcion, fecha, cronograma_id) 
              VALUES (?, ?, ?, ?)";

    $stmt = $conexion->prepare($query);
    $stmt->bind_param("sssi", $nombre, $descripcion, $fecha, $cronograma_id);

    if (!$stmt->execute()) {
        throw new Exception('Error al insertar la actividad: ' . $stmt->error);
    }

    $actividad_id = $stmt->insert_id;
    $stmt->close();
    $conexion->close();

    // Devolver respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Actividad registrada correctamente',
        'data' => [
            'id' => $actividad_id,
            'cronograma_id' => $cronograma_id
        ]
    ]);

} catch (Exception $e) {
    // Log del error para debugging
    error_log("Error en registrar-actividad.php: " . $e->getMessage());
    
    // Devolver error en formato JSON
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
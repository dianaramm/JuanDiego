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

    $usuario_id = $_SESSION['usuario_id'];

    // Primero obtener el cronograma_id válido actual
    $query_cronograma = "SELECT cronograma_id 
                        FROM planeacion 
                        WHERE solicitante_id = ?
                        ORDER BY fecha_creacion DESC 
                        LIMIT 1";

    $stmt_cronograma = $conexion->prepare($query_cronograma);
    $stmt_cronograma->bind_param("s", $usuario_id);
    
    if (!$stmt_cronograma->execute()) {
        throw new Exception('Error al obtener el cronograma');
    }

    $resultado_cronograma = $stmt_cronograma->get_result();
    
    if ($resultado_cronograma->num_rows === 0) {
        throw new Exception('No se encontró un cronograma válido');
    }

    $row_cronograma = $resultado_cronograma->fetch_assoc();
    $cronograma_id = $row_cronograma['cronograma_id'];
    $stmt_cronograma->close();

    // Obtener las actividades del cronograma actual, usando el nombre de columna correcto
    $query = "SELECT id_actividad, nombre, descripcion, fecha 
             FROM actividad 
             WHERE cronograma_id = ?
             ORDER BY fecha ASC";

    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $cronograma_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al obtener las actividades');
    }

    $resultado = $stmt->get_result();
    $actividades = [];

    while ($row = $resultado->fetch_assoc()) {
        $actividades[] = [
            'id_actividad' => $row['id_actividad'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'fecha' => $row['fecha']
        ];
    }

    $stmt->close();
    $conexion->close();

    echo json_encode([
        'success' => true,
        'actividades' => $actividades
    ]);

} catch (Exception $e) {
    error_log("Error en obtener-actividades.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header('Location: coordinador-estatus-planeacion-anual.php');
    exit();
}

$planeacion_id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];

// Usar los nombres exactos de las columnas de la base de datos
$query = "SELECT planeacion_id, nombre, tipo, importancia, 
          descripcion, objetivo, estatus_id, cronograma_id, solicitante_id
          FROM planeacion
          WHERE planeacion_id = ? 
          AND solicitante_id = ?";

$stmt = $conexion->prepare($query);
$stmt->bind_param("is", $planeacion_id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header('Location: coordinador-estatus-planeacion-anual.php');
    exit();
}

$planeacion = $resultado->fetch_assoc();

// Mapear los nombres de las columnas a los nombres del formulario
$response = array(
    'planeacion_id' => $planeacion['planeacion_id'],
    'nombre-planeacion' => $planeacion['nombre'],     
    'tipo-planeacion' => $planeacion['tipo'],       
    'importancia' => $planeacion['importancia'],     
    'descripcion' => $planeacion['descripcion'],      
    'objetivo-general' => $planeacion['objetivo'],    
    'estatus_id' => $planeacion['estatus_id'],
    'cronograma_id' => $planeacion['cronograma_id']
);

header('Content-Type: application/json');
echo json_encode($response);

$stmt->close();
$conexion->close();
?>
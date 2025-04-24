<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_GET['cronograma_id'])) {
    exit();
}

$cronograma_id = $_GET['cronograma_id'];

$query = "SELECT 
    actividad_id,
    nombre,
    descripcion,
    fecha
FROM actividad 
WHERE cronograma_id = ?
ORDER BY fecha ASC";

$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $cronograma_id);
$stmt->execute();
$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    echo "<tr>";
    echo "<td class='text-center'><input type='radio' name='actividad' value='" . $fila['actividad_id'] . "' class='seleccion-actividad'></td>";
    echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
    echo "<td>" . htmlspecialchars($fila['descripcion']) . "</td>";
    echo "<td>" . date('d/m/Y', strtotime($fila['fecha'])) . "</td>";
    echo "</tr>";
}

$stmt->close();
$conexion->close();
?>
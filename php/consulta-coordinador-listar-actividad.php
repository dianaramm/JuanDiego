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
    echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
    echo "<td>" . htmlspecialchars($fila['descripcion']) . "</td>";
    echo "<td>" . date('d/m/Y', strtotime($fila['fecha'])) . "</td>";
    echo "<td class='acciones'>";
    echo "<button onclick='editarActividad(" . $fila['actividad_id'] . ")' class='boton-editar'>";
    echo "<img src='../img/icono-editar.png' alt='Editar' class='icono-accion'>";
    echo "</button>";
    echo "<button onclick='eliminarActividad(" . $fila['actividad_id'] . ")' class='boton-eliminar'>";
    echo "<img src='../img/icono-eliminar.png' alt='Eliminar' class='icono-accion'>";
    echo "</button>";
    echo "</td>";
    echo "</tr>";
}

$stmt->close();
$conexion->close();
?>
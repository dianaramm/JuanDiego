<?php
session_start();
header('Content-Type: application/json');

// Verificar si hay datos en la sesión
if (isset($_SESSION['nombre'], $_SESSION['apellido_paterno'], $_SESSION['apellido_materno'])) {
    $nombreCompleto = $_SESSION['nombre'] . ' ' . $_SESSION['apellido_paterno'] . ' ' . $_SESSION['apellido_materno'];
    echo json_encode(['usuario' => $nombreCompleto]);
} else {
    echo json_encode(['usuario' => 'Invitado']);
}
?>


<?php
session_start();
header('Content-Type: application/json');

// Verificar si existe la sesión y no ha expirado
$sesion_activa = isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);

echo json_encode([
    'sesion_activa' => $sesion_activa
]);
?>


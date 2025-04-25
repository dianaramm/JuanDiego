
<?php
session_start();
header('Content-Type: application/json');

// Verificar si hay datos en la sesión
if (isset($_SESSION['nombre'], $_SESSION['apellido_paterno'], $_SESSION['apellido_materno'])) {
    $nombreCompleto = $_SESSION['nombre'] . ' ' . $_SESSION['apellido_paterno'] . ' ' . $_SESSION['apellido_materno'];
    // Incluir tipo_id en la respuesta
    $tipo_id = isset($_SESSION['tipo_id']) ? $_SESSION['tipo_id'] : null;
    echo json_encode([
        'usuario' => $nombreCompleto,
        'tipo_id' => $tipo_id
    ]);
} else {
    echo json_encode(['usuario' => 'Invitado', 'tipo_id' => null]);
}
?>

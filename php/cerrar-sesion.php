<?php
session_start();

// Limpiar todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Destruir la sesión
session_destroy();

// Asegurar que los headers no se han enviado antes
if (!headers_sent()) {
    // Redirigir al index
    header("Location: ../index.html");
    exit();
} else {
    // Si los headers ya se enviaron, usar JavaScript
    echo "<script>window.location.href = '../index.html';</script>";
    exit();
}
?>


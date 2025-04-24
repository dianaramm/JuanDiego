<?php
// Incluir archivo de conexión
include 'conexion.php';
session_start();

// Función para registrar intento de inicio de sesión
function registrarIntento($ip) {
    // Verificar si existe el archivo de intentos
    $archivo_intentos = __DIR__ . '/login_attempts.json';
    $tiempo_actual = time();
    $intentos = [];
    
    if (file_exists($archivo_intentos)) {
        $intentos = json_decode(file_get_contents($archivo_intentos), true);
    }
    
    // Limpiar intentos antiguos (más de 30 minutos)
    foreach ($intentos as $ip_addr => $datos) {
        if ($tiempo_actual - $datos['timestamp'] > 2000) { 
            unset($intentos[$ip_addr]);
        }
    }
    
    // Registrar o actualizar intento
    if (!isset($intentos[$ip])) {
        $intentos[$ip] = [
            'count' => 1,
            'timestamp' => $tiempo_actual
        ];
    } else {
        $intentos[$ip]['count']++;
        $intentos[$ip]['timestamp'] = $tiempo_actual;
    }
    
    // Guardar intentos actualizados
    file_put_contents($archivo_intentos, json_encode($intentos));
    
    return $intentos[$ip]['count'];
}

// Verificar intentos de inicio de sesión
$ip_cliente = $_SERVER['REMOTE_ADDR'];
$intentos = registrarIntento($ip_cliente);

// Limitar  intentos en 30 minutos
if ($intentos > 10) {
    header("Location: ../index.html?error=demasiados_intentos");
    exit;
}

// Verificar que se enviaron todos los campos requeridos
if (!isset($_POST['correo']) || !isset($_POST['clave']) || !isset($_POST['g-recaptcha-response'])) {
    header("Location: ../index.html?error=campos_incompletos");
    exit;
}

// Verificar reCAPTCHA
$recaptcha_secret = "6LeijworAAAAAIDFeAmaLduZrjT7bL3Qb3fD3dRo"; 
$recaptcha_response = $_POST['g-recaptcha-response'];

// Verificación del captcha con Google
$verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$recaptcha_secret.'&response='.$recaptcha_response);
$response_data = json_decode($verify_response);

// Si el CAPTCHA no es válido, redirigir con error
if (!$response_data->success) {
    header("Location: ../index.html?error=captcha_invalido");
    exit;
}

// Recibir datos del formulario
$entrada = $_POST['correo'];
$clave = $_POST['clave'];

// Prevenir inyecciones SQL usando sentencias preparadas
$consulta_login = "
    SELECT 
        login.usuario_id, 
        login.contraseña, 
        usuario.tipo_id, 
        usuario.estatus_id 
    FROM 
        login 
    INNER JOIN 
        usuario 
    ON 
        login.usuario_id = usuario.usuario_id
    WHERE 
        login.usuario_id = ?
";

// Preparar la consulta
$stmt = $conexion->prepare($consulta_login);
if (!$stmt) {
    header("Location: ../index.html?error=error_servidor");
    exit;
}

// Vincular parámetros y ejecutar
$stmt->bind_param("s", $entrada);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    
    // Verificar estatus y contraseña
    if ($fila['estatus_id'] == 1 && $clave == $fila['contraseña']) {
        // Login exitoso - reiniciar los intentos de inicio de sesión
        $archivo_intentos = __DIR__ . '/login_attempts.json';
        if (file_exists($archivo_intentos)) {
            $intentos = json_decode(file_get_contents($archivo_intentos), true);
            if (isset($intentos[$ip_cliente])) {
                unset($intentos[$ip_cliente]);
                file_put_contents($archivo_intentos, json_encode($intentos));
            }
        }
        
        // Establecer variables de sesión
        $_SESSION['usuario_id'] = $fila['usuario_id'];
        $_SESSION['tipo_id'] = $fila['tipo_id'];

        // Obtener nombre completo del usuario usando sentencias preparadas
        $consulta_nombre = "SELECT nombre, apellido_paterno, apellido_materno FROM usuario WHERE usuario_id = ?";
        $stmt_nombre = $conexion->prepare($consulta_nombre);
        $stmt_nombre->bind_param("s", $fila['usuario_id']);
        $stmt_nombre->execute();
        $resultado_nombre = $stmt_nombre->get_result();
        
        if ($resultado_nombre->num_rows > 0) {
            $fila_nombre = $resultado_nombre->fetch_assoc();
            $_SESSION['nombre'] = $fila_nombre['nombre'];
            $_SESSION['apellido_paterno'] = $fila_nombre['apellido_paterno'];
            $_SESSION['apellido_materno'] = $fila_nombre['apellido_materno'];
        }
        $stmt_nombre->close();

        // Guardar usuario_id en usuario_id.php (manera más segura)
        $usuario_id = $fila['usuario_id'];
        $contenido = "<?php\n\$usuario_id = '" . addslashes($usuario_id) . "';\n?>";
        file_put_contents('usuario_id.php', $contenido);

        // Redirigir según tipo_usuario
        switch ($fila['tipo_id']) {
            case 1:
                header("Location: ../html/superusuario.html");
                break;
            case 2:
                header("Location: ../html/administracion.html");
                break;
            case 3:
                header("Location: ../html/finanzas.html");
                break;
            case 4:
                header("Location: ../html/coordinador.html");
                break;
            default:
                header("Location: ../index.html");
                break;
        }
    } else {
        // Usuario inactivo o contraseña incorrecta
        header("Location: ../index.html?error=acceso_denegado");
    }
} else {
    // Usuario no encontrado
    header("Location: ../index.html?error=usuario_no_encontrado");
}

$stmt->close();
$conexion->close();
?>
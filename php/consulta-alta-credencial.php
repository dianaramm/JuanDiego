<?php 
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST['usuario'];
    
    // Primero verificar si el usuario ya existe
    $check_query = "SELECT usuario_id FROM usuario WHERE usuario_id = ?";
    $check_stmt = $conexion->prepare($check_query);
    $check_stmt->bind_param("s", $usuario_id);
    $check_stmt->execute();
    
    if($check_stmt->get_result()->num_rows > 0) {
        // Usuario duplicado - Redirigir con parámetros para notificación
        header("Location: credenciales-alta.php?status=error&msg=duplicado");
        exit();
    }
    
    // Si no existe, continuar con el resto del código original
    $nombre = $_POST['nombre'];
    $apellido_paterno = $_POST['apellido-paterno'];
    $apellido_materno = $_POST['apellido-materno'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $area = $_POST['area'];
    $cargo = $_POST['tipo-usuario'];
    $clave = $_POST['password'];

    // El resto de tu código switch y lógica permanece igual...
    switch ($cargo) {
        case 'Administrador de planeacion':
            $tipo_id = 2;
            break;
        case 'Administrador de finanzas':
            $tipo_id = 3;
            break;
        case 'Coordinador':
            $tipo_id = 4;
            break;
        default:
            $tipo_id = 0;
    }

    // Mapeo completo de todas las áreas disponibles en el formulario
    switch ($area) {
        case 'Sistemas':
            $area_id = 1;
            break;
        case 'Gestion de planeación':
            $area_id = 2;
            break;
        case 'Finanzas':
            $area_id = 3;
            break;
        case 'Academia de belleza':
            $area_id = 4;
            break;
        case 'Academia cuidado_salud':
            $area_id = 5;
            break;
        case 'Apoyo psicologico':
            $area_id = 6;
            break;
        case 'Articulos de belleza y aseo':
            $area_id = 7;
            break;
        case 'Banco de alimentos':
            $area_id = 8;
            break;
        case 'Bazaar':
            $area_id = 9;
            break;
        case 'Clínica dental':
            $area_id = 10;
            break;
        case 'Comedor comunitario':
            $area_id = 11;
            break;
        case 'Consulta medica':
            $area_id = 12;
            break;
        case 'Escuela de computacion':
            $area_id = 13;
            break;
        case 'Escuela de gastronomia':
            $area_id = 14;
            break;
        case 'Estimulacion temprana':
            $area_id = 15;
            break;
        case 'Farmacia similares':
            $area_id = 16;
            break;
        case 'Guarderia':
            $area_id = 17;
            break;
        case 'Preescolar':
            $area_id = 18;
            break;
        case 'Tortilleria':
            $area_id = 19;
            break;
        default:
            $area_id = 1; // Valor por defecto en caso de que no coincida ninguna área
    }

    $sql = "INSERT INTO usuario (usuario_id, nombre, apellido_paterno, apellido_materno, correo, telefono, area_id, estatus_id, cargo, tipo_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssisi", $usuario_id, $nombre, $apellido_paterno, $apellido_materno, $correo, $telefono, $area_id, $cargo, $tipo_id);

    if ($stmt->execute()) {
        $sql_login = "INSERT INTO login (usuario_id, contraseña) VALUES (?, ?)";
        $stmt_login = $conexion->prepare($sql_login);
        $stmt_login->bind_param("ss", $usuario_id, $clave);

        if ($stmt_login->execute()) {
            $stmt_login->close();
            $stmt->close();
            $conexion->close();
            // Registro exitoso - Redirigir con parámetros para notificación
            header("Location: credenciales-alta.php?status=exito&msg=registrado&user=".$usuario_id);
            exit();
        } else {
            // Error en login - Redirigir con parámetros para notificación
            header("Location: credenciales-alta.php?status=error&msg=login");
            exit();
        }
        $stmt_login->close();
    } else {
        // Error en registro - Redirigir con parámetros para notificación
        header("Location: credenciales-alta.php?status=error&msg=registro");
        exit();
    }

    $stmt->close();
    $conexion->close();
}
// Si no es POST, redirigir a la página principal
else {
    header("Location: credenciales-alta.php");
    exit();
}
?>
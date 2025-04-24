<?php
/**
 * Archivo para manejar las operaciones CRUD de empleados
 * Operaciones: listar, registrar, actualizar, dar de baja
 */
header('Content-Type: application/json');
session_start();
require_once 'conexion.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No hay sesión activa']);
    exit;
}

// Determinar la acción según el método HTTP y parámetros
$metodo = $_SERVER['REQUEST_METHOD'];
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

try {
    switch ($metodo) {
        case 'GET':
            // Listar empleados o obtener detalles de un empleado
            if ($accion == 'listar') {
                listarEmpleados($conexion);
            } elseif ($accion == 'obtener' && isset($_GET['id'])) {
                obtenerEmpleado($conexion, $_GET['id']);
            } else {
                throw new Exception('Acción no válida');
            }
            break;
            
        case 'POST':
            // Crear o actualizar empleado
            $datos = json_decode(file_get_contents('php://input'), true);
            
            if (!$datos) {
                // Si no hay datos en formato JSON, intentar con POST normal
                $datos = $_POST;
            }
            
            if (isset($datos['modo'])) {
                if ($datos['modo'] == 'registrar') {
                    registrarEmpleado($conexion, $datos);
                } elseif ($datos['modo'] == 'actualizar' && isset($datos['usuario_id'])) {
                    actualizarEmpleado($conexion, $datos);
                } elseif ($datos['modo'] == 'baja' && isset($datos['usuario_id'])) {
                    darDeBajaEmpleado($conexion, $datos['usuario_id']);
                } else {
                    throw new Exception('Modo no válido o ID no proporcionado');
                }
            } else {
                throw new Exception('No se proporcionó el modo de operación');
            }
            break;
            
        default:
            throw new Exception('Método no permitido');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if (isset($conexion)) {
        $conexion->close();
    }
}

/**
 * Lista todos los empleados activos
 */
function listarEmpleados($conexion) {
    // Verificar si hay un filtro de área
    $filtroArea = isset($_GET['area']) ? intval($_GET['area']) : 0;
    
    // Consulta base - Filtrar solo empleados generales (tipo_id = 5)
    $query = "SELECT u.usuario_id, u.nombre, u.apellido_paterno, u.apellido_materno, 
                    u.correo, u.telefono, u.area_id, u.cargo, u.tipo_id 
             FROM usuario u 
             WHERE u.estatus_id = 1 AND u.tipo_id = 5";
    
    // Agregar filtro de área si está presente
    if ($filtroArea > 0) {
        $query .= " AND u.area_id = ?";
    }
    
    // Ordenar por nombre
    $query .= " ORDER BY u.nombre, u.apellido_paterno, u.apellido_materno";
    
    $stmt = $conexion->prepare($query);
    
    // Asignar parámetros si hay filtro
    if ($filtroArea > 0) {
        $stmt->bind_param("i", $filtroArea);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $empleados = [];
    while ($fila = $resultado->fetch_assoc()) {
        // Procesar nombre de área
        $area = obtenerNombreArea($fila['area_id']);
        
        $empleados[] = [
            'usuario_id' => $fila['usuario_id'],
            'nombre' => $fila['nombre'],
            'apellido_paterno' => $fila['apellido_paterno'],
            'apellido_materno' => $fila['apellido_materno'],
            'nombre_completo' => $fila['nombre'] . ' ' . $fila['apellido_paterno'] . ' ' . $fila['apellido_materno'],
            'correo' => $fila['correo'],
            'telefono' => $fila['telefono'],
            'area_id' => $fila['area_id'],
            'area' => $area,
            'cargo' => $fila['cargo'],
            'tipo_id' => $fila['tipo_id']
        ];
    }
    
    echo json_encode(['success' => true, 'empleados' => $empleados]);
}

/**
 * Obtiene los detalles de un empleado específico
 */
function obtenerEmpleado($conexion, $usuario_id) {
    $query = "SELECT usuario_id, nombre, apellido_paterno, apellido_materno, 
                    correo, telefono, area_id, cargo, tipo_id 
              FROM usuario 
              WHERE usuario_id = ? AND estatus_id = 1 AND tipo_id = 5";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("s", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 0) {
        throw new Exception('Empleado no encontrado');
    }
    
    $empleado = $resultado->fetch_assoc();
    
    echo json_encode(['success' => true, 'empleado' => $empleado]);
}

/**
 * Registra un nuevo empleado
 */
function registrarEmpleado($conexion, $datos) {
    // Validar datos requeridos
    validarDatosEmpleado($datos);
    
    // Establecer siempre tipo_id = 5 (Empleado general)
    $datos['tipo-usuario'] = 5;
    
    // Generar un ID único para el empleado
    $usuario_id = generarIDUnico($conexion);
    
    // Sanear datos
    $nombre = trim($datos['nombre']);
    $apellido_paterno = trim($datos['apellido-paterno']);
    $apellido_materno = trim($datos['apellido-materno']);
    $correo = trim($datos['correo']);
    $telefono = trim($datos['telefono']);
    $area_id = (int)$datos['area'];
    $cargo = trim($datos['cargo']);
    $tipo_id = 5; // Siempre empleado general
    $estatus_id = 1; // Activo
    
    // Verificar si el correo ya existe
    $check_query = "SELECT usuario_id FROM usuario WHERE correo = ?";
    $check_stmt = $conexion->prepare($check_query);
    $check_stmt->bind_param("s", $correo);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        throw new Exception('El correo electrónico ya está registrado');
    }
    
    // Insertar en la tabla usuario
    $query = "INSERT INTO usuario (
                usuario_id, nombre, apellido_paterno, apellido_materno, 
                correo, telefono, area_id, cargo, tipo_id, estatus_id
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param(
        "ssssssissi", 
        $usuario_id, $nombre, $apellido_paterno, $apellido_materno,
        $correo, $telefono, $area_id, $cargo, $tipo_id, $estatus_id
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Error al registrar el empleado: ' . $stmt->error);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Empleado registrado exitosamente',
        'usuario_id' => $usuario_id
    ]);
}

/**
 * Actualiza los datos de un empleado existente
 */
function actualizarEmpleado($conexion, $datos) {
    // Validar datos requeridos
    validarDatosEmpleado($datos);
    
    if (!isset($datos['usuario_id']) || empty($datos['usuario_id'])) {
        throw new Exception('ID de usuario no proporcionado');
    }
    
    // Establecer siempre tipo_id = 5 (Empleado general)
    $datos['tipo-usuario'] = 5;
    
    $usuario_id = $datos['usuario_id'];
    $nombre = trim($datos['nombre']);
    $apellido_paterno = trim($datos['apellido-paterno']);
    $apellido_materno = trim($datos['apellido-materno']);
    $correo = trim($datos['correo']);
    $telefono = trim($datos['telefono']);
    $area_id = (int)$datos['area'];
    $cargo = trim($datos['cargo']);
    $tipo_id = 5; // Siempre empleado general
    
    // Verificar si el empleado existe y es de tipo empleado general
    $check_query = "SELECT usuario_id FROM usuario WHERE usuario_id = ? AND estatus_id = 1 AND tipo_id = 5";
    $check_stmt = $conexion->prepare($check_query);
    $check_stmt->bind_param("s", $usuario_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows === 0) {
        throw new Exception('Empleado no encontrado o no es un empleado general');
    }
    
    // Verificar si el correo ya existe (excepto para el mismo usuario)
    $check_email_query = "SELECT usuario_id FROM usuario WHERE correo = ? AND usuario_id != ?";
    $check_email_stmt = $conexion->prepare($check_email_query);
    $check_email_stmt->bind_param("ss", $correo, $usuario_id);
    $check_email_stmt->execute();
    
    if ($check_email_stmt->get_result()->num_rows > 0) {
        throw new Exception('El correo electrónico ya está registrado por otro empleado');
    }
    
    // Actualizar datos
    $query = "UPDATE usuario SET 
                nombre = ?, 
                apellido_paterno = ?, 
                apellido_materno = ?, 
                correo = ?, 
                telefono = ?, 
                area_id = ?, 
                cargo = ?
              WHERE usuario_id = ? AND tipo_id = 5";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param(
        "sssssiss",
        $nombre, $apellido_paterno, $apellido_materno,
        $correo, $telefono, $area_id, $cargo, $usuario_id
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Error al actualizar el empleado: ' . $stmt->error);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Datos del empleado actualizados correctamente'
    ]);
}

/**
 * Da de baja a un empleado (cambia estatus_id a 2)
 */
function darDeBajaEmpleado($conexion, $usuario_id) {
    // Verificar que el empleado existe y está activo y es de tipo empleado general
    $check_query = "SELECT usuario_id FROM usuario WHERE usuario_id = ? AND estatus_id = 1 AND tipo_id = 5";
    $check_stmt = $conexion->prepare($check_query);
    $check_stmt->bind_param("s", $usuario_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows === 0) {
        throw new Exception('Empleado no encontrado, ya está inactivo o no es un empleado general');
    }
    
    // Cambiar estatus a inactivo (2)
    $query = "UPDATE usuario SET estatus_id = 2 WHERE usuario_id = ? AND tipo_id = 5";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("s", $usuario_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al dar de baja al empleado: ' . $stmt->error);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Empleado dado de baja exitosamente'
    ]);
}

/**
 * Valida los datos del empleado
 */
function validarDatosEmpleado($datos) {
    $camposRequeridos = [
        'nombre', 'apellido-paterno', 'apellido-materno', 
        'correo', 'telefono', 'area', 'cargo'
    ];
    
    foreach ($camposRequeridos as $campo) {
        if (!isset($datos[$campo]) || empty(trim($datos[$campo]))) {
            throw new Exception("El campo {$campo} es requerido");
        }
    }
    
    // Validar formato de correo
    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Formato de correo electrónico inválido');
    }
    
    // Validar teléfono (10 dígitos)
    if (!preg_match('/^\d{10}$/', $datos['telefono'])) {
        throw new Exception('El teléfono debe contener exactamente 10 dígitos numéricos');
    }
    
    // Validar longitud de campos
    if (strlen($datos['nombre']) > 100 || 
        strlen($datos['apellido-paterno']) > 100 || 
        strlen($datos['apellido-materno']) > 100 || 
        strlen($datos['correo']) > 100 || 
        strlen($datos['cargo']) > 100) {
        throw new Exception('Uno o más campos exceden la longitud máxima permitida (100 caracteres)');
    }
}

/**
 * Genera un ID único para el usuario
 */
function generarIDUnico($conexion) {
    $prefijo = 'EMP'; // Prefijo para empleados
    $intentos = 0;
    $maxIntentos = 10;
    
    do {
        // Generar ID aleatorio
        $numeroAleatorio = mt_rand(1000, 9999);
        $letraAleatoria = chr(mt_rand(65, 90)); // Letra mayúscula aleatoria (A-Z)
        $anio = date('y'); // Últimos dos dígitos del año
        
        $id = $prefijo . $numeroAleatorio . $letraAleatoria . $anio;
        
        // Verificar si ya existe
        $query = "SELECT usuario_id FROM usuario WHERE usuario_id = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $existe = $stmt->get_result()->num_rows > 0;
        
        $intentos++;
    } while ($existe && $intentos < $maxIntentos);
    
    if ($existe) {
        throw new Exception('No se pudo generar un ID único después de varios intentos');
    }
    
    return $id;
}

/**
 * Obtiene el nombre del área según su ID
 */
function obtenerNombreArea($area_id) {
    $areas = [
        1 => 'Sistemas',
        2 => 'Planeación',
        3 => 'Finanzas',
        4 => 'Academia de belleza',
        5 => 'Academia de cuidado de la salud',
        6 => 'Apoyo psicológico',
        7 => 'Artículos de belleza y aseo personal',
        8 => 'Banco de alimentos',
        9 => 'Bazar',
        10 => 'Clínica dental',
        11 => 'Comedor comunitario',
        12 => 'Consulta médica',
        13 => 'Escuela de computación',
        14 => 'Escuela de gastronomía',
        15 => 'Estimulación temprana',
        16 => 'Farmacia Similares',
        17 => 'Guardería',
        18 => 'Preescolar',
        19 => 'Tortillería'
    ];
    
    return isset($areas[$area_id]) ? $areas[$area_id] : 'Área desconocida';
}
<?php
/**
 * Archivo para manejar las operaciones CRUD de nómina
 * Operaciones: listar, registrar, actualizar, eliminar nóminas
 */
header('Content-Type: application/json');
session_start();
require_once 'conexion.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No hay sesión activa']);
    exit;
}

// Verificar tipo de usuario (solo administrador de finanzas o superusuario)
if (!in_array($_SESSION['tipo_id'], [1, 3])) { // 1=superusuario, 3=admin finanzas
    echo json_encode(['success' => false, 'error' => 'No tiene permisos para esta operación']);
    exit;
}

// Determinar la acción según el método HTTP y parámetros
$metodo = $_SERVER['REQUEST_METHOD'];
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

try {
    switch ($metodo) {
        case 'GET':
            // Listar nóminas o obtener detalles de una nómina
            if ($accion == 'listar') {
                listarNominas($conexion);
            } elseif ($accion == 'obtener' && isset($_GET['id'])) {
                obtenerNomina($conexion, $_GET['id']);
            } elseif ($accion == 'listar_empleados') {
                listarEmpleados($conexion);
            } else {
                throw new Exception('Acción no válida');
            }
            break;
            
        case 'POST':
            // Crear, actualizar o eliminar nómina
            $datos = json_decode(file_get_contents('php://input'), true);
            
            if (!$datos) {
                // Si no hay datos en formato JSON, intentar con POST normal
                $datos = $_POST;
            }
            
            if (isset($datos['modo'])) {
                if ($datos['modo'] == 'registrar') {
                    registrarNomina($conexion, $datos);
                } elseif ($datos['modo'] == 'actualizar' && isset($datos['nomina_id'])) {
                    actualizarNomina($conexion, $datos);
                } elseif ($datos['modo'] == 'eliminar' && isset($datos['nomina_id'])) {
                    eliminarNomina($conexion, $datos['nomina_id']);
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
 * Lista todas las nóminas registradas
 */
function listarNominas($conexion) {
    // Filtrado por empleado (opcional)
    $filtroUsuario = isset($_GET['usuario_id']) ? $_GET['usuario_id'] : '';
    
    // Consulta base
    $query = "SELECT n.nomina_id, n.fecha_ingreso, n.sueldo, n.imss, n.sar, n.infonavit, n.usuario_id,
                    u.nombre, u.apellido_paterno, u.apellido_materno
             FROM nomina n
             JOIN usuario u ON n.usuario_id = u.usuario_id
             WHERE 1=1";
    
    // Agregar filtro si está presente
    $params = [];
    $types = '';
    
    if (!empty($filtroUsuario)) {
        $query .= " AND n.usuario_id = ?";
        $params[] = $filtroUsuario;
        $types .= 's';
    }
    
    // Ordenar por empleado y fecha de ingreso
    $query .= " ORDER BY u.nombre, u.apellido_paterno, u.apellido_materno, n.fecha_ingreso DESC";
    
    $stmt = $conexion->prepare($query);
    
    // Asignar parámetros si hay filtro
    if (count($params) > 0) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $nominas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $nominas[] = [
            'nomina_id' => $fila['nomina_id'],
            'fecha_ingreso' => $fila['fecha_ingreso'],
            'sueldo' => $fila['sueldo'],
            'imss' => $fila['imss'],
            'sar' => $fila['sar'],
            'infonavit' => $fila['infonavit'],
            'usuario_id' => $fila['usuario_id'],
            'nombre_completo' => $fila['nombre'] . ' ' . $fila['apellido_paterno'] . ' ' . $fila['apellido_materno']
        ];
    }
    
    echo json_encode(['success' => true, 'nominas' => $nominas]);
}

/**
 * Lista todos los empleados activos para seleccionar en nómina
 */
function listarEmpleados($conexion) {
    // Verificar si hay un filtro de área
    $filtroArea = isset($_GET['area']) ? intval($_GET['area']) : 0;
    
    // Consulta base - Filtrar solo empleados generales (tipo_id = 5) y activos (estatus_id = 1)
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
 * Obtiene los detalles de una nómina específica
 */
function obtenerNomina($conexion, $nomina_id) {
    $query = "SELECT n.nomina_id, n.fecha_ingreso, n.sueldo, n.imss, n.sar, n.infonavit, n.usuario_id,
                    u.nombre, u.apellido_paterno, u.apellido_materno
              FROM nomina n
              JOIN usuario u ON n.usuario_id = u.usuario_id
              WHERE n.nomina_id = ?";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $nomina_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 0) {
        throw new Exception('Nómina no encontrada');
    }
    
    $nomina = $resultado->fetch_assoc();
    $nomina['nombre_completo'] = $nomina['nombre'] . ' ' . $nomina['apellido_paterno'] . ' ' . $nomina['apellido_materno'];
    
    echo json_encode(['success' => true, 'nomina' => $nomina]);
}

/**
 * Registra una nueva nómina
 */
function registrarNomina($conexion, $datos) {
    // Validar datos requeridos
    validarDatosNomina($datos);
    
    // Verificar que el usuario existe
    $usuario_id = $datos['usuario_id'];
    $check_query = "SELECT usuario_id FROM usuario WHERE usuario_id = ? AND estatus_id = 1 AND tipo_id = 5";
    $check_stmt = $conexion->prepare($check_query);
    $check_stmt->bind_param("s", $usuario_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows === 0) {
        throw new Exception('El empleado seleccionado no existe o no está activo');
    }
    
    // Verificar si ya tiene una nómina registrada
    $check_nomina = "SELECT nomina_id FROM nomina WHERE usuario_id = ?";
    $check_nomina_stmt = $conexion->prepare($check_nomina);
    $check_nomina_stmt->bind_param("s", $usuario_id);
    $check_nomina_stmt->execute();
    
    if ($check_nomina_stmt->get_result()->num_rows > 0) {
        throw new Exception('El empleado ya tiene una nómina registrada. Use la función de editar.');
    }
    
    // Preparar datos
    $fecha_ingreso = $datos['fecha_ingreso'];
    $sueldo = $datos['sueldo'];
    $imss = $datos['imss'];
    $sar = $datos['sar'];
    $infonavit = $datos['infonavit'];
    
    // Insertar en la tabla nomina
    $query = "INSERT INTO nomina (
                fecha_ingreso, sueldo, imss, sar, infonavit, usuario_id
              ) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param(
        "ssssss", 
        $fecha_ingreso, $sueldo, $imss, $sar, $infonavit, $usuario_id
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Error al registrar la nómina: ' . $stmt->error);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Nómina registrada exitosamente',
        'nomina_id' => $conexion->insert_id
    ]);
}

/**
 * Actualiza los datos de una nómina existente
 */
function actualizarNomina($conexion, $datos) {
    // Validar datos requeridos
    validarDatosNomina($datos);
    
    if (!isset($datos['nomina_id']) || empty($datos['nomina_id'])) {
        throw new Exception('ID de nómina no proporcionado');
    }
    
    $nomina_id = $datos['nomina_id'];
    $usuario_id = $datos['usuario_id'];
    
    // Verificar que la nómina existe
    $check_query = "SELECT nomina_id FROM nomina WHERE nomina_id = ?";
    $check_stmt = $conexion->prepare($check_query);
    $check_stmt->bind_param("i", $nomina_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows === 0) {
        throw new Exception('La nómina a actualizar no existe');
    }
    
    // Preparar datos
    $fecha_ingreso = $datos['fecha_ingreso'];
    $sueldo = $datos['sueldo'];
    $imss = $datos['imss'];
    $sar = $datos['sar'];
    $infonavit = $datos['infonavit'];
    
    // Actualizar datos
    $query = "UPDATE nomina SET 
                fecha_ingreso = ?, 
                sueldo = ?, 
                imss = ?, 
                sar = ?, 
                infonavit = ?
              WHERE nomina_id = ? AND usuario_id = ?";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param(
        "sssssss",
        $fecha_ingreso, $sueldo, $imss, $sar, $infonavit, $nomina_id, $usuario_id
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Error al actualizar la nómina: ' . $stmt->error);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Nómina actualizada correctamente'
    ]);
}

/**
 * Elimina una nómina (eliminación física)
 */
function eliminarNomina($conexion, $nomina_id) {
    // Verificar que la nómina existe
    $check_query = "SELECT nomina_id FROM nomina WHERE nomina_id = ?";
    $check_stmt = $conexion->prepare($check_query);
    $check_stmt->bind_param("i", $nomina_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows === 0) {
        throw new Exception('La nómina a eliminar no existe');
    }
    
    // Eliminar la nómina
    $query = "DELETE FROM nomina WHERE nomina_id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $nomina_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al eliminar la nómina: ' . $stmt->error);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Nómina eliminada correctamente'
    ]);
}

/**
 * Valida los datos de la nómina
 */
function validarDatosNomina($datos) {
    $camposRequeridos = [
        'usuario_id', 'fecha_ingreso', 'sueldo', 'imss', 'sar', 'infonavit'
    ];
    
    foreach ($camposRequeridos as $campo) {
        if (!isset($datos[$campo]) || $datos[$campo] === '') {
            throw new Exception("El campo {$campo} es requerido");
        }
    }
    
    // Validar valores numéricos
    $camposNumericos = ['sueldo', 'imss', 'sar', 'infonavit'];
    foreach ($camposNumericos as $campo) {
        if (!is_numeric($datos[$campo]) || $datos[$campo] < 0) {
            throw new Exception("El campo {$campo} debe ser un número positivo");
        }
    }
    
    // Validar fecha de ingreso
    $fechaIngreso = strtotime($datos['fecha_ingreso']);
    $fechaActual = strtotime(date('Y-m-d'));
    
    if ($fechaIngreso > $fechaActual) {
        throw new Exception('La fecha de ingreso no puede ser mayor a la fecha actual');
    }
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
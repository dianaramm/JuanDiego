<?php
header('Content-Type: application/json');
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Verificar si es una solicitud de verificación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    try {
        if ($_POST['accion'] === 'verificar_existente') {
            // Modificado: Excluimos validez_id = 6 de la consulta
            $query = "SELECT planeacion_id 
                      FROM planeacion 
                      WHERE solicitante_id = ? 
                      AND validez_id = 1 
                      AND estatus_id IN (1,2,3,4,5)";
            
            $stmt = $conexion->prepare($query);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta de verificación");
            }
            
            $stmt->bind_param("s", $usuario_id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            echo json_encode([
                'success' => true,
                'exists' => $resultado->num_rows > 0
            ]);
            exit;
        }
        
        // Proceso de guardar nueva planeación
        if ($_POST['accion'] === 'guardar_planeacion') {
            // Modificado: Excluimos validez_id = 6 de la consulta
            $verificar = $conexion->prepare("SELECT planeacion_id 
                                           FROM planeacion 
                                           WHERE solicitante_id = ? 
                                           AND validez_id = 1 
                                           AND estatus_id IN (1,2,3,4,5)");
            $verificar->bind_param("s", $usuario_id);
            $verificar->execute();
            $resultado = $verificar->get_result();

            if ($resultado->num_rows > 0) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Ya existe una planeación vigente'
                ]);
                exit;
            }

            // Validar campos requeridos
            $campos_requeridos = ['nombre-planeacion', 'tipo-planeacion', 'importancia', 'descripcion', 'objetivo-general'];
            foreach ($campos_requeridos as $campo) {
                if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    throw new Exception("El campo $campo es requerido");
                }
            }

            // Iniciar transacción
            $conexion->begin_transaction();

            // Insertar planeación
            $stmt = $conexion->prepare("
                INSERT INTO planeacion (
                    nombre, 
                    tipo, 
                    importancia, 
                    descripcion, 
                    objetivo, 
                    fecha_creacion, 
                    solicitante_id, 
                    estatus_id, 
                    validez_id
                ) VALUES (?, ?, ?, ?, ?, NOW(), ?, 6, 1)
            ");

            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
            }

            $stmt->bind_param("ssssss", 
                $_POST['nombre-planeacion'],
                $_POST['tipo-planeacion'],
                $_POST['importancia'],
                $_POST['descripcion'],
                $_POST['objetivo-general'],
                $usuario_id
            );

            if (!$stmt->execute()) {
                throw new Exception("Error al guardar la planeación: " . $stmt->error);
            }

            $planeacion_id = $conexion->insert_id;

            // Generar cronograma
            $cronograma_id = mt_rand(10000, 99999);
            $nombre_planeacion = $_POST['nombre-planeacion'];

            $inserta_cronograma = $conexion->prepare("INSERT INTO cronograma (cronograma_id, nombre) VALUES (?, ?)");
            if (!$inserta_cronograma) {
                throw new Exception("Error al preparar la inserción del cronograma");
            }

            $inserta_cronograma->bind_param("ss", $cronograma_id, $nombre_planeacion);
            if (!$inserta_cronograma->execute()) {
                throw new Exception("Error al insertar el cronograma");
            }

            // Actualizar planeación con el cronograma_id
            $actualiza_planeacion = $conexion->prepare("UPDATE planeacion SET cronograma_id = ? WHERE planeacion_id = ?");
            if (!$actualiza_planeacion) {
                throw new Exception("Error al preparar la actualización de la planeación");
            }

            $actualiza_planeacion->bind_param("si", $cronograma_id, $planeacion_id);
            if (!$actualiza_planeacion->execute()) {
                throw new Exception("Error al actualizar la planeación con el cronograma");
            }

            // Confirmar transacción
            $conexion->commit();

            echo json_encode([
                'success' => true,
                'planeacion_id' => $planeacion_id,
                'mensaje' => 'Planeación guardada exitosamente'
            ]);
        }

    } catch (Exception $e) {
        if (isset($conexion)) {
            $conexion->rollback();
        }
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    } finally {
        // Cerrar todas las conexiones
        if (isset($stmt)) $stmt->close();
        if (isset($verificar)) $verificar->close();
        if (isset($inserta_cronograma)) $inserta_cronograma->close();
        if (isset($actualiza_planeacion)) $actualiza_planeacion->close();
        if (isset($conexion)) $conexion->close();
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido o acción no especificada'
    ]);
}
?>
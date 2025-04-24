<?php
require_once 'conexion.php';

try {
    $area_filtro = isset($_GET['area']) ? $_GET['area'] : '';

    $sql = "SELECT 
                u.usuario_id,
                u.nombre, 
                u.apellido_paterno, 
                u.apellido_materno, 
                u.correo,
                u.cargo,
                u.area_id,
                l.contraseña
            FROM 
                login l
                INNER JOIN usuario u ON l.usuario_id = u.usuario_id
            WHERE 
                u.estatus_id = 1";

    if (!empty($area_filtro)) {
        $sql .= " AND u.area_id = ?";
    }

    $stmt = $conexion->prepare($sql);

    if (!empty($area_filtro)) {
        $stmt->bind_param("i", $area_filtro);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            // Determinar el nombre del área basado en area_id
            $nombre_area = '';
            switch ($row['area_id']) {
                case 1: $nombre_area = 'Sistemas'; break;
                case 2: $nombre_area = 'Planeación'; break;
                case 3: $nombre_area = 'Finanzas'; break;
                case 4: $nombre_area = 'Academia de belleza'; break;
                case 5: $nombre_area = 'Academia cuidado de salud'; break;
                case 6: $nombre_area = 'Apoyo psicológico'; break;
                case 7: $nombre_area = 'Artículos de belleza y aseo'; break;
                case 8: $nombre_area = 'Banco de alimentos'; break;
                case 9: $nombre_area = 'Bazaar'; break;
                case 10: $nombre_area = 'Clínica dental'; break;
                case 11: $nombre_area = 'Comedor comunitario'; break;
                case 12: $nombre_area = 'Consulta médica'; break;
                case 13: $nombre_area = 'Escuela de computación'; break;
                case 14: $nombre_area = 'Escuela de gastronomía'; break;
                case 15: $nombre_area = 'Estimulación temprana'; break;
                case 16: $nombre_area = 'Farmacia Similares'; break;
                case 17: $nombre_area = 'Guardería'; break;
                case 18: $nombre_area = 'Preescolar'; break;
                case 19: $nombre_area = 'Tortillería'; break;
                default: $nombre_area = 'Área no definida';
            }
            
            $nombre_completo = htmlspecialchars($row['nombre'] . ' ' . $row['apellido_paterno'] . ' ' . $row['apellido_materno']);
            $usuario_id = $row['usuario_id'];

            echo "<tr data-id='{$usuario_id}'>";
            echo "<td class='text-center'><input type='radio' name='credencial' value='{$usuario_id}'></td>";
            echo "<td>{$nombre_completo}</td>";
            echo "<td>" . htmlspecialchars($row['correo']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario_id) . "</td>";
            echo "<td>" . htmlspecialchars($row['contraseña']) . "</td>";
            echo "<td>" . htmlspecialchars($nombre_area) . "</td>";
            echo "<td>" . htmlspecialchars($row['cargo']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No se encontraron credenciales registradas.</td></tr>";
    }
} catch (Exception $e) {
    echo "<tr><td colspan='7'>Error al cargar las credenciales: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}

$conexion->close();
?>
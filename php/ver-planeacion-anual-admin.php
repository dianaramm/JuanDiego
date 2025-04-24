<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Control de Planeación Anual</title>
    <link rel="stylesheet" href="../css/estilos-formularios.css">
    <link rel="stylesheet" href="../css/estilos-adicionales.css">
</head>

<body>
    <div class="pagina-contenedor">
        <nav class="navegacion">
            <div class="contenedor">
                <a href="../html/administracion.html" class="logo">
                    <img src="../img/empresa.png" alt="Logo Centro Comunitario Juan Diego" class="logo-img">
                    <span class="logo-texto">Centro Comunitario Juan Diego</span>
                </a>
                <button id="menu-toggle" class="menu-toggle">
                    <span class="icono-menu-toggle"></span>
                </button>
            </div>
        </nav>

        <div id="menu-lateral" class="menu-lateral">
            <ul>
                <li><a href=""><span class="icono-menu icono-usuario"></span> Cargando...</a></li>
                <li><a href="../html/administracion.html"><span class="icono-menu icono-menu"></span> Menú principal</a></li>
                <li><a href="../php/cerrar-sesion.php"><span class="icono-menu icono-salir"></span> Cerrar sesión</a></li>
                <li><a id="boton-ocultar"><span class="icono-menu icono-ocultar"></span> Ocultar</a></li>
            </ul>
        </div>


        <div class="navegacion-secundaria">
            <div class="contenedor-nav-secundaria">
                <ul class="menu-secundario">

                    <!-- Menú de Inicio -->
                    <li class="item-menu-secundario">
                        <a href="../html/administracion.html" class="enlace-secundario">
                            <span class="icono-menu icono-menu"></span>
                            Inicio
                        </a>
                    </li>

                    <!-- Menú Credenciales con submenú -->
                    <li class="item-menu-secundario">
                        <a href="../php/credenciales-alta.php" class="enlace-secundario enlace-con-submenu">
                            Credenciales
                            <span class="indicador-submenu"></span>
                        </a>
                    </li>

                    <!-- Menú Planeación con submenú -->
                    <li class="item-menu-secundario">
                        <a href="../php/planeacion-anual.php" class="enlace-secundario enlace-con-submenu">
                            Control de planeación anual
                            <span class="indicador-submenu"></span>
                        </a>
                    </li>

                    <!-- Menú Plazas con submenú -->
                    <li class="item-menu-secundario">
                        <a href="../php/solicitudes-plazas-admin.php" class="enlace-secundario enlace-con-submenu">
                            Solicitud de Plazas
                            <span class="indicador-submenu"></span>
                        </a>
                    </li>
            </div>
        </div>


        <main class="contenido-principal">
            <div class="contenedor">
                <h1 class="titulo-principal">Resumen de planeación anual</h1>

                <div class="panel">
                    <div class="panel-cuerpo">
                        <?php
                        require_once 'conexion.php';
                        if (isset($_GET['id'])) {
                            $id = intval($_GET['id']);
                            $query = "SELECT p.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.area_id 
                                    FROM planeacion p 
                                    JOIN usuario u ON p.solicitante_id = u.usuario_id 
                                    WHERE p.planeacion_id = ? AND p.estatus_id = 3";

                            $stmt = $conexion->prepare($query);
                            $stmt->bind_param("i", $id);
                            $stmt->execute();
                            $resultado = $stmt->get_result();

                            if ($fila = $resultado->fetch_assoc()) {
                                // Determinar el nombre del área basado en area_id
                                $area_nombre = '';
                                switch ($fila['area_id']) {
                                    case 1: $area_nombre = 'Sistemas'; break;
                                    case 2: $area_nombre = 'Planeación'; break;
                                    case 3: $area_nombre = 'Finanzas'; break;
                                    case 4: $area_nombre = 'Academia de belleza'; break;
                                    case 5: $area_nombre = 'Academia de cuidado de la salud'; break;
                                    case 6: $area_nombre = 'Apoyo psicológico'; break;
                                    case 7: $area_nombre = 'Artículos de belleza y aseo personal'; break;
                                    case 8: $area_nombre = 'Banco de alimentos'; break;
                                    case 9: $area_nombre = 'Bazar'; break;
                                    case 10: $area_nombre = 'Clínica dental'; break;
                                    case 11: $area_nombre = 'Comedor comunitario'; break;
                                    case 12: $area_nombre = 'Consulta médica'; break;
                                    case 13: $area_nombre = 'Escuela de computación'; break;
                                    case 14: $area_nombre = 'Escuela de gastronomía'; break;
                                    case 15: $area_nombre = 'Estimulación temprana'; break;
                                    case 16: $area_nombre = 'Farmacia Similares'; break;
                                    case 17: $area_nombre = 'Guardería'; break;
                                    case 18: $area_nombre = 'Preescolar'; break;
                                    case 19: $area_nombre = 'Tortillería'; break;
                                    default: $area_nombre = 'Área no definida';
                                }

                                // Formatear la fecha de creación
                                $fecha_creacion = date('d/m/Y', strtotime($fila['fecha_creacion']));

                                echo '<div class="detalles-planeacion">';
                                echo '<h2>Detalles de la Planeación</h2>';
                                
                                echo '<div class="ficha-planeacion">';
                                // Información básica en una tabla de datos estilizada
                                echo '<table class="tabla-datos">';
                                echo '<tr><th>Nombre de la planeación:</th><td>' . htmlspecialchars($fila['nombre']) . '</td></tr>';
                                echo '<tr><th>Tipo de planeación:</th><td>' . htmlspecialchars($fila['tipo']) . '</td></tr>';
                                echo '<tr><th>Importancia:</th><td>' . htmlspecialchars($fila['importancia']) . '</td></tr>';
                                echo '<tr><th>Fecha de creación:</th><td>' . $fecha_creacion . '</td></tr>';
                                echo '<tr><th>Solicitante:</th><td>' . htmlspecialchars($fila['nombre'] . ' ' . $fila['apellido_paterno'] . ' ' . $fila['apellido_materno']) . '</td></tr>';
                                echo '<tr><th>Área:</th><td>' . htmlspecialchars($area_nombre) . '</td></tr>';
                                echo '</table>';
                                
                                // Descripción y objetivo en secciones separadas para mejor legibilidad
                                echo '<div class="seccion-descripcion">';
                                echo '<h3>Descripción</h3>';
                                echo '<p>' . nl2br(htmlspecialchars($fila['descripcion'])) . '</p>';
                                echo '</div>';
                                
                                echo '<div class="seccion-objetivo">';
                                echo '<h3>Objetivo</h3>';
                                echo '<p>' . nl2br(htmlspecialchars($fila['objetivo'])) . '</p>';
                                echo '</div>';
                                echo '</div>';

                                echo '<div class="contenedor-botones-principales">';
                                echo '<button type="button" class="boton-cancelar" onclick="rechazarPlaneacion(' . $id . ')">RECHAZAR</button>';
                                echo '<button type="button" class="boton-guardar" onclick="aprobarPlaneacion(' . $id . ')">APROBAR</button>';
                                echo '</div>';
                                echo '</div>';

                            } else {
                                echo '<div class="mensaje error">No se encontró la planeación solicitada o no está pendiente de aprobación.</div>';
                            }
                            $stmt->close();
                        } else {
                            echo '<div class="mensaje error">No se especificó una planeación para revisar.</div>';
                        }
                        $conexion->close();
                        ?>
                    </div>
                </div>
            </div>
        </main>

        <footer class="pie-pagina">
            <div class="contenedor">
                <div class="derechos">
                    <p>&copy; 2024 Centro Comunitario Juan Diego. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Modal de confirmación -->
    <div id="modal-confirmacion" class="modal">
        <div class="modal-contenido">
            <div id="modal-mensaje" class="modal-mensaje">
                ¿Está seguro de realizar esta acción? Esta acción no se puede deshacer.
            </div>
            <div class="modal-botones">
                <button id="modal-confirmar" class="boton-guardar">CONFIRMAR</button>
                <button id="modal-cancelar" class="boton-cancelar">CANCELAR</button>
            </div>
        </div>
    </div>

    <script src="../js/menu.js"></script>
    <script src="../js/verificar-sesión.js"></script>
    <script src="../js/planeacion-aprobacion.js"></script>
    
    <style>
        .ficha-planeacion {
            margin-bottom: 30px;
        }
        
        .tabla-datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .tabla-datos th, .tabla-datos td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .tabla-datos th {
            width: 30%;
            color: #003366;
            font-weight: 600;
        }
        
        .seccion-descripcion, .seccion-objetivo {
            margin-bottom: 25px;
        }
        
        .seccion-descripcion h3, .seccion-objetivo h3 {
            color: #003366;
            font-size: 1.2em;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        
        .seccion-descripcion p, .seccion-objetivo p {
            line-height: 1.6;
            color: #333;
        }
        
        .contenedor-botones-principales {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
    </style>
</body>

</html>
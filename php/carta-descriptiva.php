<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Gestión de Planeación</title>
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
                <li><a href=""><span class="icono-menu icono-usuario"></span> Usuario</a></li>
                <li><a href="../html/administracion.html"><span class="icono-menu icono-menu"></span> Menú principal</a></li>
                <li><a href="../php/cerrar-sesion.php"><span class="icono-menu icono-salir"></span> Cerrar sesión</a></li>
                <li><a id="boton-ocultar"><span class="icono-menu icono-ocultar"></span> Ocultar</a></li>
            </ul>
        </div>

        <!-- Breadcrumbs dinámico -->
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

                    <!-- Croonograma -->
                    <li class="item-menu-secundario">
                        <a href="../php/admin-calendario.php" class="enlace-secundario enlace-con-submenu">
                            Cronograma
                            <span class="indicador-submenu"></span>
                        </a>
                    </li>

                    <!-- Menú Plazas con submenú -->
                    <li class="item-menu-secundario">
                        <a href="../php/solicitudes-plazas.php" class="enlace-secundario enlace-con-submenu">
                            Solicitud de Plazas
                            <span class="indicador-submenu"></span>
                        </a>
                    </li>
            </div>
        </div>

        <main class="contenido-principal">
            <div class="contenedor">
                <h1 class="titulo-principal">Planeaciones activas</h1>

                <div class="panel">
                    <div class="panel-cuerpo">
                        <div class="campo-formulario">
                            <label for="filtro-area">Área</label>
                            <select id="filtro-area" name="filtro-area">
                                <option value="">Todas las áreas</option>
                                <option value="Academia de belleza">Academia de belleza</option>
                                <option value="Academia cuidado_salud">Academia de cuidado de la salud</option>
                                <option value="Apoyo psicologico">Apoyo psicológico</option>
                                <option value="Articulos de belleza y aseo">Artículos de belleza y aseo personal
                                </option>
                                <option value="Banco de alimentos">Banco de alimentos</option>
                                <option value="Bazaar">Bazar</option>
                                <option value="Clínica dental">Clínica dental</option>
                                <option value="Comedor comunitario">Comedor comunitario</option>
                                <option value="Consulta medica">Consulta médica</option>
                                <option value="Escuela de computacion">Escuela de computación</option>
                                <option value="Escuela de gastronomia">Escuela de gastronomía</option>
                                <option value="Estimulacion temprana">Estimulación temprana</option>
                                <option value="Farmacia similares">Farmacia Similares</option>
                                <option value="Guarderia">Guardería</option>
                                <option value="Preescolar">Preescolar</option>
                                <option value="Tortilleria">Tortillería</option>
                            </select>
                        </div>

                        <div class="tabla-responsive">
                            <table class="tabla" id="tabla-principal">
                                <thead>
                                    <tr>
                                        <th>Seleccionar</th>
                                        <th>Nombre</th>
                                        <th>Fecha</th>
                                        <th>Área</th>
                                        <th>Estatus</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-planeacion">
                                    <?php
                                    if (file_exists('consulta-listar-planeacion.php')) {
                                        include '../php/consulta-listar-carta-descriptiva.php';
                                    } else {
                                        echo "<tr><td colspan='5'>Error: No se pudo cargar la lista de planeación.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="contenedor-botones-principales" style="margin-top: 20px;">
                            <button type="button" id="boton-generar-reporte" class="boton-guardar">GENERAR PDF</button>
                            <button type="button" id="boton-ver-calendario" class="boton-guardar" onclick="window.location.href='admin-calendario.php'">VER CALENDARIO</button>
                        </div>
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

    <script src="../js/menu.js"></script>
    <script src="../js/verificar-sesión.js"></script>
    <script src="../js/filtrar-carta-descriptiva.js"></script>

</body>

</html>
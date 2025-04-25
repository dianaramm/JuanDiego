<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Calendario de Actividades</title>
    <link rel="stylesheet" href="../css/estilos-formularios.css">
    <link rel="stylesheet" href="../css/estilos-adicionales.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css">
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
                        <ul class="submenu">
                            <li><a href="../php/ver-planeacion-anual-admin.php">Reportes de planeacion</a></li>
                        </ul>
                    </li>

                    <!-- Menú Plazas con submenú -->
                    <li class="item-menu-secundario">
                        <a href="../php/solicitudes-plazas-admin.php" class="enlace-secundario enlace-con-submenu">
                            Solicitud de Plazas
                            <span class="indicador-submenu"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <main class="contenido-principal">
            <div class="contenedor">
                <h1 class="titulo-principal">Calendario de Actividades</h1>

                <div class="panel">
                    <div class="panel-cabecera">
                        <h2 class="panel-titulo">Actividades de todas las áreas</h2>
                        <div class="filtro-area">
                            <label for="filtro-area">Filtrar por área:</label>
                            <select id="filtro-area" name="filtro-area">
                                <option value="">Todas las áreas</option>
                                <option value="4">Academia de belleza</option>
                                <option value="5">Academia de cuidado de la salud</option>
                                <option value="6">Apoyo psicológico</option>
                                <option value="7">Artículos de belleza y aseo personal</option>
                                <option value="8">Banco de alimentos</option>
                                <option value="9">Bazar</option>
                                <option value="10">Clínica dental</option>
                                <option value="11">Comedor comunitario</option>
                                <option value="12">Consulta médica</option>
                                <option value="13">Escuela de computación</option>
                                <option value="14">Escuela de gastronomía</option>
                                <option value="15">Estimulación temprana</option>
                                <option value="16">Farmacia Similares</option>
                                <option value="17">Guardería</option>
                                <option value="18">Preescolar</option>
                                <option value="19">Tortillería</option>
                            </select>
                        </div>
                    </div>
                    <div class="panel-cuerpo">
                        <div id="calendario-admin" style="width: 100%;"></div>
                        
                        <!-- Botones de acción -->
                        <div class="contenedor-botones-principales" style="margin-top: 20px;">
                            <button type="button" id="boton-imprimir-calendario" class="boton-guardar">IMPRIMIR CALENDARIO</button>
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
    <script src="../js/breadcrumbs.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/locales/es.js"></script>
    <script src="../js/admin-calendario.js"></script>
</body>

</html>
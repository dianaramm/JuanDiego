<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Reportes Financieros</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="../css/estilos-formularios.css">
    <link rel="stylesheet" href="../css/estilos-adicionales.css">
</head>

<body>
    <div class="pagina-contenedor">
        <nav class="navegacion">
            <div class="contenedor">
                <a href="../html/finanzas.html" class="logo">
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
                <li><a href="../html/finanzas.html"><span class="icono-menu icono-menu"></span> Menú principal</a></li>
                <li><a href="../php/cerrar-sesion.php"><span class="icono-menu icono-salir"></span> Cerrar sesión</a>
                </li>
                <li><a id="boton-ocultar"><span class="icono-menu icono-ocultar"></span> Ocultar</a></li>
            </ul>
        </div>

        <div class="navegacion-secundaria">
            <div class="contenedor-nav-secundaria">
                <ul class="menu-secundario">
                    <!-- Menú de Inicio -->
                    <li class="item-menu-secundario">
                        <a href="../html/finanzas.html" class="enlace-secundario">
                            <span class="icono-menu icono-menu"></span>
                            Inicio
                        </a>
                    </li>

                    <!-- Menú Recursos Humanos -->
                    <li class="item-menu-secundario">
                        <a href="../php/finanzas-recursos-humanos.php" class="enlace-secundario">
                            Recursos Humanos
                        </a>
                    </li>

                    <!-- Menú Nómina -->
                    <li class="item-menu-secundario">
                        <a href="../php/finanzas-nomina.php" class="enlace-secundario">
                            Nómina
                        </a>
                    </li>

                    <!-- Menú Reportes -->
                    <li class="item-menu-secundario">
                        <a href="../php/finanzas-reportes.php" class="enlace-secundario">
                            Reportes
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <main class="contenido-principal">
            <div class="contenedor">
                <h1 class="titulo-principal">Reportes Financieros</h1>

                <!-- Panel de selección de reportes -->
                <div class="panel">
                    <div class="panel-cabecera">
                        <h2 class="panel-titulo">Generar Reportes</h2>
                    </div>
                    <div class="panel-cuerpo">
                        <form id="formulario-reportes" class="formulario">
                            <div class="grid-formulario">
                                <div class="campo-formulario">
                                    <label for="tipo-reporte">Tipo de Reporte</label>
                                    <select id="tipo-reporte" name="tipo_reporte" required>
                                        <option value="">Seleccione un tipo de reporte</option>
                                        <option value="nomina">Nómina</option>
                                        <option value="recursos_humanos">Recursos Humanos</option>
                                    </select>
                                </div>

                                <div class="campo-formulario">
                                    <label for="fecha-inicio">Fecha Inicio</label>
                                    <input type="date" id="fecha-inicio" name="fecha_inicio" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="fecha-fin">Fecha Fin</label>
                                    <input type="date" id="fecha-fin" name="fecha_fin" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="filtro-area">Área (Opcional)</label>
                                    <select id="filtro-area" name="area">
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

                                <div class="campo-formulario">
                                    <label for="coordinador">Tipo de empleados</label>
                                    <select id="coordinador" name="coordinador">
                                        <option value="super">Super usuario</option>
                                        <option value="adminstrador">Administrador de planeación</option>
                                        <option value="finanzas">Administrador de finanzas</option>
                                        <option value="coordinador">Coordinadores</option>
                                        <option value="empleados">Empleados generales</option>
                                        <option value="todos">Ambos</option>
                                    </select>
                                </div>

                                <div class="campo-formulario">
                                    <label for="formato-reporte">Formato</label>
                                    <select id="formato-reporte" name="formato" required>
                                        <option value="visualizar">Visualizar en pantalla</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>
                            </div>

                            <div class="contenedor-botones-principales">
                                <button type="button" id="boton-cancelar" class="boton-cancelar">CANCELAR</button>
                                <button type="submit" id="boton-generar" class="boton-guardar">GENERAR REPORTE</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Panel para visualizar el reporte generado -->
                <div id="panel-reporte" class="panel mt-4" style="display: none;">
                    <div class="panel-cabecera">
                        <h2 id="titulo-reporte" class="panel-titulo">Previsualización del Reporte</h2>
                        <div class="panel-acciones">
                            <!--  <button id="boton-imprimir" class="boton-accion">
                                <img src="../img/icono-imprimir.png" alt="Imprimir" class="icono-accion">
                                Imprimir
                            </button> -->
                            <button id="boton-descargar" class="boton-accion">
                                <img src="../img/icono-descargar.png" alt="Descargar" class="icono-accion">
                                Descargar
                            </button>
                        </div>
                    </div>
                    <div class="panel-cuerpo">
                        <div id="contenido-reporte" class="reporte-contenido">
                            <!-- Aquí se cargará el contenido del reporte -->
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
    <script src="../js/finanzas-reportes.js"></script>
</body>

</html>
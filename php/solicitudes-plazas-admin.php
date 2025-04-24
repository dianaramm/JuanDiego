<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Gestión de Planeación</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="../css/estilos-formularios.css">
    <link rel="stylesheet" href="../css/admin-unicos.css">
</head>

<body>
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
        
    <div class="contenedor-principal">
        <!-- Menú lateral ahora dentro de un aside -->
        <aside id="menu-lateral" class="menu-lateral">
            <ul>
                <li><a href=""><span class="icono-menu icono-usuario"></span> Cargando...</a></li>
                <li><a href="../php/cerrar-sesion.php"><span class="icono-menu icono-salir"></span> Cerrar sesión</a>
                </li>
                <li><a id="boton-ocultar"><span class="icono-menu icono-ocultar"></span> Ocultar</a></li>
            </ul>
        </aside>

        <main class="contenido-principal">

            <!-- Contenido de la página -->
            <div class="contenedor">
                <h1 class="titulo-principal">Solicitudes de plazas</h1>
                <div class="grid-tarjetas">
                    <div class="panel">
                        <div class="panel-cuerpo">
                            <div class="campo-formulario">
                                <label for="filtro-area">Área</label>
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

                            <div class="tabla-responsive">
                                <table class="tabla" id="tabla-solicitudes">
                                    <thead>
                                        <tr>
                                            <th width="5%">Seleccionar</th>
                                            <th>Coordinador solicitante</th>
                                            <th>Área</th>
                                            <th>Puesto requerido</th>
                                            <th>Justificación</th>
                                            <th>Fecha</th>
                                            <th>Estatus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php include 'consulta-listar-solicitud-plazas-admin.php'; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="contenedor-botones-accion">
                                <button type="button" id="boton-aprobar" class="boton-guardar" disabled>APROBAR</button>
                                <button type="button" id="boton-rechazar" class="boton-rechazar" disabled>RECHAZAR</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de confirmación -->
    <div id="modal-confirmacion" class="modal">
        <div class="modal-contenido">
            <div class="modal-mensaje" id="modal-mensaje">
                ¿Está seguro de realizar esta acción? Esta acción no se puede deshacer.
            </div>
            <div class="modal-botones">
                <button id="modal-boton-confirmar" class="boton-guardar">Confirmar</button>
                <button id="modal-boton-cancelar" class="boton-cancelar">Cancelar</button>
            </div>
        </div>
    </div>

    <footer class="pie-pagina">
        <div class="contenedor">
            <div class="derechos">
                <p>&copy; 2024 Centro Comunitario Juan Diego. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="../js/menu.js"></script>
    <script src="../js/verificar-sesión.js"></script>
    <script src="../js/filtrar-solicitudes-plazas-admin.js"></script>
    <script src="../js/solicitudes-plazas-acciones.js"></script>
</body>

</html>
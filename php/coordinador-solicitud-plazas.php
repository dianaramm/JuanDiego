<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Plazas</title>
    <link rel="stylesheet" href="../css/estilos-formularios.css">
    <link rel="stylesheet" href="../css/coordinador.css">
    <link rel="stylesheet" href="../css/estilos-adicionales.css">
</head>

<body>
    <div class="pagina-contenedor">
        <!-- Encabezado de la página -->
        <nav class="navegacion">
            <div class="contenedor">
                <a href="../html/coordinador.html" class="logo">
                    <img src="../img/empresa.png" alt="Logo Centro Comunitario Juan Diego" class="logo-img">
                    <span class="logo-texto">Centro Comunitario Juan Diego</span>
                </a>

                <!-- Botón para abrir el menu lateral de la página -->
                <button id="menu-toggle" class="menu-toggle">
                    <span class="icono-menu-toggle"></span>
                </button>
            </div>
        </nav>

        <!-- Menu lateral de la página -->
        <div id="menu-lateral" class="menu-lateral">
            <ul>
                <li><a href=""><span class="icono-menu icono-usuario"></span> Cargando...</a></li>
                <li><a href="../html/coordinador.html"><span class="icono-menu icono-menu"></span> Menú principal</a></li>
                <li><a href="../php/cerrar-sesion.php"><span class="icono-menu salir"></span> Cerrar sesión</a></li>
                <li><a id="boton-ocultar"><span class="icono-menu ocultar"></span> Ocultar</a></li>
            </ul>
        </div>

         <!-- Breadcrumbs dinámico -->
         <div class="navegacion-secundaria">
            <div class="contenedor-nav-secundaria">
                <ul class="menu-secundario">
                    <!-- Menú de Inicio -->
                    <li class="item-menu-secundario">
                        <a href="../html/coordinador.html" class="enlace-secundario">
                            <span class="icono-menu icono-menu"></span>
                            Inicio
                        </a>
                    </li>

                    <!-- Menú Planeación con submenú -->
                    <li class="item-menu-secundario">
                        <a href="../php/coordinador-menu-planeacion-anual.php"
                            class="enlace-secundario enlace-con-submenu">
                            Planeación anual
                            <span class="indicador-submenu"></span>
                        </a>
                        <ul class="submenu">
                            <li><a href="../php/coordinador-formulario-planeacion-anual.php">Registro de planeación</a>
                            </li>
                            <li><a href="../php/coordinador-estatus-planeacion-anual.php">Estatus de planeación</a></li>
                        </ul>
                    </li>

                    <!-- Menú Cronograma -->
                    <li class="item-menu-secundario">
                        <a href="../php/coordinador-cronograma.php" class="enlace-secundario">
                            Cronograma
                        </a>
                    </li>

                    <!-- Menú Plazas con submenú -->
                    <li class="item-menu-secundario">
                        <a href="../php/coordinador-menu-plazas.php" class="enlace-secundario enlace-con-submenu">
                            Plazas
                            <span class="indicador-submenu"></span>
                        </a>
                        <ul class="submenu">
                            <li><a href="../php/coordinador-solicitud-plazas.php">Solicitud de plaza</a></li>
                            <li><a href="../php/coordinador-listar-plazas.php">Historial de plazas</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contenido principal para coordinadores MAIN  -->
        <main class="contenido-principal">
            <div class="contenedor">
                <h1 class="titulo-principal">Plazas</h1>

                <div class="panel seccion-ficha">
                    <div class="panel-cuerpo">
                        <form id="formulario-plaza" class="formulario-planeacion">
                            <input type="hidden" id="plaza_id" name="plaza_id" value="">
                            <input type="hidden" id="accion" name="accion" value="guardar">
                            
                            <div class="campo-formulario">
                                <label for="puesto">Puesto</label>
                                <input type="text" id="puesto" name="puesto" placeholder="Nombre del puesto requerido" required>
                            </div>

                            <div class="campo-formulario">
                                <label for="justificacion">Justificación</label>
                                <textarea id="justificacion" name="justificacion" placeholder="Justificación detallada de la necesidad del puesto" required></textarea>
                            </div>

                            <div class="contenedor-botones-principales">
                                <button type="button" id="boton-solicitar" class="boton-guardar">SOLICITAR</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de plazas registradas -->
                <div class="panel seccion-materiales">
                    <h2 class="titulo-seccion">Solicitudes de Plaza</h2>
                    <div class="panel-cuerpo">
                        <div class="tabla-contenedor">
                            <table class="tabla">
                                <thead>
                                    <tr>
                                        <th width="5%">Seleccionar</th>
                                        <th>Puesto</th>
                                        <th>Justificación</th>
                                        <th>Fecha</th>
                                        <th>Estatus</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-solicitudes">
                                    <!-- Contenido cargado por JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Botones de acción debajo de la tabla -->
                        <div class="contenedor-botones-accion" style="margin-top: 20px; display: flex; justify-content: center; gap: 15px;">
                            <button type="button" id="boton-editar" class="boton-guardar" disabled style="background-color: #003366; color: white;">EDITAR</button>
                            <button type="button" id="boton-enviar" class="boton-guardar" disabled style="background-color: #003366; color: white;">ENVIAR</button>
                            <button type="button" id="boton-eliminar" class="boton-guardar" disabled style="background-color: #003366; color: white;">ELIMINAR</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal para editar solicitud -->
        <div id="modal-editar" class="modal">
            <div class="modal-contenido">
                <h3 class="modal-titulo">Editar Solicitud</h3>
                <form id="formulario-editar">
                    <input type="hidden" id="editar_plaza_id" name="plaza_id">
                    <input type="hidden" id="editar_aprobacion_id" name="aprobacion_id">
                    
                    <div class="campo-formulario">
                        <label for="editar_puesto">Puesto</label>
                        <input type="text" id="editar_puesto" name="puesto" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="editar_justificacion">Justificación</label>
                        <textarea id="editar_justificacion" name="justificacion" required></textarea>
                    </div>

                    <div class="modal-botones">
                        <button type="button" id="boton-actualizar" class="boton-guardar">ACTUALIZAR</button>
                        <button type="button" id="boton-cancelar-editar" class="boton-cancelar">CANCELAR</button>
                    </div>
                </form>
            </div>
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

        <!-- Pie de pagina -->
        <footer class="pie-pagina">
            <div class="contenedor">
                <div class="derechos">
                    <p>&copy; 2024 Centro Comunitario Juan Diego. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="../js/menu.js"></script>
    <script src="../js/verificar-sesión.js"></script>
    <script src="../js/coordinador-solicitud-plazas.js"></script>
</body>

</html>
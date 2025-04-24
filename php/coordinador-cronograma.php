<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Cronograma</title>
    <link rel="stylesheet" href="../css/estilos-formularios.css">
    <link rel="stylesheet" href="../css/coordinador.css">
    <link rel="stylesheet" href="../css/estilos-adicionales.css">
    <style>
        tr.selected {
            background-color: #f0f8ff;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-contenido {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
        }
        .modal-titulo {
            margin-top: 0;
            color: #003366;
        }
        .modal-botones {
            margin-top: 20px;
            text-align: right;
        }
        .modal-mensaje {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="pagina-contenedor">
        <nav class="navegacion">
            <div class="contenedor">
                <a href="../html/coordinador.html" class="logo">
                    <img src="../img/empresa.png" class="logo-img" alt="Logo Centro Comunitario Juan Diego">
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
        
        <main class="contenido-principal">
            <div class="contenedor">
                <h1 class="titulo-principal">Cronograma de Actividades</h1>

                <!-- Sección de Cronograma -->
                <div class="panel seccion-metas">
                    <h2 class="titulo-seccion">Registro de Actividades</h2>
                    <div class="panel-cuerpo">
                        <form class="formulario-metas" id="form-actividad">
                            <!-- Campos ocultos para IDs -->
                            <input type="hidden" id="planeacion-id" name="planeacion_id">
                            <input type="hidden" id="cronograma-id" name="cronograma_id">
                            
                            <div class="campo-formulario">
                                <label for="nombre-actividad">Nombre de la actividad</label>
                                <input type="text" id="nombre-actividad" name="nombre" 
                                       >
                            </div>

                            <div class="campo-formulario">
                                <label for="descripcion-actividad">Descripción</label>
                                <input type="text" id="descripcion-actividad" name="descripcion" 
                                       >
                            </div>

                            <div class="campo-formulario">
                                <label for="fecha-actividad">Fecha</label>
                                <input type="date" id="fecha-actividad" name="fecha" required>
                            </div>

                            <div class="contenedor-boton">
                            <button type="button" class="boton-agregar" onclick="agregarActividad()">AGREGAR</button>
                            </div>
                        </form>

                        <div class="tabla-contenedor">
                            <table class="tabla">
                                <thead>
                                    <tr>
                                        <th width="5%">Seleccionar</th>
                                        <th>Nombre de la actividad</th>
                                        <th>Descripción</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-actividades">
                                    <!-- Las actividades se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Botones de acción bajo la tabla -->
                        <div class="contenedor-botones-accion" style="margin-top: 20px; display: flex; justify-content: center; gap: 15px;">
                            <button type="button" id="boton-editar" class="boton-guardar" disabled>EDITAR</button>
                            <button type="button" id="boton-eliminar" class="boton-guardar" disabled>ELIMINAR</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal para editar actividad -->
        <div id="modal-editar" class="modal">
            <div class="modal-contenido">
                <h3 class="modal-titulo">Editar Actividad</h3>
                <form id="formulario-editar">
                    <input type="hidden" id="editar_actividad_id" name="actividad_id">
                    
                    <div class="campo-formulario">
                        <label for="editar_nombre">Nombre de la actividad</label>
                        <input type="text" id="editar_nombre" name="nombre" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="editar_descripcion">Descripción</label>
                        <input type="text" id="editar_descripcion" name="descripcion" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="editar_fecha">Fecha</label>
                        <input type="date" id="editar_fecha" name="fecha" required>
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
                    ¿Está seguro de eliminar esta actividad? Esta acción no se puede deshacer.
                </div>
                <div class="modal-botones">
                    <button id="modal-confirmar" class="boton-guardar">CONFIRMAR</button>
                    <button id="modal-cancelar" class="boton-cancelar">CANCELAR</button>
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
    </div>

    <script src="../js/menu.js"></script>
    <script src="../js/coordinador-cronograma-funciones.js"></script>
</body>
</html>
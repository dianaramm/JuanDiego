<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Recursos Humanos</title>
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
                <h1 class="titulo-principal">Gestión de Recursos Humanos</h1>

                <!-- Panel de registro/edición de empleado -->
                <div class="panel">
                    <div class="panel-cabecera">
                        <h2 class="panel-titulo" id="formulario-titulo">Registrar nuevo empleado</h2>
                    </div>
                    <div class="panel-cuerpo">
                        <form id="formulario-empleado" class="formulario">
                            <input type="hidden" id="modo" name="modo" value="registrar">
                            <input type="hidden" id="usuario_id" name="usuario_id" value="">
                            <input type="hidden" id="tipo-usuario" name="tipo-usuario" value="5">

                            <div class="grid-formulario">
                                <div class="campo-formulario">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" maxlength="100"
                                        placeholder="Nombre del empleado" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="apellido-paterno">Apellido paterno</label>
                                    <input type="text" id="apellido-paterno" name="apellido-paterno" maxlength="100"
                                        placeholder="Apellido paterno" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="apellido-materno">Apellido materno</label>
                                    <input type="text" id="apellido-materno" name="apellido-materno" maxlength="100"
                                        placeholder="Apellido materno" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="correo">Correo electrónico</label>
                                    <input type="email" id="correo" name="correo" maxlength="100"
                                        placeholder="Correo electrónico" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="telefono">Teléfono</label>
                                    <input type="tel" id="telefono" name="telefono" maxlength="10"
                                        placeholder="10 dígitos" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="area">Área</label>
                                    <select id="area" name="area" required>
                                        <option value="">Seleccionar área</option>
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
                                    <label for="cargo">Cargo</label>
                                    <input type="text" id="cargo" name="cargo" maxlength="100"
                                        placeholder="Cargo o puesto" required>
                                </div>
                            </div>

                            <div class="contenedor-botones-principales">
                                <button type="button" id="boton-cancelar" class="boton-cancelar">CANCELAR</button>
                                <button type="submit" id="boton-guardar" class="boton-guardar">GUARDAR</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Panel de empleados registrados -->
                <div class="panel mt-4">
                    <div class="panel-cabecera">
                        <h2 class="panel-titulo">Empleados activos</h2>
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
                        <div class="tabla-responsive">
                            <table class="tabla">
                                <thead>
                                    <tr>
                                        <th width="5%">Seleccionar</th>
                                        <th>Nombre completo</th>
                                        <th>Correo</th>
                                        <th>Teléfono</th>
                                        <th>Área</th>
                                        <th>Cargo</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-empleados">
                                    <!-- Se cargará dinámicamente con JS -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Botones de acción bajo la tabla -->
                        <div class="contenedor-botones-principales" style="margin-top: 20px;">
                            <button type="button" id="boton-editar" class="boton-guardar" disabled>EDITAR</button>
                            <button type="button" id="boton-eliminar" class="boton-cancelar" disabled>ELIMINAR</button>
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

    <!-- Modal para editar empleado -->
    <div id="modal-editar" class="modal">
        <div class="modal-contenido">
            <h3 class="modal-titulo">Editar Empleado</h3>
            <form id="formulario-editar">
                <input type="hidden" id="editar_usuario_id" name="usuario_id">

                <div class="campo-formulario">
                    <label for="editar_nombre">Nombre</label>
                    <input type="text" id="editar_nombre" name="nombre" maxlength="100" required>
                </div>

                <div class="campo-formulario">
                    <label for="editar_apellido_paterno">Apellido paterno</label>
                    <input type="text" id="editar_apellido_paterno" name="apellido-paterno" maxlength="100" required>
                </div>

                <div class="campo-formulario">
                    <label for="editar_apellido_materno">Apellido materno</label>
                    <input type="text" id="editar_apellido_materno" name="apellido-materno" maxlength="100" required>
                </div>

                <div class="campo-formulario">
                    <label for="editar_correo">Correo electrónico</label>
                    <input type="email" id="editar_correo" name="correo" maxlength="100" required>
                </div>

                <div class="campo-formulario">
                    <label for="editar_telefono">Teléfono</label>
                    <input type="tel" id="editar_telefono" name="telefono" maxlength="10" required>
                </div>

                <div class="campo-formulario">
                    <label for="editar_area">Área</label>
                    <select id="editar_area" name="area" required>
                        <option value="">Seleccionar área</option>
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
                    <label for="editar_cargo">Cargo</label>
                    <input type="text" id="editar_cargo" name="cargo" maxlength="100" required>
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
                ¿Está seguro de dar de baja a este empleado? Esta acción no se puede deshacer.
            </div>
            <div class="modal-botones">
                <button id="modal-confirmar" class="boton-guardar">CONFIRMAR</button>
                <button id="modal-cancelar" class="boton-cancelar">CANCELAR</button>
            </div>
        </div>
    </div>

    <script src="../js/menu.js"></script>
    <script src="../js/verificar-sesión.js"></script>
    <script src="../js/finanzas-recursos-humanos.js"></script>
</body>

</html>
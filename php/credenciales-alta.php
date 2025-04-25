<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Gestión de Planeación</title>
    <link rel="stylesheet" href="../css/estilos-formularios.css">
    <link rel="stylesheet" href="../css/breadcrumbs.css">
    <link rel="stylesheet" href="../css/admin-unicos.css">
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

        <aside id="menu-lateral" class="menu-lateral">
            <ul>
                <li><a href="#"><span class="icono-menu icono-usuario"></span> Usuario</a></li>
                <li><a href="../html/administracion.html"><span class="icono-menu icono-menu"></span> Menú principal</a>
                </li>
                <li><a href="../php/cerrar-sesion.php"><span class="icono-menu icono-salir"></span> Cerrar sesión</a>
                </li>
                <li><a id="boton-ocultar"><span class="icono-menu icono-ocultar"></span> Ocultar</a></li>
            </ul>
        </aside>

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
                        <a href="../php/solicitudes-plazas-admin.php" class="enlace-secundario enlace-con-submenu">
                            Solicitud de Plazas
                            <span class="indicador-submenu"></span>
                        </a>
                    </li>
            </div>
        </div>

        <main class="contenido-principal">
            <div class="contenedor">

                <h1 class="titulo-principal">Nuevos usuarios</h1>

                <div class="panel">
                    <div class="panel-cabecera">
                        <h2 class="panel-titulo">Registro de nuevo usuario</h2>
                    </div>
                    <div class="panel-cuerpo">
                        <form class="formulario" action="../php/consulta-alta-credencial.php" method="POST">
                            <div class="grid-formulario">
                                <div class="campo-formulario">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="apellido-paterno">Apellido paterno</label>
                                    <input type="text" id="apellido-paterno" name="apellido-paterno" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="apellido-materno">Apellido materno</label>
                                    <input type="text" id="apellido-materno" name="apellido-materno" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="telefono">Teléfono</label>
                                    <input type="telefono" id="telefono" name="telefono" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="correo">Correo</label>
                                    <input type="email" id="correo" name="correo" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="area">Área</label>
                                    <select id="area" name="area" required>
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

                                <div class="campo-formulario">
                                    <label for="tipo-usuario">Tipo de usuario</label>
                                    <select id="tipo-usuario" name="tipo-usuario" required>
                                        <option value="" disabled selected>Seleccionar tipo de usuario</option>
                                        <option value="Administrador de planeacion">Administrador de planeación</option>
                                        <option value="Administrador de finanzas">Administrador de finanzas</option>
                                        <option value="Coordinador">Coordinador</option>
                                    </select>
                                </div>

                                <div class="campo-formulario campo-clave">
                                    <label for="usuario">Usuario</label>
                                    <div class="contenedor-clave">
                                        <input type="text" id="usuario" name="usuario" required readonly>
                                        <button type="button" id="boton-generar-usuario"
                                            class="boton-generar">Generar</button>
                                    </div>
                                </div>
                                <div class="campo-formulario campo-clave">
                                    <label for="contraseña">Contraseña</label>
                                    <div class="contenedor-clave">
                                        <input type="text" id="contraseña" name="password" required readonly>
                                        <button type="button" id="boton-generar-contraseña"
                                            class="boton-generar">Generar</button>
                                    </div>
                                </div>

                                <div class="contenedor-boton">
                                    <button type="submit" class="boton-guardar">GUARDAR</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="panel mt-4">
                    <div class="panel-cabecera">
                        <h2 class="panel-titulo">Credenciales registradas</h2>
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
                                        <th>Usuario</th>
                                        <th>Contraseña</th>
                                        <th>Area</th>
                                        <th>Tipo de usuario</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-credenciales">
                                    <?php
                                    if (file_exists('../php/consulta-listar-credencial.php')) {
                                        include '../php/consulta-listar-credencial.php';
                                    } else {
                                        echo "<tr><td colspan='7'>Error: No se pudo cargar la lista de credenciales.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Botones de acción debajo de la tabla -->
                        <div class="contenedor-botones-accion">
                            <button type="button" id="boton-editar" class="boton-guardar" disabled>EDITAR</button>
                            <button type="button" id="boton-eliminar" class="boton-rechazar" disabled>ELIMINAR</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal para editar credencial -->
        <div id="modal-editar" class="modal">
            <div class="modal-contenido">
                <h3 class="modal-titulo">Editar Usuario</h3>
                <form id="formulario-editar">
                    <input type="hidden" id="editar_usuario_id" name="usuario_id">

                    <div class="campo-formulario">
                        <label for="editar_nombre">Nombre</label>
                        <input type="text" id="editar_nombre" name="nombre" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="editar_apellido_paterno">Apellido paterno</label>
                        <input type="text" id="editar_apellido_paterno" name="apellido-paterno" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="editar_apellido_materno">Apellido materno</label>
                        <input type="text" id="editar_apellido_materno" name="apellido-materno" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="editar_telefono">Teléfono</label>
                        <input type="tel" id="editar_telefono" name="telefono" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="editar_correo">Correo</label>
                        <input type="email" id="editar_correo" name="correo" required>
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
                        <label for="editar_tipo_usuario">Tipo de usuario</label>
                        <select id="editar_tipo_usuario" name="tipo-usuario" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="Administrador de planeacion">Administrador de planeación</option>
                            <option value="Administrador de finanzas">Administrador de finanzas</option>
                            <option value="Coordinador">Coordinador</option>
                        </select>
                    </div>

                    <div class="campo-formulario">
                        <label for="editar_contraseña">Contraseña</label>
                        <div class="contenedor-clave">
                            <input type="text" id="editar_contraseña" name="password" required>
                            <button type="button" id="editar_boton_generar_contraseña"
                                class="boton-generar">Generar</button>
                        </div>
                    </div>

                    <div class="modal-botones">
                        <button type="button" id="boton-actualizar" class="boton-guardar">ACTUALIZAR</button>
                        <button type="button" id="boton-cancelar-editar" class="boton-cancelar">CANCELAR</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal de confirmación para eliminar -->
        <div id="modal-confirmacion" class="modal">
            <div class="modal-contenido">
                <div id="modal-mensaje" class="modal-mensaje">
                    ¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.
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
    <script src="../js/verificar-sesión.js"></script>
    <script src="../js/validaciones-formulario-alta-usuario.js"></script>
    <script src="../js/credenciales.js"></script>
    <script src="../js/breadcrumbs.js"></script>

</body>

</html>
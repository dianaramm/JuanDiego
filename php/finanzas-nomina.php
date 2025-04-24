<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Gestión de Nómina</title>
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
                <h1 class="titulo-principal">Gestión de Nómina</h1>

                <!-- Panel de selección de empleado para nómina -->
                <div class="panel">
                    <div class="panel-cabecera">
                        <h2 class="panel-titulo">Seleccionar Empleado</h2>
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
                        <!-- Botón para agregar empleado -->
                        <div style="text-align: left; margin-bottom: 20px;">
                            <button type="button" id="boton-agregar-empleado" class="boton-guardar">AGREGAR
                                EMPLEADO</button>
                        </div>

                        <div class="tabla-responsive">
                            <table class="tabla">
                                <thead>
                                    <tr>
                                        <th>Seleccionar</th>
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
                    </div>
                </div>

                <!-- Panel de registro/edición de nómina -->
                <div class="panel mt-4">
                    <div class="panel-cabecera">
                        <h2 class="panel-titulo" id="formulario-titulo">Registrar Nómina</h2>
                    </div>
                    <div class="panel-cuerpo">
                        <form id="formulario-nomina" class="formulario">
                            <input type="hidden" id="modo" name="modo" value="registrar">
                            <input type="hidden" id="nomina_id" name="nomina_id" value="">
                            <input type="hidden" id="usuario_id" name="usuario_id" value="">

                            <div class="grid-formulario">
                                <div class="campo-formulario">
                                    <label for="nombre-empleado">Empleado</label>
                                    <input type="text" id="nombre-empleado" readonly disabled>
                                </div>

                                <div class="campo-formulario">
                                    <label for="fecha-ingreso">Fecha de Ingreso</label>
                                    <input type="date" id="fecha-ingreso" name="fecha_ingreso" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="sueldo">Sueldo Mensual</label>
                                    <input type="number" id="sueldo" name="sueldo" placeholder="Sueldo bruto" min="0"
                                        step="0.01" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="imss">Aportación IMSS</label>
                                    <input type="number" id="imss" name="imss" placeholder="Aportación al IMSS" min="0"
                                        step="0.01" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="sar">Aportación SAR</label>
                                    <input type="number" id="sar" name="sar" placeholder="Aportación al SAR" min="0"
                                        step="0.01" required>
                                </div>

                                <div class="campo-formulario">
                                    <label for="infonavit">Aportación INFONAVIT</label>
                                    <input type="number" id="infonavit" name="infonavit"
                                        placeholder="Aportación al INFONAVIT" min="0" step="0.01" required>
                                </div>
                            </div>

                            <div class="contenedor-botones-principales">
                                <button type="button" id="boton-cancelar" class="boton-cancelar">CANCELAR</button>
                                <button type="submit" id="boton-guardar" class="boton-guardar" disabled>GUARDAR</button>
                                <button type="button" id="boton-generar-reporte" class="boton-guardar" disabled>GENERAR
                                    REPORTE</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Panel de nóminas registradas -->
                <div class="panel mt-4">
                    <div class="panel-cabecera">
                        <h2 class="panel-titulo">Nóminas Registradas</h2>
                    </div>
                    <div class="panel-cuerpo">
                        <div class="tabla-responsive">
                            <table class="tabla">
                                <thead>
                                    <tr>
                                        <th>Seleccionar</th>
                                        <th>Empleado</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Sueldo</th>
                                        <th>IMSS</th>
                                        <th>SAR</th>
                                        <th>INFONAVIT</th>

                                    </tr>
                                </thead>
                                <tbody id="tabla-nominas">
                                    <!-- Se cargará dinámicamente con JS -->
                                </tbody>
                            </table>
                        </div>

                        <div class="contenedor-botones-principales mt-3">
                            <button type="button" id="boton-editar-nomina" class="boton-guardar"
                                disabled>EDITAR</button>
                            <button type="button" id="boton-eliminar-nomina" class="boton-cancelar"
                                disabled>ELIMINAR</button>
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

    <!-- Modal de edición de nómina -->
    <div id="modal-editar" class="modal">
        <div class="modal-contenido">
            <h3 class="modal-titulo">Editar Nómina</h3>
            <form id="formulario-editar-nomina">
                <input type="hidden" id="editar_nomina_id" name="nomina_id">
                <input type="hidden" id="editar_usuario_id" name="usuario_id">

                <div class="campo-formulario">
                    <label for="editar_nombre_empleado">Empleado</label>
                    <input type="text" id="editar_nombre_empleado" readonly disabled>
                </div>

                <div class="campo-formulario">
                    <label for="editar_fecha_ingreso">Fecha de Ingreso</label>
                    <input type="date" id="editar_fecha_ingreso" name="fecha_ingreso" required>
                </div>

                <div class="campo-formulario">
                    <label for="editar_sueldo">Sueldo Mensual</label>
                    <input type="number" id="editar_sueldo" name="sueldo" min="0" step="0.01" required>
                </div>

                <div class="campo-formulario">
                    <label for="editar_imss">Aportación IMSS</label>
                    <input type="number" id="editar_imss" name="imss" min="0" step="0.01" required>
                </div>

                <div class="campo-formulario">
                    <label for="editar_sar">Aportación SAR</label>
                    <input type="number" id="editar_sar" name="sar" min="0" step="0.01" required>
                </div>

                <div class="campo-formulario">
                    <label for="editar_infonavit">Aportación INFONAVIT</label>
                    <input type="number" id="editar_infonavit" name="infonavit" min="0" step="0.01" required>
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
                ¿Está seguro de eliminar esta nómina? Esta acción no se puede deshacer.
            </div>
            <div class="modal-botones">
                <button id="modal-confirmar" class="boton-guardar">CONFIRMAR</button>
                <button id="modal-cancelar" class="boton-cancelar">CANCELAR</button>
            </div>
        </div>
    </div>

    <!-- Modal para agregar empleado -->
    <div id="modal-agregar-empleado" class="modal">
        <div class="modal-contenido" style="max-width: 800px; width: 90%;">
            <h3 class="modal-titulo">Registrar Nuevo Empleado</h3>
            <form id="formulario-agregar-empleado" class="formulario">
                <input type="hidden" id="agregar_modo" name="modo" value="registrar">
                <input type="hidden" id="agregar_tipo-usuario" name="tipo-usuario" value="5">

                <div class="grid-formulario">
                    <div class="campo-formulario">
                        <label for="agregar_nombre">Nombre</label>
                        <input type="text" id="agregar_nombre" name="nombre" maxlength="100"
                            placeholder="Nombre del empleado" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="agregar_apellido-paterno">Apellido paterno</label>
                        <input type="text" id="agregar_apellido-paterno" name="apellido-paterno" maxlength="100"
                            placeholder="Apellido paterno" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="agregar_apellido-materno">Apellido materno</label>
                        <input type="text" id="agregar_apellido-materno" name="apellido-materno" maxlength="100"
                            placeholder="Apellido materno" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="agregar_correo">Correo electrónico</label>
                        <input type="email" id="agregar_correo" name="correo" maxlength="100"
                            placeholder="Correo electrónico" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="agregar_telefono">Teléfono</label>
                        <input type="tel" id="agregar_telefono" name="telefono" maxlength="10" placeholder="10 dígitos"
                            required>
                    </div>

                    <div class="campo-formulario">
                        <label for="agregar_area">Área</label>
                        <select id="agregar_area" name="area" required>
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
                        <label for="agregar_cargo">Cargo</label>
                        <input type="text" id="agregar_cargo" name="cargo" maxlength="100" placeholder="Cargo o puesto"
                            required>
                    </div>
                </div>

                <div class="modal-botones">
                    <button type="submit" id="boton-guardar-empleado" class="boton-guardar">GUARDAR</button>
                    <button type="button" id="boton-cancelar-agregar" class="boton-cancelar">CANCELAR</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/menu.js"></script>
    <script src="../js/verificar-sesión.js"></script>
    <script src="../js/finanzas-nomina.js"></script>
    <script src="../js/finanzas-nomina-agregar-empleado.js"></script>
</body>

</html>
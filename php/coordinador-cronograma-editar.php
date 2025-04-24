<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Editar Actividad</title>
    <link rel="stylesheet" href="../css/estilos-formularios.css">
    <link rel="stylesheet" href="../css/coordinador.css">
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
                <li><a href="coordinador-cronograma.php"><span class="icono-menu icono-regresar"></span> Regresar</a></li>
                <li><a href="../php/cerrar-sesion.php"><span class="icono-menu salir"></span> Cerrar sesión</a></li>
                <li><a id="boton-ocultar"><span class="icono-menu ocultar"></span> Ocultar</a></li>
            </ul>
        </div>

        <main class="contenido-principal">
            <div class="contenedor">
                <h1 class="titulo-principal">Editar Actividad</h1>

                <div class="panel seccion-metas">
                    <h2 class="titulo-seccion">Datos de la Actividad</h2>
                    <div class="panel-cuerpo">
                        <form class="formulario-metas" id="form-actividad">
                            <div class="campo-formulario">
                                <label for="nombre-actividad">Nombre de la actividad</label>
                                <input type="text" id="nombre-actividad" name="nombre" 
                                       placeholder="Nombre de la actividad" required>
                            </div>

                            <div class="campo-formulario">
                                <label for="descripcion-actividad">Descripción</label>
                                <input type="text" id="descripcion-actividad" name="descripcion" 
                                       placeholder="Descripción de la actividad" required>
                            </div>

                            <div class="campo-formulario">
                                <label for="fecha-actividad">Fecha</label>
                                <input type="date" id="fecha-actividad" name="fecha" required>
                            </div>

                            <div class="contenedor-botones-principales">
                                <button type="button" class="boton-guardar" onclick="guardarCronograma()">GUARDAR</button>
                            </div>
                        </form>
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
    <script src="../js/coordinador-cronograma-acciones.js"></script>
</body>
</html>
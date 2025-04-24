<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Planeación Anual</title>
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
                <li><a href="../php/cerrar-sesion.php"><span class="icono-menu salir"></span> Cerrar sesión</a></li>
                <li><a id="boton-ocultar"><span class="icono-menu ocultar"></span> Ocultar</a></li>
            </ul>
        </div>

        <main class="contenido-principal">
            <div class="contenedor">
                <h1 class="titulo-principal">Planeación anual</h1>

                <!-- Ficha Descriptiva -->
                <div class="panel seccion-ficha">
                    <h2 class="titulo-seccion">Ficha Descriptiva</h2>
                    <div class="panel-cuerpo">
                        <form class="formulario-planeacion" id="form-planeacion">
                            <input type="hidden" id="planeacion-id" name="planeacion_id" value="<?php echo $_GET['id'] ?? ''; ?>">
                            <input type="hidden" id="cronograma-id" name="cronograma_id">
                            
                            <div class="campo-formulario">
                                <label for="nombre-planeacion">Nombre de planeación</label>
                                <input type="text" id="nombre-planeacion" name="nombre-planeacion" 
                                        required>
                            </div>

                            <div class="campo-formulario">
                                <label for="tipo-planeacion">Tipo de planeación</label>
                                <select id="tipo-planeacion" name="tipo-planeacion" required>
                                    <option value="" disabled selected>Seleccionar tipo</option>
                                    <option value="Educativo">Educativo</option>
                                    <option value="Salud">Salud</option>
                                    <option value="Productivo">Productivo</option>
                                    <option value="Social">Social</option>
                                </select>
                            </div>

                            <div class="campo-formulario">
                                <label for="importancia">Importancia de la planeación</label>
                                <input type="text" id="importancia" name="importancia"  required>
                            </div>

                            <div class="campo-formulario">
                                <label for="descripcion">Descripción</label>
                                <textarea id="descripcion" name="descripcion"  required></textarea>
                            </div>

                            <div class="seccion-objetivos">
                                <h3 class="subtitulo-seccion">Objetivo</h3>
                                <div class="campo-formulario">
                                    <label for="objetivo-general">Objetivo general</label>
                                    <input type="text" id="objetivo-general" name="objetivo-general"
                                           placeholder="El objetivo general debe describir en la totalidad de la finalidad de planeación"
                                           required>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Botones principales -->
                <div class="contenedor-botones-principales">
                    <button type="button" class="boton-cancelar" onclick="cancelarEdicion()">CANCELAR</button>
                    <button type="button" class="boton-guardar" onclick="guardarPlaneacion()">GUARDAR</button>
                    <button type="button" class="boton-enviar" onclick="enviarPlaneacion()">ENVIAR</button>
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
    <script src="../js/coordinador-editar-planeacion.js"></script>
    <script src="../js/verificar-sesión.js"></script>
</body>
</html>
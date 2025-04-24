<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Comunitario Juan Diego - Planeación</title>
    <link rel="stylesheet" href="../css/estilos-formularios.css">
    <link rel="stylesheet" href="../css/coordinador.css">
</head>
<body>
    <div class="pagina-contenedor">
        <nav class="navegacion">
            <div class="contenedor">
                <a href="../html/coordinador.html" class="logo">
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
                <h1 class="titulo-principal">Planeación anual</h1>

                <div class="panel seccion-estatus-planeacion-anual">
                    <h2 class="titulo-seccion">Estatus de planeación</h2>
                    <div class="panel-cuerpo">
                        <div class="tabla-contenedor">
                            <table class="tabla">
                                <thead>
                                    <tr>
                                        <th>Nombre de planeación</th>
                                        <th>Fecha</th>
                                        <th>Estatus</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php include '../php/consulta-coordinador-listar-planeacion-estatus.php'; ?>
                                </tbody>
                            </table>
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
   
</body>
</html>
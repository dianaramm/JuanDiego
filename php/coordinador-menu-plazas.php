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
</head>

<bo>
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



    <!-- Menú lateral ahora dentro de un aside -->
    <aside id="menu-lateral" class="menu-lateral">
        <ul>
            <li><a href=""><span class="icono-menu icono-usuario"></span> Cargando...</a></li>
            <li><a href="../php/cerrar-sesion.php"><span class="icono-menu icono-salir"></span> Cerrar sesión</a></li>
            <li><a id="boton-ocultar"><span class="icono-menu icono-ocultar"></span> Ocultar</a></li>
        </ul>
    </aside>

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
                    <a href="../php/coordinador-menu-planeacion-anual.php" class="enlace-secundario enlace-con-submenu">
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


    <div class="contenedor-principal">
        <main class="contenido-principal">
            <div class="contenedor">
                <h1 class="titulo-principal">Plazas</h1>
                <div class="grid-tarjetas">

                    <a href="coordinador-solicitud-plazas.php" class="tarjeta">
                        <img src="../img/icono-empleadonuevo.png" alt="Icono Gráfico" class="icono-tarjeta">
                        <h2>Solicitud de plazas</h2>
                    </a>

                    <a href="coordinador-listar-plazas.php" class="tarjeta">
                        <img src="../img/icono-empleado.png" alt="Icono Carta" class="icono-tarjeta">
                        <h2>Historial de plazas</h2>
                    </a>
                </div>
            </div>
        </main>
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
    </body>

</html>
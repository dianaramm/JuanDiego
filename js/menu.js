
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuLateral = document.getElementById('menu-lateral');
    const botonOcultar = document.getElementById('boton-ocultar');
    
    // Variable para almacenar si el usuario es superusuario
    let esSuperUsuario = false;
    
    // Función para actualizar la visibilidad del botón de menú
    function actualizarVisibilidadBoton() {
        if (menuLateral.classList.contains('activo')) {
            menuToggle.classList.remove('visible');
        } else {
            menuToggle.classList.add('visible');
        }
    }

    // Menú esté visible al cargar la página
    menuLateral.classList.add('activo');
    actualizarVisibilidadBoton();

    menuToggle.addEventListener('click', function() {
        menuLateral.classList.add('activo');
        actualizarVisibilidadBoton();
    });

    botonOcultar.addEventListener('click', function() {
        menuLateral.classList.remove('activo');
        actualizarVisibilidadBoton();
    });

    // Script para manejar el cambio de pestañas
    const pestanas = document.querySelectorAll('.pestana');
    const vistas = document.querySelectorAll('.vista');

    pestanas.forEach(pestana => {
        pestana.addEventListener('click', () => {
            // Remover clase activa de todas las pestañas y vistas
            pestanas.forEach(p => p.classList.remove('activa'));
            vistas.forEach(v => v.classList.remove('activa'));

            // Agregar clase activa a la pestaña clickeada
            pestana.classList.add('activa');

            // Mostrar la vista correspondiente
            const vistaId = pestana.getAttribute('data-vista');
            document.querySelector(`.vista-${vistaId}`).classList.add('activa');
        });
    });

    // Obtener nombre del usuario y tipo_id desde la sesión
    fetch('../php/menu_sesion.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log(data); // Para depuración
            const usuarioElemento = document.querySelector('.icono-usuario').parentElement;
            
            // Actualizar el nombre de usuario en el menú
            if (data.usuario && data.usuario !== 'Invitado') {
                usuarioElemento.innerHTML = `<span class="icono-menu icono-usuario"></span> ${data.usuario}`;
            } else {
                usuarioElemento.innerHTML = `<span class="icono-menu icono-usuario"></span> Usuario no identificado`;
            }
            
            // Verificar si es superusuario (tipo_id = 1)
            if (data.tipo_id === 1) {
                esSuperUsuario = true;
                
                // Buscar el elemento "Menú principal" para modificarlo
                const menuPrincipalElemento = document.querySelector('.icono-menu').parentElement;
                if (menuPrincipalElemento && menuPrincipalElemento.textContent.includes('Menú principal')) {
                    menuPrincipalElemento.href = '../html/superusuario.html';
                }
                
                // Agregar opción "Volver a Superusuario" después del menú principal
                const menuLateral = document.querySelector('.menu-lateral ul');
                if (menuLateral) {
                    const volverSuperusuarioItem = document.createElement('li');
                    volverSuperusuarioItem.innerHTML = `<a href="../html/superusuario.html"><span class="icono-menu icono-regresar"></span> Volver a Superusuario</a>`;
                    
                    // Insertar después del menú principal
                    const menuPrincipalItem = menuLateral.querySelector('li:nth-child(2)');
                    if (menuPrincipalItem) {
                        menuLateral.insertBefore(volverSuperusuarioItem, menuPrincipalItem.nextSibling);
                    } else {
                        menuLateral.appendChild(volverSuperusuarioItem);
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error al cargar el usuario:', error);
            const usuarioElemento = document.querySelector('.icono-usuario').parentElement;
            usuarioElemento.innerHTML = `<span class="icono-menu icono-usuario"></span> Error al cargar usuario`;
        }); 

        document.addEventListener('DOMContentLoaded', function() {
            // Estructura de navegación del sitio
            const estructura = {
                'administracion.html': {
                    nombre: 'Administración',
                    subpaginas: {
                        'credenciales-alta.php': 'Alta de Credenciales',
                        'credenciales-activas.php': 'Credenciales Activas',
                        'credenciales-editar.php': 'Editar Credencial',
                        'planeacion-anual.php': 'Control de Planeación Anual',
                        'carta-descriptiva.php': 'Historial de Planeación',
                        'solicitudes-plazas-admin.php': 'Solicitudes de Plazas',
                        'ver-planeacion-anual-admin.php': 'Resumen de Planeación'
                    }
                },
                'coordinador.html': {
                    nombre: 'Coordinación',
                    subpaginas: {
                        'coordinador-formulario-planeacion-anual.php': 'Registro de Planeación',
                        'coordinador-menu-planeacion-anual.php': 'Planeación Anual',
                        'coordinador-estatus-planeacion-anual.php': 'Estatus de Planeación',
                        'coordinador-editar-planeacion-anual.php': 'Editar Planeación',
                        'coordinador-cronograma.php': 'Cronograma de Actividades',
                        'coordinador-cronograma-editar.php': 'Editar Actividad',
                        'coordinador-menu-plazas.php': 'Plazas',
                        'coordinador-solicitud-plazas.php': 'Solicitud de Plaza',
                        'coordinador-listar-plazas.php': 'Historial de Plazas'
                    }
                },
                'finanzas.html': {
                    nombre: 'Finanzas',
                    subpaginas: {
                        'finanzas-recursos-humanos.php': 'Recursos Humanos',
                        'finanzas-nomina.php': 'Nómina',
                        'finanzas-reportes.php': 'Reportes'
                    }
                }
            };
        
            // Obtener ruta actual
            const rutaCompleta = window.location.pathname;
            const archivoActual = rutaCompleta.split('/').pop();
            
            // Elemento para el nombre de la página actual
            const paginaActualElement = document.getElementById('pagina-actual');
            // Elemento para el módulo actual
            const moduloActualElement = document.getElementById('modulo-actual');
            
            // Determinar módulo y página
            let moduloEncontrado = false;
            let nombrePagina = archivoActual; // Valor por defecto
        
            // Buscar en la estructura
            for (const [modulo, datos] of Object.entries(estructura)) {
                // Verificar si la página actual es un módulo principal
                if (archivoActual === modulo) {
                    if (paginaActualElement) paginaActualElement.textContent = datos.nombre;
                    if (moduloActualElement) {
                        moduloActualElement.textContent = "Inicio";
                        moduloActualElement.href = "../index.html";
                    }
                    moduloEncontrado = true;
                    break;
                }
                
                // Verificar si la página actual es una subpágina
                if (datos.subpaginas[archivoActual]) {
                    if (paginaActualElement) paginaActualElement.textContent = datos.subpaginas[archivoActual];
                    if (moduloActualElement) {
                        moduloActualElement.textContent = datos.nombre;
                        moduloActualElement.href = `../html/${modulo}`;
                    }
                    moduloEncontrado = true;
                    break;
                }
            }
            
            // Si no se encontró en la estructura, mostrar nombre genérico
            if (!moduloEncontrado) {
                if (paginaActualElement) {
                    // Convertir nombre de archivo a un formato más amigable
                    const nombreFormateado = archivoActual
                        .replace('.php', '')
                        .replace('.html', '')
                        .replace(/-/g, ' ')
                        .split(' ')
                        .map(palabra => palabra.charAt(0).toUpperCase() + palabra.slice(1))
                        .join(' ');
                        
                    paginaActualElement.textContent = nombreFormateado;
                }
                
                if (moduloActualElement) {
                    moduloActualElement.textContent = "Página Principal";
                    moduloActualElement.href = "../index.html";
                }
            }
        });
}); 
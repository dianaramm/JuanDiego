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
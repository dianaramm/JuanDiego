document.addEventListener('DOMContentLoaded', function() {
    const filtroArea = document.getElementById('filtro-area');
    const tablaPlaneacion = document.getElementById('tabla-planeacion');
    const botonGenerarReporte = document.getElementById('boton-generar-reporte');

    // Filtrar por área
    filtroArea.addEventListener('change', function() {
        const areaSeleccionada = this.value;
        
        // Mostrar indicador de carga
        tablaPlaneacion.innerHTML = "<tr><td colspan='5'><div class='cargando'>Cargando datos...</div></td></tr>";
        
        fetch(`consulta-listar-carta-descriptiva.php?area=${encodeURIComponent(areaSeleccionada)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.text();
            })
            .then(data => {
                tablaPlaneacion.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                tablaPlaneacion.innerHTML = "<tr><td colspan='5'>Error al cargar los datos de planeación.</td></tr>";
                mostrarNotificacion('error', 'Error de conexión', 'No se pudieron cargar los datos. Por favor, intente nuevamente.');
            });
    });

    // Manejar generación de reporte para la planeación seleccionada
    if (botonGenerarReporte) {
        botonGenerarReporte.addEventListener('click', function() {
            const planeacionSeleccionada = document.querySelector('input[name="planeacion_seleccionada"]:checked');
            
            if (!planeacionSeleccionada) {
                mostrarNotificacion('error', 'Selección requerida', 'Por favor, seleccione una planeación para generar el PDF.');
                return;
            }
            
            const planeacionId = planeacionSeleccionada.value;
            generarPDF(planeacionId);
        });
    }

    // Función para mostrar notificaciones
    function mostrarNotificacion(tipo, titulo, mensaje) {
        // Crear elementos de notificación
        const overlay = document.createElement('div');
        overlay.className = 'notificacion-overlay';
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
        overlay.style.display = 'flex';
        overlay.style.justifyContent = 'center';
        overlay.style.alignItems = 'center';
        overlay.style.zIndex = '9999';
        
        const notificacion = document.createElement('div');
        notificacion.className = `notificacion notificacion-${tipo}`;
        notificacion.style.backgroundColor = '#fff';
        notificacion.style.borderRadius = '5px';
        notificacion.style.boxShadow = '0 0 10px rgba(0,0,0,0.3)';
        notificacion.style.padding = '20px';
        notificacion.style.maxWidth = '600px';
        notificacion.style.width = '90%';
        
        notificacion.innerHTML = `
            <div class="notificacion-titulo" style="font-size: 18px; font-weight: bold; margin-bottom: 10px; color: ${tipo === 'error' ? '#ff0000' : '#003366'}">${titulo}</div>
            <div class="notificacion-mensaje" style="margin-bottom: 15px;">${mensaje}</div>
            <button class="notificacion-boton" style="padding: 8px 16px; background-color: #003366; color: white; border: none; border-radius: 4px; cursor: pointer;">Aceptar</button>
        `;
        
        overlay.appendChild(notificacion);
        document.body.appendChild(overlay);
        
        // Cerrar notificación al hacer clic
        const botonCerrar = notificacion.querySelector('.notificacion-boton');
        botonCerrar.addEventListener('click', function() {
            document.body.removeChild(overlay);
        });
        
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                document.body.removeChild(overlay);
            }
        });
    }
});

// Función para generar PDF
function generarPDF(planeacionId) {
    // Abrir la URL en una nueva pestaña
    window.open(`generar-carta-descriptiva-pdf.php?id=${planeacionId}`, '_blank');
}
/**
 * Script para gestionar la aprobación y rechazo de planeaciones
 */
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const modalConfirmacion = document.getElementById('modal-confirmacion');
    const modalMensaje = document.getElementById('modal-mensaje');
    const botonConfirmar = document.getElementById('modal-confirmar');
    const botonCancelar = document.getElementById('modal-cancelar');
    
    // Variables globales
    let accionActual = '';
    let planeacionId = null;
    
    // Asignar eventos
    botonConfirmar.addEventListener('click', procesarAccion);
    botonCancelar.addEventListener('click', cerrarModal);
    
    /**
     * Función para mostrar el modal de confirmación
     */
    function mostrarModal(mensaje, accion, id) {
        modalMensaje.textContent = mensaje;
        accionActual = accion;
        planeacionId = id;
        modalConfirmacion.style.display = 'block';
    }
    
    /**
     * Función para cerrar el modal
     */
    function cerrarModal() {
        modalConfirmacion.style.display = 'none';
    }
    
    /**
     * Función para procesar la acción seleccionada
     */
    function procesarAccion() {
        if (!planeacionId || !accionActual) {
            cerrarModal();
            return;
        }
        
        // Determinar el estatus según la acción
        const estatus = accionActual === 'aprobar' ? 4 : 5;
        
        // Realizar la petición al servidor
        fetch('actualizar-estatus-planeacion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${planeacionId}&estatus=${estatus}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            cerrarModal();
            
            if (data.success) {
                // Crear notificación de éxito
                const mensaje = accionActual === 'aprobar' 
                    ? 'La planeación ha sido aprobada correctamente.'
                    : 'La planeación ha sido rechazada.';
                
                mostrarNotificacion(
                    accionActual === 'aprobar' ? 'exito' : 'info',
                    accionActual === 'aprobar' ? 'Planeación aprobada' : 'Planeación rechazada',
                    mensaje
                );
                
                // Redirigir después de un tiempo
                setTimeout(() => {
                    window.location.href = 'planeacion-anual.php';
                }, 2000);
            } else {
                // Mostrar error
                mostrarNotificacion(
                    'error',
                    'Error',
                    'No se pudo actualizar el estatus de la planeación.'
                );
            }
        })
        .catch(error => {
            cerrarModal();
            console.error('Error:', error);
            
            // Mostrar error
            mostrarNotificacion(
                'error',
                'Error de conexión',
                'Hubo un problema al procesar la solicitud.'
            );
        });
    }
    
    /**
     * Mostrar una notificación estilizada
     */
    function mostrarNotificacion(tipo, titulo, mensaje) {
        // Crear elementos de la notificación
        const notificacion = document.createElement('div');
        notificacion.className = `notificacion notificacion-${tipo}`;
        
        // Determinar icono según el tipo
        let iconoSVG = '';
        switch(tipo) {
            case 'exito':
                iconoSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
                break;
            case 'error':
                iconoSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
                break;
            case 'info':
                iconoSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
                break;
            default:
                iconoSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
        }
        
        // Construir el HTML de la notificación
        notificacion.innerHTML = `
            <div class="notificacion-icono">${iconoSVG}</div>
            <div class="notificacion-contenido">
                <div class="notificacion-titulo">${titulo}</div>
                <div class="notificacion-mensaje">${mensaje}</div>
            </div>
            <button class="notificacion-cerrar">&times;</button>
        `;
        
        // Agregar estilos si no existen
        if (!document.getElementById('estilos-notificacion')) {
            const estilos = document.createElement('style');
            estilos.id = 'estilos-notificacion';
            estilos.textContent = `
                .notificacion {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    min-width: 300px;
                    background-color: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    display: flex;
                    padding: 16px;
                    margin-bottom: 10px;
                    transform: translateX(120%);
                    transition: transform 0.3s ease;
                    z-index: 1000;
                }
                
                .notificacion.mostrar {
                    transform: translateX(0);
                }
                
                .notificacion-icono {
                    margin-right: 12px;
                    display: flex;
                    align-items: flex-start;
                }
                
                .notificacion-contenido {
                    flex: 1;
                }
                
                .notificacion-titulo {
                    font-weight: bold;
                    margin-bottom: 4px;
                }
                
                .notificacion-mensaje {
                    color: #666;
                }
                
                .notificacion-cerrar {
                    background: transparent;
                    border: none;
                    font-size: 20px;
                    cursor: pointer;
                    margin-left: 10px;
                    line-height: 0.5;
                    padding: 8px 4px;
                }
                
                .notificacion-exito .notificacion-icono {
                    color: #4caf50;
                }
                
                .notificacion-error .notificacion-icono {
                    color: #f44336;
                }
                
                .notificacion-info .notificacion-icono {
                    color: #2196f3;
                }
            `;
            document.head.appendChild(estilos);
        }
        
        // Agregar la notificación al documento
        document.body.appendChild(notificacion);
        
        // Mostrar notificación después de un breve retraso (para que se aplique la transición)
        setTimeout(() => {
            notificacion.classList.add('mostrar');
        }, 10);
        
        // Configurar botón de cerrar
        const botonCerrar = notificacion.querySelector('.notificacion-cerrar');
        botonCerrar.addEventListener('click', () => {
            cerrarNotificacion(notificacion);
        });
        
        // Cerrar automáticamente después de 5 segundos
        setTimeout(() => {
            cerrarNotificacion(notificacion);
        }, 5000);
    }
    
    /**
     * Cerrar una notificación con animación
     */
    function cerrarNotificacion(notificacion) {
        notificacion.classList.remove('mostrar');
        setTimeout(() => {
            if (notificacion.parentNode) {
                notificacion.parentNode.removeChild(notificacion);
            }
        }, 300);
    }
    
    // Exponer funciones globalmente
    window.aprobarPlaneacion = function(id) {
        mostrarModal(
            '¿Está seguro de aprobar esta planeación? Esta acción no se puede deshacer.',
            'aprobar',
            id
        );
    };
    
    window.rechazarPlaneacion = function(id) {
        mostrarModal(
            '¿Está seguro de rechazar esta planeación? Esta acción no se puede deshacer.',
            'rechazar',
            id
        );
    };
});
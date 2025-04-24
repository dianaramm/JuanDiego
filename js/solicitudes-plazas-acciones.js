
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los elementos del DOM
    const tablaSolicitudes = document.getElementById('tabla-solicitudes');
    const botonAprobar = document.getElementById('boton-aprobar');
    const botonRechazar = document.getElementById('boton-rechazar');
    const modalConfirmacion = document.getElementById('modal-confirmacion');
    const modalMensaje = document.getElementById('modal-mensaje');
    const botonConfirmar = document.getElementById('modal-boton-confirmar');
    const botonCancelar = document.getElementById('modal-boton-cancelar');
    const radioButtons = document.querySelectorAll('input[name="solicitud"]');
    
    let accionActual = '';
    let aprobacionIdSeleccionada = null;
    
    // Función para habilitar/deshabilitar botones según selección
    function actualizarEstadoBotones() {
        if (aprobacionIdSeleccionada) {
            botonAprobar.disabled = false;
            botonRechazar.disabled = false;
        } else {
            botonAprobar.disabled = true;
            botonRechazar.disabled = true;
        }
    }
    
    // Manejar selección de filas con radio buttons
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            // Limpiar selección anterior
            document.querySelectorAll('tr.selected').forEach(row => {
                row.classList.remove('selected');
            });
            
            // Resaltar fila seleccionada
            if (this.checked) {
                aprobacionIdSeleccionada = this.value;
                const fila = this.closest('tr');
                fila.classList.add('selected');
            } else {
                aprobacionIdSeleccionada = null;
            }
            
            actualizarEstadoBotones();
        });
    });
    
    // Funciones para mostrar y ocultar modal
    function mostrarModal(mensaje, accion) {
        modalMensaje.textContent = mensaje;
        accionActual = accion;
        modalConfirmacion.style.display = 'block';
    }
    
    function ocultarModal() {
        modalConfirmacion.style.display = 'none';
    }
    
    // Event listeners para botones de acción
    botonAprobar.addEventListener('click', function() {
        if (!aprobacionIdSeleccionada) return;
        mostrarModal('¿Está seguro de aprobar esta solicitud de plaza? Esta acción no se puede deshacer.', 'aprobar');
    });
    
    botonRechazar.addEventListener('click', function() {
        if (!aprobacionIdSeleccionada) return;
        mostrarModal('¿Está seguro de rechazar esta solicitud de plaza? Esta acción no se puede deshacer.', 'rechazar');
    });
    
    // Event listeners para modal
    botonConfirmar.addEventListener('click', function() {
        procesarAccion();
    });
    
    botonCancelar.addEventListener('click', function() {
        ocultarModal();
    });
    
    // Función para procesar aprobación o rechazo
    function procesarAccion() {
        if (!aprobacionIdSeleccionada) return;
        
        fetch('../php/consulta-admin-aprobar-plazas.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `aprobacion_id=${aprobacionIdSeleccionada}&accion=${accionActual}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(`Solicitud ${accionActual === 'aprobar' ? 'aprobada' : 'rechazada'} correctamente`);
                window.location.reload();
            } else {
                alert(`Error: ${data.error || 'No se pudo procesar la solicitud'}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        })
        .finally(() => {
            ocultarModal();
        });
    }
});
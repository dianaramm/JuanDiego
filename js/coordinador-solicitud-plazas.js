/**
 * Script para gestionar las solicitudes de plazas de coordinadores
 */
document.addEventListener('DOMContentLoaded', function() {
    // Referencias DOM
    const formularioPlaza = document.getElementById('formulario-plaza');
    const botonSolicitar = document.getElementById('boton-solicitar');
    const tablaSolicitudes = document.getElementById('tabla-solicitudes');
    const modalEditar = document.getElementById('modal-editar');
    const formularioEditar = document.getElementById('formulario-editar');
    const botonActualizar = document.getElementById('boton-actualizar');
    const botonCancelarEditar = document.getElementById('boton-cancelar-editar');
    const modalConfirmacion = document.getElementById('modal-confirmacion');
    const modalMensaje = document.getElementById('modal-mensaje');
    const modalConfirmar = document.getElementById('modal-confirmar');
    const modalCancelar = document.getElementById('modal-cancelar');
    
    // Botones de acción bajo la tabla
    const botonEditar = document.getElementById('boton-editar');
    const botonEliminar = document.getElementById('boton-eliminar');
    const botonEnviar = document.getElementById('boton-enviar');
    
    // Variables globales
    let accionActual = '';
    let idSeleccionado = null;
    let plazaIdSeleccionado = null;
    
    // Cargar solicitudes al iniciar
    cargarSolicitudes();
    
    // Event Listeners
    botonSolicitar.addEventListener('click', guardarSolicitud);
    botonActualizar.addEventListener('click', actualizarSolicitud);
    botonCancelarEditar.addEventListener('click', cerrarModalEditar);
    modalConfirmar.addEventListener('click', ejecutarAccionConfirmada);
    modalCancelar.addEventListener('click', cerrarModalConfirmacion);
    
    // Eventos para los botones de acción
    botonEditar.addEventListener('click', function() {
        if (idSeleccionado && plazaIdSeleccionado) {
            cargarDatosSolicitud(plazaIdSeleccionado, idSeleccionado);
        }
    });
    
    botonEliminar.addEventListener('click', function() {
        if (idSeleccionado) {
            mostrarConfirmacion('eliminar', idSeleccionado);
        }
    });
    
    botonEnviar.addEventListener('click', function() {
        if (idSeleccionado) {
            mostrarConfirmacion('enviar', idSeleccionado);
        }
    });
    
    /**
     * Carga las solicitudes de plaza del usuario actual
     */
    function cargarSolicitudes() {
        if (!tablaSolicitudes) return;
        
        tablaSolicitudes.innerHTML = '<tr><td colspan="5">Cargando solicitudes...</td></tr>';
        
        fetch('../php/consulta-coordinador-listar-plazas.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.text();
            })
            .then(html => {
                tablaSolicitudes.innerHTML = html;
                inicializarSeleccion();
            })
            .catch(error => {
                console.error('Error:', error);
                tablaSolicitudes.innerHTML = '<tr><td colspan="5">Error al cargar las solicitudes</td></tr>';
            });
    }
    
    /**
     * Inicializa la selección de solicitudes en la tabla
     */
    function inicializarSeleccion() {
        document.querySelectorAll('.seleccion-plaza').forEach(radio => {
            radio.addEventListener('change', function() {
                // Actualizar variables globales con los IDs seleccionados
                idSeleccionado = this.value;
                plazaIdSeleccionado = this.getAttribute('data-plaza');
                
                // Habilitar/deshabilitar botones según la selección
                botonEditar.disabled = false;
                botonEliminar.disabled = false;
                botonEnviar.disabled = false;
            });
        });
    }
    
    /**
     * Guarda una nueva solicitud
     */
    function guardarSolicitud(e) {
        e.preventDefault();
        
        if (!validarFormulario(formularioPlaza)) {
            return;
        }
        
        const formData = new FormData(formularioPlaza);
        formData.append('accion', 'guardar');
        
        // Deshabilitar botón mientras se procesa
        botonSolicitar.disabled = true;
        botonSolicitar.textContent = 'Procesando...';
        
        fetch('../php/consulta-coordinador-guardar-plaza.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarMensaje('Solicitud guardada exitosamente', 'exito');
                formularioPlaza.reset();
                cargarSolicitudes();
                
                // Reiniciar selección
                idSeleccionado = null;
                plazaIdSeleccionado = null;
                botonEditar.disabled = true;
                botonEliminar.disabled = true;
                botonEnviar.disabled = true;
            } else {
                mostrarMensaje(data.error || 'Error al guardar la solicitud', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al procesar la solicitud', 'error');
        })
        .finally(() => {
            botonSolicitar.disabled = false;
            botonSolicitar.textContent = 'SOLICITAR';
        });
    }
    
    /**
     * Carga los datos de una solicitud para editar
     */
    function cargarDatosSolicitud(id, aprobacionId) {
        fetch(`../php/consulta-coordinador-obtener-plaza.php?id=${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Llenar formulario
                    document.getElementById('editar_plaza_id').value = id;
                    document.getElementById('editar_aprobacion_id').value = aprobacionId;
                    document.getElementById('editar_puesto').value = data.solicitud.puesto;
                    document.getElementById('editar_justificacion').value = data.solicitud.justificacion;
                    
                    // Mostrar modal
                    modalEditar.style.display = 'block';
                } else {
                    mostrarMensaje(data.error || 'Error al cargar los datos', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al cargar los datos de la solicitud', 'error');
            });
    }
    
    /**
     * Actualiza una solicitud existente
     */
    function actualizarSolicitud() {
        if (!validarFormulario(formularioEditar)) {
            return;
        }
        
        const formData = new FormData(formularioEditar);
        formData.append('accion', 'actualizar');
        
        fetch('../php/consulta-coordinador-guardar-plaza.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarMensaje('Solicitud actualizada exitosamente', 'exito');
                cerrarModalEditar();
                cargarSolicitudes();
                
                // Reiniciar selección
                idSeleccionado = null;
                plazaIdSeleccionado = null;
                botonEditar.disabled = true;
                botonEliminar.disabled = true;
                botonEnviar.disabled = true;
            } else {
                mostrarMensaje(data.error || 'Error al actualizar la solicitud', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al procesar la solicitud', 'error');
        });
    }
    
    /**
     * Muestra el modal de confirmación para acciones destructivas
     */
    function mostrarConfirmacion(accion, id) {
        accionActual = accion;
        idSeleccionado = id;
        
        if (accion === 'eliminar') {
            modalMensaje.textContent = '¿Está seguro de eliminar esta solicitud? Esta acción no se puede deshacer.';
        } else if (accion === 'enviar') {
            modalMensaje.textContent = '¿Está seguro de enviar esta solicitud para aprobación? Una vez enviada, no podrá modificarla.';
        }
        
        modalConfirmacion.style.display = 'block';
    }
    
    /**
     * Ejecuta la acción después de confirmar
     */
    function ejecutarAccionConfirmada() {
        if (!idSeleccionado || !accionActual) {
            cerrarModalConfirmacion();
            return;
        }
        
        let url = '../php/consulta-coordinador-guardar-plaza.php';
        let formData = new FormData();
        formData.append('id', idSeleccionado);
        formData.append('accion', accionActual);
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                let mensaje = '';
                if (accionActual === 'eliminar') {
                    mensaje = 'Solicitud eliminada exitosamente';
                } else if (accionActual === 'enviar') {
                    mensaje = 'Solicitud enviada exitosamente';
                }
                mostrarMensaje(mensaje, 'exito');
                cargarSolicitudes();
                
                // Reiniciar selección
                idSeleccionado = null;
                plazaIdSeleccionado = null;
                botonEditar.disabled = true;
                botonEliminar.disabled = true;
                botonEnviar.disabled = true;
            } else {
                mostrarMensaje(data.error || `Error al ${accionActual} la solicitud`, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje(`Error al ${accionActual} la solicitud`, 'error');
        })
        .finally(() => {
            cerrarModalConfirmacion();
        });
    }
    
    /**
     * Cierra el modal de edición
     */
    function cerrarModalEditar() {
        modalEditar.style.display = 'none';
        formularioEditar.reset();
    }
    
    /**
     * Cierra el modal de confirmación
     */
    function cerrarModalConfirmacion() {
        modalConfirmacion.style.display = 'none';
        accionActual = '';
    }
    
    /**
     * Valida los campos de un formulario
     */
    function validarFormulario(formulario) {
        let esValido = true;
        
        // Validación básica de campos requeridos
        formulario.querySelectorAll('[required]').forEach(campo => {
            if (!campo.value.trim()) {
                campo.classList.add('campo-error');
                esValido = false;
            } else {
                campo.classList.remove('campo-error');
            }
        });
        
        if (!esValido) {
            mostrarMensaje('Por favor complete todos los campos requeridos', 'error');
        }
        
        return esValido;
    }
    
    /**
     * Muestra un mensaje temporal en la interfaz
     */
    function mostrarMensaje(texto, tipo) {
        const mensaje = document.createElement('div');
        mensaje.className = `mensaje ${tipo}`;
        mensaje.textContent = texto;
        
        // Insertar el mensaje en la página
        document.querySelector('.contenido-principal').insertBefore(mensaje, document.querySelector('.contenido-principal').firstChild);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            mensaje.remove();
        }, 3000);
    }
});
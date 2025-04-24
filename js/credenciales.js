

document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos DOM
    const formularioUsuario = document.querySelector('.formulario');
    const formularioEditar = document.getElementById('formulario-editar');
    const filtroArea = document.getElementById('filtro-area');
    const tablaCredenciales = document.getElementById('tabla-credenciales');
    const modalEditar = document.getElementById('modal-editar');
    const modalConfirmacion = document.getElementById('modal-confirmacion');
    const botonEditar = document.getElementById('boton-editar');
    const botonEliminar = document.getElementById('boton-eliminar');
    const botonCancelarEditar = document.getElementById('boton-cancelar-editar');
    const botonActualizar = document.getElementById('boton-actualizar');
    const modalConfirmar = document.getElementById('modal-confirmar');
    const modalCancelar = document.getElementById('modal-cancelar');
    
    // Botones para generación de contraseña en el modal de edición
    const editarBotonGenerarContrasena = document.getElementById('editar_boton_generar_contraseña');
    
    // Variables globales
    let usuarioIdSeleccionado = null;
    
    // Inicializar eventos
    initEventos();
    initGeneradoresModal();
    
    // Cargar datos iniciales si hay filtro de área
    if (filtroArea) {
        filtroArea.addEventListener('change', cargarCredenciales);
        // Cargar datos iniciales
        cargarCredenciales();
    }
    
    /**
     * Inicializa todos los eventos necesarios
     */
    function initEventos() {
        // Eventos para los botones de la tabla
        if (botonEditar) {
            botonEditar.addEventListener('click', function() {
                const radioSeleccionado = document.querySelector('input[name="credencial"]:checked');
                if (radioSeleccionado) {
                    editarCredencial(radioSeleccionado.value);
                }
            });
        }
        
        if (botonEliminar) {
            botonEliminar.addEventListener('click', function() {
                const radioSeleccionado = document.querySelector('input[name="credencial"]:checked');
                if (radioSeleccionado) {
                    mostrarConfirmacionEliminar(radioSeleccionado.value);
                }
            });
        }
        
        // Manejar la selección de credenciales para habilitar botones
        document.addEventListener('click', function(e) {
            if (e.target && e.target.type === 'radio' && e.target.name === 'credencial') {
                if (botonEditar) botonEditar.disabled = false;
                if (botonEliminar) botonEliminar.disabled = false;
            }
        });
        
        // Evento para actualizar usuario
        if (botonActualizar) {
            botonActualizar.addEventListener('click', actualizarUsuario);
        }
        
        // Eventos para modal
        if (botonCancelarEditar) {
            botonCancelarEditar.addEventListener('click', cerrarModalEditar);
        }
        
        // Eventos para modal de confirmación
        if (modalConfirmar) {
            modalConfirmar.addEventListener('click', confirmarEliminarUsuario);
        }
        
        if (modalCancelar) {
            modalCancelar.addEventListener('click', cerrarModalConfirmacion);
        }
    }
    
    /**
     * Inicializa los generadores de contraseña para el modal
     */
    function initGeneradoresModal() {
        if (editarBotonGenerarContrasena) {
            editarBotonGenerarContrasena.addEventListener('click', function() {
                const contraseña = generarContrasenaAleatoria();
                document.getElementById('editar_contraseña').value = contraseña;
            });
        }
    }
    
    /**
     * Genera una contraseña aleatoria segura
     */
    function generarContrasenaAleatoria() {
        const caracteresPermitidos = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        let contraseña = '';
        for (let i = 0; i < 8; i++) {
            contraseña += caracteresPermitidos.charAt(Math.floor(Math.random() * caracteresPermitidos.length));
        }
        return contraseña;
    }
    
    /**
     * Carga la lista de credenciales filtradas
     */
    function cargarCredenciales() {
        if (!tablaCredenciales) return;
        
        const areaSeleccionada = filtroArea ? filtroArea.value : '';
        
        tablaCredenciales.innerHTML = '<tr><td colspan="7">Cargando credenciales...</td></tr>';
        
        fetch(`../php/consulta-listar-credencial.php?area=${encodeURIComponent(areaSeleccionada)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.text();
            })
            .then(html => {
                tablaCredenciales.innerHTML = html;
                // Reiniciar estado de los botones
                if (botonEditar) botonEditar.disabled = true;
                if (botonEliminar) botonEliminar.disabled = true;
            })
            .catch(error => {
                console.error('Error:', error);
                tablaCredenciales.innerHTML = '<tr><td colspan="7">Error al cargar las credenciales.</td></tr>';
                mostrarMensaje('Error al cargar las credenciales: ' + error.message, 'error');
            });
    }
    
    /**
     * Función para editar una credencial
     */
    function editarCredencial(usuarioId) {
        fetch(`../php/consulta-obtener-usuario.php?id=${usuarioId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Rellenar formulario de edición
                    usuarioIdSeleccionado = usuarioId;
                    
                    document.getElementById('editar_usuario_id').value = data.usuario.usuario_id;
                    document.getElementById('editar_nombre').value = data.usuario.nombre;
                    document.getElementById('editar_apellido_paterno').value = data.usuario.apellido_paterno;
                    document.getElementById('editar_apellido_materno').value = data.usuario.apellido_materno;
                    document.getElementById('editar_telefono').value = data.usuario.telefono;
                    document.getElementById('editar_correo').value = data.usuario.correo;
                    document.getElementById('editar_area').value = data.usuario.area_id;
                    document.getElementById('editar_tipo_usuario').value = data.usuario.cargo;
                    document.getElementById('editar_contraseña').value = data.usuario.contraseña;
                    
                    // Mostrar modal
                    modalEditar.style.display = 'block';
                } else {
                    mostrarMensaje('Error al cargar datos: ' + data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al cargar los datos del usuario', 'error');
            });
    }
    
    /**
     * Muestra el modal de confirmación para eliminar
     */
    function mostrarConfirmacionEliminar(usuarioId) {
        usuarioIdSeleccionado = usuarioId;
        modalConfirmacion.style.display = 'block';
    }
    
    /**
     * Confirma la eliminación de un usuario
     */
    function confirmarEliminarUsuario() {
        if (!usuarioIdSeleccionado) {
            cerrarModalConfirmacion();
            return;
        }
        
        fetch('../php/credenciales-eliminar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `usuario_id=${usuarioIdSeleccionado}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarMensaje('Usuario eliminado correctamente', 'exito');
                cargarCredenciales();
            } else {
                mostrarMensaje(data.error || 'Error al eliminar usuario', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al procesar la solicitud', 'error');
        })
        .finally(() => {
            cerrarModalConfirmacion();
        });
    }
    
    /**
     * Actualiza un usuario existente
     */
    function actualizarUsuario() {
        if (!validarFormulario(formularioEditar)) {
            return;
        }
        
        const formData = new FormData(formularioEditar);
        
        fetch('../php/consulta-editar-credencial.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarMensaje('Usuario actualizado correctamente', 'exito');
                cerrarModalEditar();
                cargarCredenciales();
            } else {
                mostrarMensaje(data.error || 'Error al actualizar el usuario', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al actualizar el usuario', 'error');
        });
    }
    
    /**
     * Valida los campos de un formulario
     */
    function validarFormulario(formulario) {
        let esValido = true;
        let mensajeError = '';
        
        // Validar todos los campos requeridos
        const camposRequeridos = formulario.querySelectorAll('[required]');
        camposRequeridos.forEach(campo => {
            if (!campo.value.trim()) {
                campo.classList.add('campo-error');
                esValido = false;
                mensajeError += `El campo ${campo.id} es requerido.\n`;
            } else {
                campo.classList.remove('campo-error');
            }
        });
        
        // Validar formato de correo
        const campoCorreo = formulario.querySelector('input[type="email"]');
        if (campoCorreo && campoCorreo.value) {
            const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regexCorreo.test(campoCorreo.value)) {
                campoCorreo.classList.add('campo-error');
                esValido = false;
                mensajeError += 'El formato del correo electrónico no es válido.\n';
            }
        }
        
        // Validar formato de teléfono
        const campoTelefono = formulario.querySelector('input[name="telefono"]');
        if (campoTelefono && campoTelefono.value) {
            if (!/^\d{10}$/.test(campoTelefono.value)) {
                campoTelefono.classList.add('campo-error');
                esValido = false;
                mensajeError += 'El teléfono debe contener exactamente 10 dígitos.\n';
            }
        }
        
        if (!esValido) {
            mostrarMensaje(mensajeError || 'Por favor, corrija los errores en el formulario.', 'error');
        }
        
        return esValido;
    }
    
    /**
     * Cierra el modal de edición
     */
    function cerrarModalEditar() {
        modalEditar.style.display = 'none';
        // Eliminar clase de error de todos los campos
        const camposError = formularioEditar.querySelectorAll('.campo-error');
        camposError.forEach(campo => campo.classList.remove('campo-error'));
    }
    
    /**
     * Cierra el modal de confirmación
     */
    function cerrarModalConfirmacion() {
        modalConfirmacion.style.display = 'none';
        usuarioIdSeleccionado = null;
    }
    
    /**
     * Muestra un mensaje temporal
     */
    function mostrarMensaje(texto, tipo) {
        const mensaje = document.createElement('div');
        mensaje.className = `mensaje ${tipo}`;
        mensaje.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 30px;
            border-radius: 5px;
            z-index: 1000;
            background-color: ${tipo === 'error' ? '#ff4444' : '#44aa44'};
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        `;
        mensaje.textContent = texto;
        
        document.body.appendChild(mensaje);
        
        setTimeout(() => {
            mensaje.remove();
        }, 3000);
    }
});
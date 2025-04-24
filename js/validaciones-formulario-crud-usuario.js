// Funciones para manejar las acciones de credenciales
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar los botones de acción de la tabla
    const botonEditar = document.getElementById('boton-editar');
    const botonEliminar = document.getElementById('boton-eliminar');
    const modalEditar = document.getElementById('modal-editar');
    const modalConfirmacion = document.getElementById('modal-confirmacion');
    const botonCancelarEditar = document.getElementById('boton-cancelar-editar');
    const botonCancelarEliminar = document.getElementById('modal-cancelar');
    const botonConfirmarEliminar = document.getElementById('modal-confirmar');
    
    if (botonEditar && botonEliminar) {
        // Habilitar los botones cuando se selecciona un radio button
        document.querySelectorAll('input[name="credencial"]').forEach(radio => {
            radio.addEventListener('change', function() {
                botonEditar.disabled = false;
                botonEliminar.disabled = false;
            });
        });
        
        // Asignar eventos a los botones
        botonEditar.addEventListener('click', function() {
            const radioSeleccionado = document.querySelector('input[name="credencial"]:checked');
            if (radioSeleccionado) {
                editarCredencial(radioSeleccionado.value);
            }
        });
        
        botonEliminar.addEventListener('click', function() {
            const radioSeleccionado = document.querySelector('input[name="credencial"]:checked');
            if (radioSeleccionado) {
                // Mostrar modal de confirmación
                modalConfirmacion.style.display = 'block';
                
                // Guardar el ID del usuario a eliminar
                modalConfirmacion.setAttribute('data-usuario-id', radioSeleccionado.value);
            }
        });
    }
    
    // Agregar eventos para hacer que las filas de la tabla sean clickeables
    function agregarEventosFilas() {
        const filas = document.querySelectorAll('#tabla-credenciales tr');
        
        filas.forEach(fila => {
            // Obtener todas las celdas excepto la primera (que contiene el radio button)
            const celdas = fila.querySelectorAll('td:not(:first-child)');
            const radioButton = fila.querySelector('input[type="radio"]');
            
            if (radioButton && celdas.length > 0) {
                celdas.forEach(celda => {
                    celda.style.cursor = 'pointer'; // Cambiar el cursor para indicar que es clickeable
                    
                    celda.addEventListener('click', function() {
                        // Marcar el radio button
                        radioButton.checked = true;
                        
                        // Disparar el evento change para activar los botones
                        const event = new Event('change');
                        radioButton.dispatchEvent(event);
                    });
                });
            }
        });
    }
    
    // Ejecutar esta función al cargar la página
    agregarEventosFilas();
    
    // Configurar el modal de edición
    if (modalEditar) {
        // Cerrar el modal al hacer clic en el botón cancelar
        if (botonCancelarEditar) {
            botonCancelarEditar.addEventListener('click', function() {
                modalEditar.style.display = 'none';
            });
        }
        
        // Cerrar el modal al hacer clic fuera de él
        window.addEventListener('click', function(event) {
            if (event.target == modalEditar) {
                modalEditar.style.display = 'none';
            }
        });
    }
    
    // Configurar el modal de confirmación para eliminar
    if (modalConfirmacion) {
        // Cerrar el modal al hacer clic en el botón cancelar
        if (botonCancelarEliminar) {
            botonCancelarEliminar.addEventListener('click', function() {
                modalConfirmacion.style.display = 'none';
            });
        }
        
        // Configurar el botón de confirmar eliminación
        if (botonConfirmarEliminar) {
            botonConfirmarEliminar.addEventListener('click', function() {
                const usuarioId = modalConfirmacion.getAttribute('data-usuario-id');
                if (usuarioId) {
                    eliminarCredencial(usuarioId);
                }
                modalConfirmacion.style.display = 'none';
            });
        }
        
        // Cerrar el modal al hacer clic fuera
        window.addEventListener('click', function(event) {
            if (event.target == modalConfirmacion) {
                modalConfirmacion.style.display = 'none';
            }
        });
    }
    
    // Configurar evento para el botón de actualizar en el modal
    const botonActualizar = document.getElementById('boton-actualizar');
    if (botonActualizar) {
        botonActualizar.addEventListener('click', function() {
            const formularioEditar = document.getElementById('formulario-editar');
            if (formularioEditar) {
                // Validar antes de enviar
                if (validarFormulario(formularioEditar)) {
                    // Crear FormData con los datos del formulario
                    const formData = new FormData(formularioEditar);
                    
                    // Enviar actualización
                    fetch('../php/consulta-editar-credencial.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            mostrarNotificacion('exito', 'Usuario actualizado', 'El usuario ha sido actualizado correctamente');
                            
                            // Cerrar el modal y recargar la tabla después de un breve retraso
                            setTimeout(() => {
                                document.getElementById('modal-editar').style.display = 'none';
                                document.getElementById('filtro-area').dispatchEvent(new Event('change'));
                            }, 1500);
                        } else {
                            mostrarNotificacion('error', 'Error', data.error || 'Error al actualizar el usuario');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        mostrarNotificacion('error', 'Error de conexión', 'No se pudo conectar con el servidor');
                    });
                }
            }
        });
    }
    
    // Event listener para generar nueva contraseña en el modal de edición
    const botonGenerarContraseñaEditar = document.getElementById('editar_boton_generar_contraseña');
    if (botonGenerarContraseñaEditar) {
        botonGenerarContraseñaEditar.addEventListener('click', function() {
            const longitud = 8;
            // Solo incluimos letras, números y los caracteres especiales permitidos: ".", "-", "_"
            const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789._-';
            let nuevaContraseña = '';
            
            // Generar contraseña
            for (let i = 0; i < longitud; i++) {
                nuevaContraseña += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
            }
            
            // Asignar la contraseña generada
            document.getElementById('editar_contraseña').value = nuevaContraseña;
            
            // Mostrar notificación
            mostrarNotificacion('exito', 'Contraseña generada', 'Se ha generado una contraseña segura correctamente');
        });
    }
    
    // Agregar validaciones a los campos del modal de edición
    const editarNombre = document.getElementById('editar_nombre');
    const editarApellidoPaterno = document.getElementById('editar_apellido_paterno');
    const editarApellidoMaterno = document.getElementById('editar_apellido_materno');
    const editarTelefono = document.getElementById('editar_telefono');
    const editarCorreo = document.getElementById('editar_correo');
    const editarContraseña = document.getElementById('editar_contraseña');
    
    // Función para validar solo letras y espacios
    function soloLetras(e) {
        const charCode = e.charCode || e.keyCode;
        const char = String.fromCharCode(charCode);
        
        // Permitir letras, espacios y algunos caracteres especiales (á,é,í,ó,ú,ñ)
        const patron = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]$/;
        
        if (!patron.test(char) && charCode !== 8 && charCode !== 9) {
            e.preventDefault();
            return false;
        }
    }

    // Función para validar solo números
    function soloNumeros(e) {
        const charCode = e.charCode || e.keyCode;
        const char = String.fromCharCode(charCode);
        
        if (!/^[0-9]$/.test(char) && charCode !== 8 && charCode !== 9) {
            e.preventDefault();
            return false;
        }
    }
    
    // Aplicar longitud máxima y validaciones a los campos del modal
    if (editarNombre) {
        editarNombre.maxLength = 20;
        editarNombre.addEventListener('keypress', soloLetras);
    }
    
    if (editarApellidoPaterno) {
        editarApellidoPaterno.maxLength = 20;
        editarApellidoPaterno.addEventListener('keypress', soloLetras);
    }
    
    if (editarApellidoMaterno) {
        editarApellidoMaterno.maxLength = 20;
        editarApellidoMaterno.addEventListener('keypress', soloLetras);
    }
    
    if (editarTelefono) {
        editarTelefono.maxLength = 10;
        editarTelefono.addEventListener('keypress', soloNumeros);
    }
    
    // Función para validar correo electrónico
    function validarCorreo(input) {
        const valor = input.value;
        const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!regexCorreo.test(valor) && valor !== '') {
            input.setCustomValidity('Por favor, ingrese un correo electrónico válido');
        } else {
            input.setCustomValidity('');
        }
    }
    
    // Agregar validación a correo electrónico
    if (editarCorreo) {
        editarCorreo.addEventListener('input', function() {
            validarCorreo(this);
        });
        
        editarCorreo.addEventListener('blur', function() {
            validarCorreo(this);
        });
    }
    
    // Si estamos en la página de filtrar credenciales, agregar eventos para recargar
    const filtroArea = document.getElementById('filtro-area');
    if (filtroArea) {
        filtroArea.addEventListener('change', function() {
            // Después de recargar la tabla, volver a agregar eventos a las filas
            setTimeout(agregarEventosFilas, 500);
        });
    }
    
    // Realizar actualización del DOM si se realizan cambios mediante AJAX
    const mutationObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.target.id === 'tabla-credenciales') {
                // Se han agregado o eliminado nodos del tbody
                agregarEventosFilas();
            }
        });
    });
    
    // Configurar el observador para la tabla de credenciales
    const tablaCredenciales = document.getElementById('tabla-credenciales');
    if (tablaCredenciales) {
        mutationObserver.observe(tablaCredenciales, { 
            childList: true,
            subtree: true
        });
    }
});

// Función para editar credencial
function editarCredencial(usuarioId) {
    // Obtener los datos del usuario y mostrarlos en el modal
    fetch(`../php/consulta-obtener-usuario.php?id=${usuarioId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Llenar el formulario del modal con los datos del usuario
                document.getElementById('editar_usuario_id').value = data.usuario.usuario_id;
                document.getElementById('editar_nombre').value = data.usuario.nombre;
                document.getElementById('editar_apellido_paterno').value = data.usuario.apellido_paterno;
                document.getElementById('editar_apellido_materno').value = data.usuario.apellido_materno;
                document.getElementById('editar_telefono').value = data.usuario.telefono;
                document.getElementById('editar_correo').value = data.usuario.correo;
                document.getElementById('editar_area').value = data.usuario.area_id;
                document.getElementById('editar_tipo_usuario').value = data.usuario.cargo;
                document.getElementById('editar_contraseña').value = data.usuario.contraseña || '';
                
                // Mostrar el modal
                const modalEditar = document.getElementById('modal-editar');
                if (modalEditar) {
                    modalEditar.style.display = 'block';
                }
            } else {
                mostrarNotificacion('error', 'Error', data.error || 'No se pudo cargar la información del usuario');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('error', 'Error de conexión', 'No se pudo conectar con el servidor');
        });
}

// Función para eliminar credencial
function eliminarCredencial(usuarioId) {
    fetch('../php/credenciales-eliminar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `usuario_id=${usuarioId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion('exito', 'Usuario eliminado', 'El usuario ha sido eliminado correctamente');
            // Recargar la tabla después de un breve retraso
            setTimeout(() => {
                document.getElementById('filtro-area').dispatchEvent(new Event('change'));
            }, 1500);
        } else {
            mostrarNotificacion('error', 'Error', data.error || 'Error al eliminar el usuario');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('error', 'Error de conexión', 'No se pudo conectar con el servidor');
    });
}

// Función para cargar datos del usuario a editar (usado en páginas de edición separadas)
function cargarDatosUsuario() {
    const params = new URLSearchParams(window.location.search);
    const usuarioId = params.get('id');
    
    if (usuarioId) {
        fetch(`../php/consulta-obtener-usuario.php?id=${usuarioId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Llenar los campos del formulario
                    document.getElementById('usuario_id').value = data.usuario.usuario_id;
                    document.getElementById('nombre').value = data.usuario.nombre;
                    document.getElementById('apellido-paterno').value = data.usuario.apellido_paterno;
                    document.getElementById('apellido-materno').value = data.usuario.apellido_materno;
                    document.getElementById('telefono').value = data.usuario.telefono;
                    document.getElementById('correo').value = data.usuario.correo;
                    document.getElementById('area').value = data.usuario.area_id;
                    document.getElementById('tipo-usuario').value = data.usuario.cargo;
                    document.getElementById('usuario').value = data.usuario.usuario_id;
                    document.getElementById('contraseña').value = data.usuario.contraseña || '';
                } else {
                    mostrarNotificacion('error', 'Error', data.error || 'No se pudo cargar la información del usuario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('error', 'Error de conexión', 'No se pudo conectar con el servidor');
            });
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(tipo, titulo, mensaje) {
    // Crear elementos para la notificación
    const overlay = document.createElement('div');
    overlay.className = 'notificacion-overlay';
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    overlay.style.zIndex = '1000';
    overlay.style.display = 'flex';
    overlay.style.justifyContent = 'center';
    overlay.style.alignItems = 'center';
    
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion notificacion-${tipo}`;
    notificacion.style.backgroundColor = '#fff';
    notificacion.style.borderRadius = '8px';
    notificacion.style.padding = '20px';
    notificacion.style.boxShadow = '0 6px 16px rgba(0, 0, 0, 0.2)';
    notificacion.style.display = 'flex';
    notificacion.style.flexDirection = 'column';
    notificacion.style.alignItems = 'center';
    notificacion.style.textAlign = 'center';
    notificacion.style.maxWidth = '400px';
    
    // Iconos para diferentes tipos de notificaciones
    let icono = '';
    let colorIcono = '#2196f3'; // Color default (info)
    
    switch(tipo) {
        case 'error':
            icono = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
            colorIcono = '#f44336';
            break;
        case 'exito':
            icono = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
            colorIcono = '#4caf50';
            break;
        case 'info':
            icono = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
            colorIcono = '#2196f3';
            break;
    }
    
    // Contenido de la notificación
    notificacion.innerHTML = `
        <div class="notificacion-icono" style="margin-bottom: 15px; color: ${colorIcono};">${icono}</div>
        <div class="notificacion-titulo" style="font-weight: bold; font-size: 18px; margin-bottom: 10px; color: #333;">${titulo}</div>
        <div class="notificacion-mensaje" style="color: #666; margin-bottom: 20px;">${mensaje}</div>
        <button class="notificacion-boton" style="background-color: ${colorIcono}; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: 500;">Aceptar</button>
    `;
    
    // Agregar la notificación al documento
    document.body.appendChild(overlay);
    overlay.appendChild(notificacion);
    
    // Evento para cerrar la notificación
    const boton = notificacion.querySelector('.notificacion-boton');
    boton.addEventListener('click', function() {
        document.body.removeChild(overlay);
    });
    
    // También cerrar al hacer clic fuera
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            document.body.removeChild(overlay);
        }
    });
}

// Función para validar el formulario antes de enviar
function validarFormulario(form) {
    const telefono = form.querySelector('input[name="telefono"]') ? form.querySelector('input[name="telefono"]').value : '';
    const correo = form.querySelector('input[name="correo"]') ? form.querySelector('input[name="correo"]').value : '';
    const nombre = form.querySelector('input[name="nombre"]') ? form.querySelector('input[name="nombre"]').value : '';
    const apellidoPaterno = form.querySelector('input[name="apellido-paterno"]') ? form.querySelector('input[name="apellido-paterno"]').value : '';
    const apellidoMaterno = form.querySelector('input[name="apellido-materno"]') ? form.querySelector('input[name="apellido-materno"]').value : '';
    const contraseña = form.querySelector('input[name="contraseña"]') ? form.querySelector('input[name="contraseña"]').value : '';
    
    let isValid = true;
    let mensajesError = [];
    
    // Validar formato de teléfono (10 dígitos)
    if (telefono && !/^\d{10}$/.test(telefono)) {
        mensajesError.push('El teléfono debe contener exactamente 10 dígitos');
        isValid = false;
    }
    
    // Validar formato de correo
    if (correo) {
        const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regexCorreo.test(correo)) {
            mensajesError.push('Por favor ingrese un correo electrónico válido');
            isValid = false;
        }
    }
    
    // Validar longitud mínima de nombres
    if (nombre && nombre.length < 3) {
        mensajesError.push('El nombre debe tener al menos 3 caracteres');
        isValid = false;
    }
    
    if (apellidoPaterno && apellidoPaterno.length < 3) {
        mensajesError.push('El apellido paterno debe tener al menos 3 caracteres');
        isValid = false;
    }
    
    if (apellidoMaterno && apellidoMaterno.length < 3) {
        mensajesError.push('El apellido materno debe tener al menos 3 caracteres');
        isValid = false;
    }
    
    // Validar que el campo de contraseña no esté vacío
    if (!contraseña || contraseña.trim() === '') {
        mensajesError.push('Debe ingresar o generar una contraseña');
        isValid = false;
    }
    
    if (!isValid) {
        mostrarNotificacion('error', 'Error de validación', mensajesError.join('<br>'));
    }
    
    return isValid;
}

// Función para manejar la actualización del usuario (usado en páginas de edición separadas)
function actualizarUsuario(event) {
    event.preventDefault();
    
    // Validar el formulario antes de enviar
    if (!validarFormulario(event.target)) {
        return false;
    }
    
    const formData = new FormData(event.target);
    
    fetch('../php/consulta-editar-credencial.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion('exito', 'Usuario actualizado', 'El usuario ha sido actualizado correctamente');
            
            // Redirigir a la página de credenciales después de un breve retraso
            setTimeout(() => {
                window.location.href = 'credenciales-alta.php';
            }, 2000);
        } else {
            mostrarNotificacion('error', 'Error', data.error || 'Error al actualizar el usuario');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('error', 'Error de conexión', 'No se pudo conectar con el servidor');
    });
    
    return false;
}
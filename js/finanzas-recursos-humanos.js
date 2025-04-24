/**
 * Script para gestionar las operaciones CRUD de empleados
 */
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const formularioEmpleado = document.getElementById('formulario-empleado');
    const formularioEditar = document.getElementById('formulario-editar');
    const formularioTitulo = document.getElementById('formulario-titulo');
    const botonGuardar = document.getElementById('boton-guardar');
    const botonCancelar = document.getElementById('boton-cancelar');
    const tablaEmpleados = document.getElementById('tabla-empleados');
    const filtroArea = document.getElementById('filtro-area');
    const modalEditar = document.getElementById('modal-editar');
    const modalConfirmacion = document.getElementById('modal-confirmacion');
    const modalConfirmar = document.getElementById('modal-confirmar');
    const modalCancelar = document.getElementById('modal-cancelar');
    
    // Referencias a los botones de acción
    const botonEditar = document.getElementById('boton-editar');
    const botonEliminar = document.getElementById('boton-eliminar');
    const botonActualizar = document.getElementById('boton-actualizar');
    const botonCancelarEditar = document.getElementById('boton-cancelar-editar');
    
    // Variables globales
    let empleadoIdSeleccionado = null;
    
    // Inicializar evento de filtro
    if (filtroArea) {
        filtroArea.addEventListener('change', cargarEmpleados);
    }
    
    // Inicializar eventos para los botones de acción
    if (botonEditar) {
        botonEditar.addEventListener('click', function() {
            const radioSeleccionado = document.querySelector('input[name="empleado"]:checked');
            if (radioSeleccionado) {
                editarEmpleado(radioSeleccionado.value);
            }
        });
    }
    
    if (botonEliminar) {
        botonEliminar.addEventListener('click', function() {
            const radioSeleccionado = document.querySelector('input[name="empleado"]:checked');
            if (radioSeleccionado) {
                darDeBajaEmpleado(radioSeleccionado.value);
            }
        });
    }
    
    // Inicializar eventos del modal
    if (botonActualizar) {
        botonActualizar.addEventListener('click', actualizarEmpleado);
    }
    
    if (botonCancelarEditar) {
        botonCancelarEditar.addEventListener('click', cerrarModalEditar);
    }
    
    // Inicializar eventos de formulario
    if (formularioEmpleado) {
        formularioEmpleado.addEventListener('submit', function(e) {
            e.preventDefault();
            guardarEmpleado();
        });
    }
    
    if (botonCancelar) {
        botonCancelar.addEventListener('click', resetearFormulario);
    }
    
    // Eventos del modal de confirmación
    if (modalConfirmar) {
        modalConfirmar.addEventListener('click', confirmarDarDeBaja);
    }
    
    if (modalCancelar) {
        modalCancelar.addEventListener('click', cerrarModalConfirmacion);
    }
    
    // Inicializar validaciones de campos
    inicializarValidaciones();
    
    // Cargar empleados al iniciar
    cargarEmpleados();
    
    /**
     * Inicializa validaciones para los formularios
     */
    function inicializarValidaciones() {
        // Validar que solo se ingresen letras en nombre y apellidos (formulario principal)
        validarCamposTexto('nombre', 'apellido-paterno', 'apellido-materno');
        
        // Validar que solo se ingresen letras en nombre y apellidos (formulario modal)
        validarCamposTexto('editar_nombre', 'editar_apellido_paterno', 'editar_apellido_materno');
        
        // Validar que solo se ingresen números en teléfono (formulario principal)
        validarCampoTelefono('telefono');
        
        // Validar que solo se ingresen números en teléfono (formulario modal)
        validarCampoTelefono('editar_telefono');
        
        // Validar formato de correo al perder el foco (formulario principal)
        validarCampoCorreo('correo');
        
        // Validar formato de correo al perder el foco (formulario modal)
        validarCampoCorreo('editar_correo');
    }
    
    /**
     * Valida campos de texto para que solo acepten letras
     */
    function validarCamposTexto(...ids) {
        ids.forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) {
                elemento.addEventListener('keypress', function(e) {
                    const char = String.fromCharCode(e.charCode);
                    if (!/^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]$/.test(char)) {
                        e.preventDefault();
                    }
                });
                
                // Capitalizar primera letra al perder el foco
                elemento.addEventListener('blur', function() {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                });
            }
        });
    }
    
    /**
     * Valida campo de teléfono para que solo acepte números y límite de 10 dígitos
     */
    function validarCampoTelefono(id) {
        const telefono = document.getElementById(id);
        if (telefono) {
            telefono.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.charCode);
                if (!/^[0-9]$/.test(char)) {
                    e.preventDefault();
                }
                
                // Limitar a 10 dígitos
                if (this.value.length >= 10 && e.which !== 8) {
                    e.preventDefault();
                }
            });
        }
    }
    
    /**
     * Valida el formato de correo electrónico
     */
    function validarCampoCorreo(id) {
        const correo = document.getElementById(id);
        if (correo) {
            correo.addEventListener('blur', function() {
                if (this.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value)) {
                    this.classList.add('campo-error');
                    mostrarMensaje('Formato de correo electrónico inválido', 'error');
                } else {
                    this.classList.remove('campo-error');
                }
            });
        }
    }
    
    /**
     * Carga los empleados en la tabla
     */
    function cargarEmpleados() {
        if (!tablaEmpleados) return;
        
        // Obtener filtro de área
        const areaSeleccionada = filtroArea ? filtroArea.value : '';
        
        // Mostrar indicador de carga
        tablaEmpleados.innerHTML = '<tr><td colspan="6">Cargando empleados...</td></tr>';
        
        // Obtener empleados del servidor
        fetch(`../php/consulta-finanzas-recursos-humanos.php?accion=listar&area=${areaSeleccionada}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    mostrarEmpleados(data.empleados);
                } else {
                    throw new Error(data.error || 'Error al cargar empleados');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tablaEmpleados.innerHTML = `<tr><td colspan="6">Error al cargar empleados: ${error.message}</td></tr>`;
            });
    }
    
    /**
     * Muestra los empleados en la tabla
     */
    function mostrarEmpleados(empleados) {
        if (!tablaEmpleados) return;
        
        if (empleados.length === 0) {
            tablaEmpleados.innerHTML = '<tr><td colspan="6">No hay empleados registrados</td></tr>';
            // Deshabilitar botones de acción
            if (botonEditar) botonEditar.disabled = true;
            if (botonEliminar) botonEliminar.disabled = true;
            return;
        }
        
        let html = '';
        empleados.forEach(empleado => {
            html += `
                <tr>
                    <td class="text-center">
                        <input type="radio" name="empleado" value="${empleado.usuario_id}">
                    </td>
                    <td>${empleado.nombre_completo}</td>
                    <td>${empleado.correo}</td>
                    <td>${empleado.telefono}</td>
                    <td>${empleado.area}</td>
                    <td>${empleado.cargo}</td>
                </tr>
            `;
        });
        
        tablaEmpleados.innerHTML = html;
        
        // Agregar evento a los radio buttons
        document.querySelectorAll('input[name="empleado"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Si está seleccionado, habilitar botones
                if (this.checked) {
                    if (botonEditar) botonEditar.disabled = false;
                    if (botonEliminar) botonEliminar.disabled = false;
                    empleadoIdSeleccionado = this.value;
                }
            });
        });
        
        // Al cargar, deshabilitar botones hasta que se seleccione un empleado
        if (botonEditar) botonEditar.disabled = true;
        if (botonEliminar) botonEliminar.disabled = true;
        empleadoIdSeleccionado = null;
    }
    
    /**
     * Carga los datos de un empleado para editar
     */
    function editarEmpleado(usuarioId) {
        empleadoIdSeleccionado = usuarioId;
        
        // Obtener datos del empleado
        fetch(`../php/consulta-finanzas-recursos-humanos.php?accion=obtener&id=${usuarioId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Llenar el formulario modal
                    const empleado = data.empleado;
                    document.getElementById('editar_usuario_id').value = empleado.usuario_id;
                    document.getElementById('editar_nombre').value = empleado.nombre;
                    document.getElementById('editar_apellido_paterno').value = empleado.apellido_paterno;
                    document.getElementById('editar_apellido_materno').value = empleado.apellido_materno;
                    document.getElementById('editar_correo').value = empleado.correo;
                    document.getElementById('editar_telefono').value = empleado.telefono;
                    document.getElementById('editar_area').value = empleado.area_id;
                    document.getElementById('editar_cargo').value = empleado.cargo;
                    
                    // Mostrar modal
                    modalEditar.style.display = 'block';
                } else {
                    throw new Error(data.error || 'Error al cargar los datos del empleado');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al cargar los datos del empleado: ' + error.message, 'error');
            });
    }
    
    /**
     * Cierra el modal de edición
     */
    function cerrarModalEditar() {
        modalEditar.style.display = 'none';
        
        // Limpiar campo-error de todos los campos
        const camposError = document.querySelectorAll('#formulario-editar .campo-error');
        camposError.forEach(campo => campo.classList.remove('campo-error'));
    }
    
    /**
     * Actualiza los datos de un empleado desde el modal
     */
    function actualizarEmpleado() {
        if (!validarFormulario(formularioEditar)) {
            return;
        }
        
        // Preparar datos del formulario
        const datos = {
            modo: 'actualizar',
            usuario_id: document.getElementById('editar_usuario_id').value,
            nombre: document.getElementById('editar_nombre').value,
            'apellido-paterno': document.getElementById('editar_apellido_paterno').value,
            'apellido-materno': document.getElementById('editar_apellido_materno').value,
            correo: document.getElementById('editar_correo').value,
            telefono: document.getElementById('editar_telefono').value,
            area: document.getElementById('editar_area').value,
            cargo: document.getElementById('editar_cargo').value,
            'tipo-usuario': "5" // Forzar a empleado general (tipo_id = 5)
        };
        
        // Deshabilitar botón mientras se procesa
        botonActualizar.disabled = true;
        
        fetch('../php/consulta-finanzas-recursos-humanos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarMensaje(data.message || 'Datos del empleado actualizados correctamente', 'exito');
                cerrarModalEditar();
                cargarEmpleados();
            } else {
                throw new Error(data.error || 'Error al actualizar el empleado');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al actualizar el empleado: ' + error.message, 'error');
        })
        .finally(() => {
            botonActualizar.disabled = false;
        });
    }
    
    /**
     * Prepara el modal para dar de baja un empleado
     */
    function darDeBajaEmpleado(usuarioId) {
        empleadoIdSeleccionado = usuarioId;
        modalConfirmacion.style.display = 'block';
    }
    
    /**
     * Confirma dar de baja a un empleado
     */
    function confirmarDarDeBaja() {
        if (!empleadoIdSeleccionado) {
            cerrarModalConfirmacion();
            return;
        }
        
        const datos = {
            modo: 'baja',
            usuario_id: empleadoIdSeleccionado
        };
        
        fetch('../php/consulta-finanzas-recursos-humanos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarMensaje('Empleado dado de baja exitosamente', 'exito');
                cargarEmpleados();
            } else {
                throw new Error(data.error || 'Error al dar de baja al empleado');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al dar de baja al empleado: ' + error.message, 'error');
        })
        .finally(() => {
            cerrarModalConfirmacion();
        });
    }
    
    /**
     * Cierra el modal de confirmación
     */
    function cerrarModalConfirmacion() {
        modalConfirmacion.style.display = 'none';
    }
    
    /**
     * Guarda un nuevo empleado
     */
    function guardarEmpleado() {
        if (!validarFormulario(formularioEmpleado)) {
            return;
        }
        
        // Preparar datos del formulario
        const datos = {
            modo: document.getElementById('modo').value,
            usuario_id: document.getElementById('usuario_id').value,
            nombre: document.getElementById('nombre').value,
            'apellido-paterno': document.getElementById('apellido-paterno').value,
            'apellido-materno': document.getElementById('apellido-materno').value,
            correo: document.getElementById('correo').value,
            telefono: document.getElementById('telefono').value,
            area: document.getElementById('area').value,
            cargo: document.getElementById('cargo').value,
            'tipo-usuario': "5" // Forzar a empleado general (tipo_id = 5)
        };
        
        // Deshabilitar botón mientras se procesa
        botonGuardar.disabled = true;
        
        fetch('../php/consulta-finanzas-recursos-humanos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarMensaje(data.message || 'Empleado guardado exitosamente', 'exito');
                resetearFormulario();
                cargarEmpleados();
            } else {
                throw new Error(data.error || 'Error al guardar el empleado');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al guardar el empleado: ' + error.message, 'error');
        })
        .finally(() => {
            botonGuardar.disabled = false;
        });
    }
    
    /**
     * Valida los campos del formulario
     */
    function validarFormulario(formulario) {
        let esValido = true;
        const camposRequeridos = formulario.querySelectorAll('[required]');
        
        // Validar campos requeridos
        camposRequeridos.forEach(campo => {
            if (!campo.value.trim()) {
                campo.classList.add('campo-error');
                esValido = false;
            } else {
                campo.classList.remove('campo-error');
            }
        });
        
        // Validar formato de correo
        const correo = formulario.querySelector('input[type="email"]');
        if (correo && correo.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo.value)) {
            correo.classList.add('campo-error');
            mostrarMensaje('Formato de correo electrónico inválido', 'error');
            esValido = false;
        }
        
        // Validar longitud del teléfono
        const telefono = formulario.querySelector('input[type="tel"]');
        if (telefono && telefono.value && telefono.value.length !== 10) {
            telefono.classList.add('campo-error');
            mostrarMensaje('El teléfono debe tener 10 dígitos', 'error');
            esValido = false;
        }
        
        if (!esValido) {
            mostrarMensaje('Por favor complete todos los campos requeridos correctamente', 'error');
        }
        
        return esValido;
    }
    
    /**
     * Resetea el formulario a su estado inicial
     */
    function resetearFormulario() {
        formularioEmpleado.reset();
        document.getElementById('modo').value = 'registrar';
        document.getElementById('usuario_id').value = '';
        formularioTitulo.textContent = 'Registrar nuevo empleado';
        
        // Quitar clase error de todos los campos
        document.querySelectorAll('.campo-error').forEach(campo => {
            campo.classList.remove('campo-error');
        });
        
        // Desmarcar radio button seleccionado
        const radioSeleccionado = document.querySelector('input[name="empleado"]:checked');
        if (radioSeleccionado) {
            radioSeleccionado.checked = false;
        }
        
        // Deshabilitar botones de acción
        if (botonEditar) botonEditar.disabled = true;
        if (botonEliminar) botonEliminar.disabled = true;
        empleadoIdSeleccionado = null;
    }
    
    /**
     * Muestra un mensaje de notificación
     */
    function mostrarMensaje(texto, tipo) {
        const mensaje = document.createElement('div');
        mensaje.className = `mensaje ${tipo}`;
        mensaje.textContent = texto;
        
        // Insertar al inicio del contenido principal
        const contenidoPrincipal = document.querySelector('.contenido-principal');
        contenidoPrincipal.insertBefore(mensaje, contenidoPrincipal.firstChild);
        
        // Quitar mensaje después de 3 segundos
        setTimeout(() => {
            mensaje.remove();
        }, 3000);
    }
});
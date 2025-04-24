/**
 * Script para manejar la funcionalidad de agregar empleado desde nómina
 */
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const botonAgregarEmpleado = document.getElementById('boton-agregar-empleado');
    const modalAgregarEmpleado = document.getElementById('modal-agregar-empleado');
    const formularioAgregarEmpleado = document.getElementById('formulario-agregar-empleado');
    const botonCancelarAgregar = document.getElementById('boton-cancelar-agregar');
    
    // Inicializar eventos
    if (botonAgregarEmpleado) {
        botonAgregarEmpleado.addEventListener('click', abrirModalAgregarEmpleado);
    }
    
    if (botonCancelarAgregar) {
        botonCancelarAgregar.addEventListener('click', cerrarModalAgregarEmpleado);
    }
    
    if (formularioAgregarEmpleado) {
        formularioAgregarEmpleado.addEventListener('submit', guardarEmpleado);
    }
    
    // Inicializar validaciones para los campos del formulario
    inicializarValidaciones();
    
    /**
     * Inicializa validaciones para los campos del formulario
     */
    function inicializarValidaciones() {
        // Validar que solo se ingresen letras en nombre y apellidos
        validarCamposTexto('agregar_nombre', 'agregar_apellido-paterno', 'agregar_apellido-materno');
        
        // Validar que solo se ingresen números en teléfono
        validarCampoTelefono('agregar_telefono');
        
        // Validar formato de correo al perder el foco
        validarCampoCorreo('agregar_correo');
        
        // Agregar validación para todos los campos requeridos
        const camposRequeridos = formularioAgregarEmpleado.querySelectorAll('[required]');
        camposRequeridos.forEach(campo => {
            campo.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.classList.add('campo-error');
                } else {
                    this.classList.remove('campo-error');
                }
            });
        });
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
                    if (!this.value.trim()) {
                        this.classList.add('campo-error');
                    } else {
                        this.classList.remove('campo-error');
                        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                    }
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
            
            // Validar al perder el foco
            telefono.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.classList.add('campo-error');
                } else if (this.value.length !== 10) {
                    this.classList.add('campo-error');
                    mostrarMensaje('El teléfono debe tener 10 dígitos', 'error');
                } else {
                    this.classList.remove('campo-error');
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
                if (!this.value.trim()) {
                    this.classList.add('campo-error');
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value)) {
                    this.classList.add('campo-error');
                    mostrarMensaje('Formato de correo electrónico inválido', 'error');
                } else {
                    this.classList.remove('campo-error');
                }
            });
        }
    }
    
    /**
     * Abre el modal para agregar un nuevo empleado
     */
    function abrirModalAgregarEmpleado() {
        // Limpiar formulario por si hay datos previos
        formularioAgregarEmpleado.reset();
        
        // Quitar clases de error
        const camposError = formularioAgregarEmpleado.querySelectorAll('.campo-error');
        camposError.forEach(campo => campo.classList.remove('campo-error'));
        
        // Mostrar modal
        modalAgregarEmpleado.style.display = 'block';
    }
    
    /**
     * Cierra el modal de agregar empleado
     */
    function cerrarModalAgregarEmpleado() {
        modalAgregarEmpleado.style.display = 'none';
    }
    
    /**
     * Guarda un nuevo empleado
     */
    function guardarEmpleado(e) {
        e.preventDefault();
        
        if (!validarFormulario(formularioAgregarEmpleado)) {
            return;
        }
        
        // Preparar datos del formulario
        const datos = {
            modo: 'registrar',
            nombre: document.getElementById('agregar_nombre').value,
            'apellido-paterno': document.getElementById('agregar_apellido-paterno').value,
            'apellido-materno': document.getElementById('agregar_apellido-materno').value,
            correo: document.getElementById('agregar_correo').value,
            telefono: document.getElementById('agregar_telefono').value,
            area: document.getElementById('agregar_area').value,
            cargo: document.getElementById('agregar_cargo').value,
            'tipo-usuario': "5" // Forzar a empleado general (tipo_id = 5)
        };
        
        // Deshabilitar botón mientras se procesa
        document.getElementById('boton-guardar-empleado').disabled = true;
        
        // Enviar datos al servidor usando la misma API que usa recursos humanos
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
                cerrarModalAgregarEmpleado();
                
                // Esperar un momento antes de recargar la lista de empleados
                // para asegurar que el servidor ha procesado completamente el nuevo registro
                setTimeout(() => {
                    // Recargar la lista de empleados
                    if (typeof window.cargarEmpleados === 'function') {
                        window.cargarEmpleados();
                    }
                }, 500);
            } else {
                throw new Error(data.error || 'Error al guardar el empleado');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al guardar el empleado: ' + error.message, 'error');
        })
        .finally(() => {
            document.getElementById('boton-guardar-empleado').disabled = false;
        });
    }
    
    /**
     * Valida los campos del formulario
     */
    function validarFormulario(formulario) {
        let esValido = true;
        let mensajeError = '';
        const camposRequeridos = formulario.querySelectorAll('[required]');
        
        // Validar campos requeridos
        camposRequeridos.forEach(campo => {
            if (!campo.value.trim()) {
                campo.classList.add('campo-error');
                esValido = false;
                mensajeError = 'Por favor complete todos los campos requeridos';
            } else {
                campo.classList.remove('campo-error');
            }
        });
        
        // Validar formato de correo
        const correo = formulario.querySelector('input[type="email"]');
        if (correo && correo.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo.value)) {
            correo.classList.add('campo-error');
            esValido = false;
            mensajeError = 'Formato de correo electrónico inválido';
        }
        
        // Validar longitud del teléfono
        const telefono = formulario.querySelector('input[type="tel"]');
        if (telefono && telefono.value && telefono.value.length !== 10) {
            telefono.classList.add('campo-error');
            esValido = false;
            mensajeError = 'El teléfono debe tener 10 dígitos';
        }
        
        // Validar área seleccionada
        const area = document.getElementById('agregar_area');
        if (area && !area.value) {
            area.classList.add('campo-error');
            esValido = false;
            mensajeError = 'Debe seleccionar un área';
        }
        
        if (!esValido) {
            mostrarMensaje(mensajeError || 'Por favor complete todos los campos requeridos correctamente', 'error');
        }
        
        return esValido;
    }
    
    /**
     * Muestra un mensaje de notificación
     * Reutiliza la función existente si está disponible
     */
    function mostrarMensaje(texto, tipo) {
        // Si existe la función en el scope global (desde finanzas-nomina.js) la usamos
        if (window.mostrarMensaje && typeof window.mostrarMensaje === 'function') {
            window.mostrarMensaje(texto, tipo);
            return;
        }
        
        // Si no, implementamos nuestra propia versión
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
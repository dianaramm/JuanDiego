document.addEventListener('DOMContentLoaded', function() {
    const formularioPlaneacion = document.querySelector('.formulario-planeacion');
    const botonGuardar = document.querySelector('.boton-guardar');
    const botonEnviar = document.querySelector('.boton-enviar');
    const botonCancelar = document.querySelector('.boton-cancelar');

    // Variable para controlar si hay una notificación activa
    let notificacionActiva = false;
    
    // Crear el fondo oscuro y el contenedor de notificaciones
    const overlay = document.createElement('div');
    overlay.className = 'notificacion-overlay';
    
    const notificacionContainer = document.createElement('div');
    notificacionContainer.className = 'notificacion-container';
    
    document.body.appendChild(overlay);
    document.body.appendChild(notificacionContainer);
    
    // Estilos para notificaciones
    const estilos = document.createElement('style');
    estilos.innerHTML = `
        .notificacion-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            justify-content: center;
            align-items: center;
        }
        
        .notificacion-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            z-index: 1001;
            width: 400px;
            max-width: 90%;
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
            pointer-events: none;
        }
        
        .notificacion-container.mostrar {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
            pointer-events: auto;
        }
        
        .notificacion {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .notificacion-icono {
            margin-bottom: 15px;
        }
        
        .notificacion-titulo {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .notificacion-mensaje {
            color: #666;
            margin-bottom: 20px;
        }
        
        .notificacion-boton {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .notificacion-boton:hover {
            background-color: #3a7bc8;
        }
        
        .notificacion-error .notificacion-icono svg {
            stroke: #f44336;
        }
        
        .notificacion-exito .notificacion-icono svg {
            stroke: #4caf50;
        }
        
        .notificacion-info .notificacion-icono svg {
            stroke: #2196f3;
        }
        
        .notificacion-error .notificacion-boton {
            background-color: #f44336;
        }
        
        .notificacion-error .notificacion-boton:hover {
            background-color: #d32f2f;
        }
        
        .notificacion-exito .notificacion-boton {
            background-color: #4caf50;
        }
        
        .notificacion-exito .notificacion-boton:hover {
            background-color: #388e3c;
        }
        
        .notificacion-info .notificacion-boton {
            background-color: #2196f3;
        }
        
        .notificacion-info .notificacion-boton:hover {
            background-color: #1976d2;
        }
        
        .notificacion-botones {
            display: flex;
            gap: 10px;
            justify-content: center;
            width: 100%;
        }
        
        .campo-invalido {
            border: 2px solid #ff4444 !important;
            background-color: #fff8f8 !important;
        }
    `;
    document.head.appendChild(estilos);

    // Cargar datos de la planeación si existe ID
    const urlParams = new URLSearchParams(window.location.search);
    const planeacionId = urlParams.get('id');
    
    if (planeacionId) {
        cargarDatosPlaneacion(planeacionId);
    }

    // Definir validaciones y mensajes para los campos
    const validaciones = {
        'nombre-planeacion': {
            minLength: 15,
            message: 'Ingresa un nombre de planeación válido'
        },
        'tipo-planeacion': {
            required: true,
            message: 'Selecciona un tipo de planeación'
        },
        'importancia': {
            minLength: 15,
            message: 'Ingresa una importancia válida'
        },
        'descripcion': {
            minLength: 15,
            message: 'Ingresa una descripción válida'
        },
        'objetivo-general': {
            minLength: 15,
            message: 'Ingresa un objetivo general válido'
        }
    };

    // Agregar eventos de validación para cada campo
    Object.keys(validaciones).forEach(campoId => {
        const campo = document.getElementById(campoId);
        if (campo) {
            campo.addEventListener('blur', function() {
                validarCampo(this);
            });
        }
    });

    function validarCampo(campo) {
        // Obtener configuración de validación
        const config = validaciones[campo.id];
        if (!config) return true;

        // Limpiar clases de error previas
        campo.classList.remove('campo-error');
        campo.classList.remove('campo-invalido');

        const valor = campo.value.trim();
        
        // Validar campo requerido
        if (config.required && !valor) {
            campo.classList.add('campo-invalido');
            return false;
        }
        
        // Validar longitud mínima
        if (config.minLength && valor.length < config.minLength) {
            campo.classList.add('campo-invalido');
            return false;
        }
        
        return true;
    }

    // Validar formulario completo
    function validarFormulario() {
        let esValido = true;
        let primerCampoInvalido = null;
        
        // Validar cada campo con las reglas definidas
        Object.keys(validaciones).forEach(campoId => {
            const campo = document.getElementById(campoId);
            if (campo && !validarCampo(campo)) {
                esValido = false;
                if (!primerCampoInvalido) {
                    primerCampoInvalido = campo;
                }
            }
        });
        
        // Si hay un campo inválido, mostrar solo el mensaje del primer campo inválido
        if (primerCampoInvalido) {
            const config = validaciones[primerCampoInvalido.id];
            mostrarMensaje(config.message, 'error');
        }
        
        return esValido;
    }

    // Cargar datos de planeación existente
    function cargarDatosPlaneacion(id) {
        fetch(`../php/consulta-coordinador-obtener-planeacion.php?id=${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data); // Para debugging
                
                if (data.error) {
                    mostrarMensaje(data.error, 'error');
                    return;
                }
                
                // Asignar valores usando los nombres correctos de los campos
                document.getElementById('nombre-planeacion').value = data['nombre-planeacion'] || '';
                document.getElementById('tipo-planeacion').value = data['tipo-planeacion'] || '';
                document.getElementById('importancia').value = data.importancia || '';
                document.getElementById('descripcion').value = data.descripcion || '';
                document.getElementById('objetivo-general').value = data['objetivo-general'] || '';
                
                // Si hay un cronograma_id, guardarlo
                if (data.cronograma_id) {
                    document.getElementById('cronograma-id').value = data.cronograma_id;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al cargar los datos de la planeación', 'error');
            });
    }

    // Función para mostrar notificaciones centradas
    function mostrarMensaje(mensaje, tipo) {
        // Si ya hay una notificación activa, no mostrar otra
        if (notificacionActiva) {
            return;
        }
        
        notificacionActiva = true;
        
        // Limpiar el contenedor por si acaso
        notificacionContainer.innerHTML = '';
        
        const notificacion = document.createElement('div');
        notificacion.className = `notificacion notificacion-${tipo}`;
        
        // Iconos para diferentes tipos de notificaciones
        let icono = '';
        let titulo = '';
        
        switch(tipo) {
            case 'error':
                icono = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
                titulo = 'Error';
                break;
            case 'exito':
                icono = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
                titulo = 'Operación exitosa';
                break;
            case 'info':
                icono = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
                titulo = 'Información';
                break;
        }
        
        notificacion.innerHTML = `
            <div class="notificacion-icono">${icono}</div>
            <div class="notificacion-titulo">${titulo}</div>
            <div class="notificacion-mensaje">${mensaje}</div>
            <button class="notificacion-boton">Aceptar</button>
        `;
        
        notificacionContainer.appendChild(notificacion);
        
        // Mostrar el overlay y la notificación
        overlay.style.display = 'flex';
        setTimeout(() => {
            notificacionContainer.classList.add('mostrar');
        }, 10);
        
        // Agregar evento al botón para cerrar la notificación
        const botonCerrar = notificacion.querySelector('.notificacion-boton');
        botonCerrar.addEventListener('click', cerrarNotificacion);
        
        // También cerrar al hacer clic en el overlay
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                cerrarNotificacion();
            }
        });
        
        // Función para cerrar la notificación
        function cerrarNotificacion() {
            notificacionContainer.classList.remove('mostrar');
            
            setTimeout(() => {
                overlay.style.display = 'none';
                notificacionContainer.innerHTML = '';
                notificacionActiva = false;
                
                // Redirigir si es una notificación de éxito de guardado o envío
                if (tipo === 'exito' && (mensaje.includes('guardados exitosamente') || mensaje.includes('enviada exitosamente'))) {
                    window.location.href = 'coordinador-estatus-planeacion-anual.php';
                }
            }, 300);
        }
    }

    // Función para mostrar ventana modal de confirmación con dos botones
    function mostrarConfirmacion(tipo, titulo, mensaje, textoBotonAceptar, textoBotonCancelar, accionConfirmar) {
        // Si ya hay una notificación activa, no mostrar otra
        if (notificacionActiva) {
            return;
        }
        
        notificacionActiva = true;
        
        // Limpiar el contenedor
        notificacionContainer.innerHTML = '';
        
        const notificacion = document.createElement('div');
        notificacion.className = `notificacion notificacion-${tipo}`;
        
        // Iconos para diferentes tipos de notificaciones
        let icono = '';
        switch(tipo) {
            case 'info':
                icono = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
                break;
            case 'advertencia':
                icono = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
                break;
        }
        
        notificacion.innerHTML = `
            <div class="notificacion-icono">${icono}</div>
            <div class="notificacion-titulo">${titulo}</div>
            <div class="notificacion-mensaje">${mensaje}</div>
            <div class="notificacion-botones">
                <button class="notificacion-boton boton-aceptar">${textoBotonAceptar}</button>
                <button class="notificacion-boton boton-cancelar" style="background-color: #6c757d">${textoBotonCancelar}</button>
            </div>
        `;
        
        notificacionContainer.appendChild(notificacion);
        
        // Mostrar el overlay y la notificación
        overlay.style.display = 'flex';
        setTimeout(() => {
            notificacionContainer.classList.add('mostrar');
        }, 10);
        
        // Agregar eventos a los botones
        const botonAceptar = notificacion.querySelector('.boton-aceptar');
        const botonCancelar = notificacion.querySelector('.boton-cancelar');
        
        botonAceptar.addEventListener('click', () => {
            cerrarNotificacion(true);
        });
        
        botonCancelar.addEventListener('click', () => {
            cerrarNotificacion(false);
        });
        
        // También cerrar al hacer clic en el overlay
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                cerrarNotificacion(false);
            }
        });
        
        // Función para cerrar la notificación
        function cerrarNotificacion(confirmado) {
            notificacionContainer.classList.remove('mostrar');
            
            setTimeout(() => {
                overlay.style.display = 'none';
                notificacionContainer.innerHTML = '';
                notificacionActiva = false;
                
                // Ejecutar acción si se confirmó
                if (confirmado && typeof accionConfirmar === 'function') {
                    accionConfirmar();
                }
            }, 300);
        }
    }

    // Guardar cambios
    if (botonGuardar) {
        botonGuardar.addEventListener('click', function(e) {
            e.preventDefault();
            guardarPlaneacion();
        });
    }

    // Función guardar planeación
    function guardarPlaneacion() {
        if (!validarFormulario()) {
            return; // La validación ya muestra el mensaje de error
        }

        const formData = new FormData(formularioPlaneacion);
        formData.append('planeacion_id', planeacionId);

        fetch('../php/consulta-coordinador-actualizar-planeacion.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Capturar incluso respuestas de error para mostrar detalles
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Respuesta del servidor (error):', text);
                    throw new Error(`Error del servidor (${response.status}): ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarMensaje('Cambios guardados exitosamente', 'exito');
                // La redirección se maneja en la función cerrarNotificacion
            } else {
                const mensajeError = data.error || 'Error al guardar cambios';
                console.error('Detalle del error:', data);
                mostrarMensaje(mensajeError, 'error');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            mostrarMensaje(`Error detallado: ${error.message}`, 'error');
        });
    }

    // Función cancelar edición
    if (botonCancelar) {
        botonCancelar.addEventListener('click', function(e) {
            e.preventDefault();
            // Mostrar ventana modal de confirmación para cancelar
            mostrarConfirmacion(
                'info', 
                '¿Está seguro de cancelar?', 
                'Se perderán los cambios no guardados.',
                'Sí, cancelar',
                'No, continuar editando',
                () => { window.location.href = 'coordinador-estatus-planeacion-anual.php'; }
            );
        });
    }

    // Función enviar planeación
    if (botonEnviar) {
        botonEnviar.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!validarFormulario()) {
                return; // La validación ya muestra el mensaje de error
            }
            
            // Mostrar confirmación antes de enviar
            mostrarConfirmacion(
                'advertencia',
                '¿Está seguro de enviar la planeación?',
                'Una vez enviada no podrá modificarla.',
                'Sí, enviar',
                'No, seguir editando',
                enviarPlaneacionConfirmada
            );
        });
    }

    // Función para enviar planeación después de confirmación
    function enviarPlaneacionConfirmada() {
        const formData = new FormData(formularioPlaneacion);
        formData.append('planeacion_id', planeacionId);
        formData.append('accion', 'enviar');

        fetch('../php/consulta-coordinador-actualizar-planeacion.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Capturar incluso respuestas de error para mostrar detalles
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Respuesta del servidor (error):', text);
                    throw new Error(`Error del servidor (${response.status}): ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarMensaje('Planeación enviada exitosamente', 'exito');
                // La redirección se maneja en la función cerrarNotificacion
            } else {
                const mensajeError = data.error || 'Error al enviar la planeación';
                console.error('Detalle del error:', data);
                mostrarMensaje(`Error al enviar la planeación: ${mensajeError}`, 'error');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            mostrarMensaje(`Error detallado: ${error.message}`, 'error');
        });
    }
});
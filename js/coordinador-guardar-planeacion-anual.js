document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.querySelector('.formulario-planeacion');
    const botonGuardar = document.querySelector('.boton-guardar');
    const botonCancelar = document.querySelector('.boton-cancelar');
    const contenedorPrincipal = document.querySelector('.contenido-principal');
    
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
    `;
    document.head.appendChild(estilos);

    // Verificación inicial al cargar la página
    verificarPlaneacionExistente();

    // Definir validaciones y mensajes para los campos
    const validaciones = {
        'nombre-planeacion': {
            minLength: 15,
            message: 'Ingresa un nombre de planeación válido'
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
        
        // Validar longitud mínima
        if (valor.length < config.minLength) {
            campo.classList.add('campo-invalido');
            mostrarMensaje(config.message, 'error');
            return false;
        }
        
        return true;
    }

    function validarFormularioCompleto() {
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

    function verificarPlaneacionExistente() {
        fetch('consulta-coordinador-planeacion-anual.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'accion=verificar_existente'
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                mostrarMensajePlaneacionExistente();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al verificar planeación existente', 'error');
        });
    }

    // Manejador del botón guardar
    if (botonGuardar) {
        botonGuardar.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!formulario.checkValidity()) {
                formulario.reportValidity();
                return;
            }

            // Aplicar validaciones personalizadas
            if (!validarFormularioCompleto()) {
                return;
            }

            const formData = new FormData(formulario);
            formData.append('accion', 'guardar_planeacion'); // Añadir la acción al FormData

            fetch('consulta-coordinador-planeacion-anual.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje('Planeación guardada con éxito', 'exito');
                    // No necesitamos el setTimeout aquí, ya que la redirección
                    // se maneja en la función cerrarNotificacion
                } else {
                    mostrarMensaje(data.error || 'Error al guardar la planeación', 'error');
                    if (data.error === 'Ya existe una planeación vigente') {
                        // Usar setTimeout para dar tiempo a que se cierre la notificación actual
                        setTimeout(() => {
                            mostrarMensajePlaneacionExistente();
                        }, 1000);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Ocurrió un error al guardar la planeación', 'error');
            });
        });
    }

    function mostrarMensajePlaneacionExistente() {
        if (formulario) {
            formulario.style.display = 'none';
        }
        const botonesContainer = document.querySelector('.contenedor-botones-principales');
        if (botonesContainer) {
            botonesContainer.style.display = 'none';
        }

        const mensajeDiv = document.createElement('div');
        mensajeDiv.style.cssText = `
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        `;

        mensajeDiv.innerHTML = `
            <img src="../img/icono-advertencia.png" alt="Información" style="width: 64px; margin-bottom: 15px;">
            <h3 style="color: #003366; margin-bottom: 15px;">Planeación ya registrada</h3>
            <p style="color: #666; margin-bottom: 20px;">Ya has registrado una planeación para este período. 
               Puedes consultarla en la sección de estatus de planeación.</p>
            <div style="margin-top: 20px;">
                <button onclick="window.location.href='coordinador-estatus-planeacion-anual.php'" 
                        style="background-color: #003366; color: white; border: none; 
                               padding: 10px 20px; border-radius: 4px; cursor: pointer; 
                               margin-right: 10px;">
                    Ver mi planeación
                </button>
                <button onclick="window.location.href='../html/coordinador.html'" 
                        style="background-color: #6c757d; color: white; border: none; 
                               padding: 10px 20px; border-radius: 4px; cursor: pointer;">
                    Volver al inicio
                </button>
            </div>
        `;

        contenedorPrincipal.insertBefore(mensajeDiv, contenedorPrincipal.firstChild);
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
                
                // Redirigir si es una notificación de éxito de guardado
                if (tipo === 'exito' && mensaje.includes('guardada con éxito')) {
                    window.location.href = 'coordinador-estatus-planeacion-anual.php';
                }
            }, 300);
        }
    }

    if (botonCancelar) {
        botonCancelar.addEventListener('click', function() {
            // Mostrar ventana modal de confirmación para cancelar
            mostrarConfirmacion(
                'info', 
                '¿Está seguro de cancelar?', 
                'Se perderán los datos no guardados.',
                'Sí, cancelar',
                'No, continuar editando',
                () => { window.location.href = '../html/coordinador.html'; }
            );
        });
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
        
        // Agregar estilo para los botones
        const estilosBotones = document.createElement('style');
        estilosBotones.innerHTML = `
            .notificacion-botones {
                display: flex;
                gap: 10px;
                justify-content: center;
                width: 100%;
            }
        `;
        document.head.appendChild(estilosBotones);
        
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

    // Agregar estilo para campos inválidos
    const style = document.createElement('style');
    style.textContent = `
        .campo-invalido {
            border: 2px solid #ff4444 !important;
            background-color: #fff8f8 !important;
        }
    `;
    document.head.appendChild(style);
});
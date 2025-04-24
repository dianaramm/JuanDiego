document.addEventListener('DOMContentLoaded', function() {
    const nombre = document.getElementById('nombre');
    const apellidoPaterno = document.getElementById('apellido-paterno');
    const apellidoMaterno = document.getElementById('apellido-materno');
    const telefono = document.getElementById('telefono');
    const botonGenerarUsuario = document.getElementById('boton-generar-usuario');
    const botonGenerarContrasena = document.getElementById('boton-generar-contraseña');
    const inputUsuario = document.getElementById('usuario');
    const inputContrasena = document.getElementById('contraseña');
    const inputNombre = document.getElementById('nombre');
    const inputApellidoPaterno = document.getElementById('apellido-paterno');
    const inputApellidoMaterno = document.getElementById('apellido-materno');
    const selectTipoUsuario = document.getElementById('tipo-usuario');
    const filtroArea = document.getElementById('filtro-area');
    const tablaCredenciales = document.querySelector('.tabla tbody');

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

    // Función para mostrar notificaciones centradas
    function mostrarNotificacion(tipo, titulo, mensaje) {
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
        switch(tipo) {
            case 'error':
                icono = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
                break;
            case 'exito':
                icono = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
                break;
            case 'info':
                icono = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
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
            }, 300);
        }
    }

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

    // Establecer longitud máxima
    if(nombre) {
        nombre.maxLength = 20;
    }
    if(apellidoPaterno) {
        apellidoPaterno.maxLength = 20;
    }
    if(apellidoMaterno) {
        apellidoMaterno.maxLength = 20;
    }
    if(telefono) {
        telefono.maxLength = 10;
    }

    // Agregar eventos de validación para letras
    if(nombre) {
        nombre.addEventListener('keypress', soloLetras);
    }
    if(apellidoPaterno) {
        apellidoPaterno.addEventListener('keypress', soloLetras);
    }
    if(apellidoMaterno) {
        apellidoMaterno.addEventListener('keypress', soloLetras);
    }

    // Agregar evento de validación para números
    if(telefono) {
        telefono.addEventListener('keypress', soloNumeros);
    }

    // Validar formulario antes de enviar
    const formulario = document.querySelector('.formulario');
    if(formulario) {
        formulario.addEventListener('submit', function(e) {
            let isValid = true;
            let mensajesError = [];

            // Validar longitud del teléfono
            if (telefono && telefono.value.length !== 10) {
                mensajesError.push('El teléfono debe tener exactamente 10 dígitos');
                isValid = false;
            }

            // Validar longitud mínima de nombres
            if (nombre && nombre.value.length < 3) {
                mensajesError.push('El nombre debe tener al menos 2 caracteres');
                isValid = false;
            }

            if (apellidoPaterno && apellidoPaterno.value.length < 3) {
                mensajesError.push('El apellido paterno debe tener al menos 2 caracteres');
                isValid = false;
            }

            if (apellidoMaterno && apellidoMaterno.value.length < 3) {
                mensajesError.push('El apellido materno debe tener al menos 2 caracteres');
                isValid = false;
            }
            
            // Validar que se haya generado una contraseña
            if (inputContrasena && (!inputContrasena.value || inputContrasena.value.trim() === '')) {
                mensajesError.push('Debe generar una contraseña antes de guardar');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                // Mostrar errores como notificaciones
                mostrarNotificacion('error', 'Error de validación', mensajesError.join('<br>'));
            }
        });
    }

    // Asociar eventos a botones
    if(botonGenerarUsuario) {
        botonGenerarUsuario.addEventListener('click', generarUsuario);
    }
    if(botonGenerarContrasena) {
        botonGenerarContrasena.addEventListener('click', generarContrasena);
    }

    /**
     * Generar el nombre de usuario basado en los datos proporcionados.
     */
    function generarUsuario() {
        // Obtener valores de los campos
        const nombre = inputNombre.value.trim().toUpperCase();
        const apellidoPaterno = inputApellidoPaterno.value.trim().toUpperCase();
        const tipoUsuario = selectTipoUsuario.value;

        // Validar que los campos necesarios estén completos
        if (!nombre || !apellidoPaterno || !tipoUsuario) {
            mostrarNotificacion('info', 'Información requerida', 'Por favor, complete los campos de nombre, apellido paterno y tipo de usuario antes de generar el usuario.');
            return;
        }

        // Determinar prefijo basado en el tipo de usuario
        let prefijo;
        switch (tipoUsuario) {
            case 'Administrador de planeacion': prefijo = 'AP'; break;
            case 'Administrador de finanzas': prefijo = 'AF'; break;
            case 'Coordinador': prefijo = 'CO'; break;
            default: prefijo = 'XX'; // En caso de error
        }

        // Generar el usuario único
        const primeraLetraNombre = nombre.charAt(0);
        const primeraLetraApellido = apellidoPaterno.charAt(0);
        const numeroAleatorio = Math.floor(Math.random() * 10);
        const letraAleatoria = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)];
        const anioActual = new Date().getFullYear().toString().slice(-2);

        const usuarioGenerado = `${prefijo}${primeraLetraNombre}${primeraLetraApellido}${numeroAleatorio}${letraAleatoria}${anioActual}`;

        // Asignar el valor generado al campo correspondiente
        inputUsuario.value = usuarioGenerado;
        
        // Mostrar notificación de éxito
        mostrarNotificacion('exito', 'Usuario generado', `Se ha generado correctamente el usuario: ${usuarioGenerado}`);
    }

    /**
     * Generar contraseña aleatoria segura.
     * Solo se permiten caracteres alfanuméricos y los especiales ".", "-" y "_"
     */
    function generarContrasena() {
        const longitud = 8;
        // Solo incluimos letras, números y los caracteres especiales permitidos: ".", "-", "_"
        const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789._-';
        let contrasena = '';

        // Generar contraseña
        for (let i = 0; i < longitud; i++) {
            contrasena += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
        }

        // Asignar la contraseña generada al campo correspondiente
        inputContrasena.value = contrasena;
        
        // Mostrar notificación de éxito
        mostrarNotificacion('exito', 'Contraseña generada', 'Se ha generado una contraseña segura correctamente');
    }

    //Filtrar credenciales 
    if(filtroArea) {
        filtroArea.addEventListener('change', function() {
            const areaSeleccionada = this.value;
            
            // Consulta de credenciales
            fetch(`../php/consulta-listar-credencial.php?area=${encodeURIComponent(areaSeleccionada)}`)
                .then(response => response.text())
                .then(data => {
                    tablaCredenciales.innerHTML = data;
                    mostrarNotificacion('info', 'Filtro aplicado', `Se han filtrado las credenciales por área: ${areaSeleccionada}`);
                    
                    // Volver a agregar event listeners para filas de la tabla después de actualizar
                    agregarEventosFila();
                })
                .catch(error => {
                    console.error('Error:', error);
                    tablaCredenciales.innerHTML = "<tr><td colspan='7'>Error al cargar las credenciales.</td></tr>";
                    mostrarNotificacion('error', 'Error de carga', 'No se pudieron cargar las credenciales. Por favor, intente nuevamente.');
                });
        });
    }

    // Verificar parámetros de URL para mostrar notificaciones
    function verificarNotificacionesURL() {
        // Obtener parámetros de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const msg = urlParams.get('msg');
        const user = urlParams.get('user');
        
        if (status && msg) {
            // Determinamos el tipo de notificación según el status
            let tipo = status === 'exito' ? 'exito' : 'error';
            let titulo = status === 'exito' ? 'Operación exitosa' : 'Error';
            let mensaje = '';
            
            // Seleccionar mensaje según el parámetro msg
            switch (msg) {
                case 'registrado':
                    mensaje = `El usuario ${user || ''} ha sido registrado correctamente.`;
                    break;
                case 'duplicado':
                    mensaje = 'El usuario ya existe en el sistema. Por favor, intente con otro ID.';
                    break;
                case 'login':
                    mensaje = 'Error al registrar las credenciales de acceso.';
                    break;
                case 'registro':
                    mensaje = 'Error al registrar el usuario en el sistema.';
                    break;
                default:
                    mensaje = 'Se ha completado la operación.';
            }
            
            // Mostrar la notificación
            mostrarNotificacion(tipo, titulo, mensaje);
        }
    }
    
    // Agregar la funcionalidad para seleccionar una fila haciendo clic en cualquier parte de ella
    function agregarEventosFila() {
        // Obtener todas las filas de la tabla
        const filas = document.querySelectorAll('#tabla-credenciales tr');
        
        filas.forEach(fila => {
            // Asignar evento a cada celda de la fila, excepto a la primera que contiene el radio button
            const celdas = fila.querySelectorAll('td:not(:first-child)');
            const radioButton = fila.querySelector('input[type="radio"]');
            
            if (radioButton && celdas.length > 0) {
                celdas.forEach(celda => {
                    celda.style.cursor = 'pointer'; // Cambiar el cursor para indicar que es clickeable
                    
                    celda.addEventListener('click', function() {
                        // Marcar el radio button
                        radioButton.checked = true;
                        
                        // Disparar el evento change en el radio button para activar los botones
                        const event = new Event('change');
                        radioButton.dispatchEvent(event);
                    });
                });
            }
        });
    }
    
    // Aplicar la mejora a las filas de la tabla al cargar la página
    agregarEventosFila();
    
    // Ejecutar la verificación cuando la página se carga
    verificarNotificacionesURL();

    // INICIO: Funcionalidad para modal de edición
    // Agregar validaciones al formulario de edición
    const editarBotonGenerarContrasena = document.getElementById('editar_boton_generar_contraseña');
    const editarInputContrasena = document.getElementById('editar_contraseña');
    const editarNombre = document.getElementById('editar_nombre');
    const editarApellidoPaterno = document.getElementById('editar_apellido_paterno');
    const editarApellidoMaterno = document.getElementById('editar_apellido_materno');
    const editarTelefono = document.getElementById('editar_telefono');
    const editarCorreo = document.getElementById('editar_correo');
    const botonActualizar = document.getElementById('boton-actualizar');
    
    // Aplicar validaciones a los campos del modal
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
    
    // Generar contraseña para el formulario de edición
    if (editarBotonGenerarContrasena) {
        editarBotonGenerarContrasena.addEventListener('click', function() {
            const longitud = 8;
            const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789._-';
            let contrasena = '';
            
            for (let i = 0; i < longitud; i++) {
                contrasena += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
            }
            
            editarInputContrasena.value = contrasena;
            mostrarNotificacion('exito', 'Contraseña generada', 'Se ha generado una contraseña segura correctamente');
        });
    }
    
    // Validar formulario de edición antes de enviar
    if (botonActualizar) {
        botonActualizar.addEventListener('click', function(e) {
            let isValid = true;
            let mensajesError = [];
            
            // Validar campo de teléfono
            if (editarTelefono && editarTelefono.value.length !== 10) {
                mensajesError.push('El teléfono debe tener exactamente 10 dígitos');
                isValid = false;
            }
            
            // Validar correo electrónico
            if (editarCorreo) {
                const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!regexCorreo.test(editarCorreo.value)) {
                    mensajesError.push('Por favor ingrese un correo electrónico válido');
                    isValid = false;
                }
            }
            
            // Validar longitud mínima de nombres
            if (editarNombre && editarNombre.value.length < 3) {
                mensajesError.push('El nombre debe tener al menos 3 caracteres');
                isValid = false;
            }
            
            if (editarApellidoPaterno && editarApellidoPaterno.value.length < 3) {
                mensajesError.push('El apellido paterno debe tener al menos 3 caracteres');
                isValid = false;
            }
            
            if (editarApellidoMaterno && editarApellidoMaterno.value.length < 3) {
                mensajesError.push('El apellido materno debe tener al menos 3 caracteres');
                isValid = false;
            }
            
            // Validar que el campo de contraseña no esté vacío
            if (editarInputContrasena && (!editarInputContrasena.value || editarInputContrasena.value.trim() === '')) {
                mensajesError.push('Debe ingresar o generar una contraseña antes de actualizar');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                mostrarNotificacion('error', 'Error de validación', mensajesError.join('<br>'));
                return;
            }
            
            // Si todo es válido, enviar el formulario
            enviarFormularioEdicion();
        });
    }
    
    // Función para enviar el formulario de edición mediante AJAX
    function enviarFormularioEdicion() {
        const formEditar = document.getElementById('formulario-editar');
        if (!formEditar) return;
        
        const formData = new FormData(formEditar);
        
        fetch('../php/consulta-editar-credencial.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion('exito', 'Usuario actualizado', 'Los datos del usuario han sido actualizados correctamente');
                
                // Cerrar el modal después de un breve retraso
                setTimeout(() => {
                    const modal = document.getElementById('modal-editar');
                    if (modal) {
                        modal.style.display = 'none';
                    }
                    
                    // Recargar la tabla de credenciales
                    if (filtroArea) {
                        filtroArea.dispatchEvent(new Event('change'));
                    } else {
                        window.location.reload();
                    }
                }, 1500);
            } else {
                mostrarNotificacion('error', 'Error', data.error || 'Ha ocurrido un error al actualizar los datos');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('error', 'Error de conexión', 'No se pudo conectar con el servidor. Por favor, intente nuevamente.');
        });
    }
});
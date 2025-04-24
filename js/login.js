document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.getElementById('formularioLogin');
    const usuarioInput = document.getElementById('correo');
    const claveInput = document.getElementById('clave');
    
    // Verificar si hay un error en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    
    if (error) {
        let mensajeError = '';
        
        switch(error) {
            case 'captcha_invalido':
                mensajeError = 'Por favor, verifica que no eres un robot completando el CAPTCHA.';
                break;
            case 'acceso_denegado':
                mensajeError = 'Usuario inactivo o contraseña incorrecta.';
                break;
            case 'usuario_no_encontrado':
                mensajeError = 'Usuario no encontrado en el sistema.';
                break;
            case 'campos_incompletos':
                mensajeError = 'Por favor, complete todos los campos requeridos.';
                break;
            default:
                mensajeError = 'Ha ocurrido un error. Inténtelo de nuevo.';
        }
        
        // Mensaje error
        const mensajeDiv = document.createElement('div');
        mensajeDiv.className = 'error-mensaje';
        mensajeDiv.style.color = '#ff0000';
        mensajeDiv.style.fontSize = '14px';
        mensajeDiv.style.textAlign = 'center';
        mensajeDiv.style.marginTop = '10px';
        mensajeDiv.textContent = mensajeError;
        
        const contenedorBoton = claveInput.parentElement.nextElementSibling;
        claveInput.parentElement.insertAdjacentElement('afterend', mensajeDiv);
    }
    
    // Validación en el formulario antes de enviar
    formulario.addEventListener('submit', function(event) {
        let esValido = true;
        
        // Usuario no valido 
        if (usuarioInput.value.length < 3 || usuarioInput.value.length > 50) {
            mostrarError(usuarioInput, 'Datos incorrectos, vuelve a intentarlo');
            esValido = false;
        } else {
            limpiarError(usuarioInput);
        }
        
        // Contraseña incorrecta
        if (claveInput.value.length < 5 || claveInput.value.length > 50) {
            mostrarError(claveInput, 'Ingresa una contraseña válida');
            esValido = false;
        } else {
            limpiarError(claveInput);
        }
        
        // Usuario incorrecto
        const patronPeligroso = /['";\\=\-\(\)]/;
        if (patronPeligroso.test(usuarioInput.value)) {
            mostrarError(usuarioInput, 'Datos incorrectos, vuelve a intentarlo');
            esValido = false;
        }
        
        if (!esValido) {
            event.preventDefault();
        }
    });
    
    // Función para mostrar mensaje de error
    function mostrarError(elemento, mensaje) {
        // Limpiar errores previos
        limpiarError(elemento);
        
        // Agregar borde rojo
        elemento.style.borderColor = '#ff0000';
        
        // Crear mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'campo-error';
        errorDiv.style.color = '#ff0000';
        errorDiv.style.fontSize = '14px';
        errorDiv.style.marginTop = '5px';
        errorDiv.textContent = mensaje;
        
        // Insertar mensaje después del elemento
        elemento.parentElement.appendChild(errorDiv);
    }
    
    // Función para limpiar mensaje de error
    function limpiarError(elemento) {
        // Restaurar borde
        elemento.style.borderColor = '';
        
        // Eliminar mensaje de error si existe
        const errorPrevio = elemento.parentElement.querySelector('.campo-error');
        if (errorPrevio) {
            errorPrevio.remove();
        }
    }
    
    // Validación en tiempo real para evitar caracteres peligrosos
    usuarioInput.addEventListener('input', function() {
        const patronPeligroso = /['";\\=\-\(\)]/;
        if (patronPeligroso.test(this.value)) {
            mostrarError(this, 'Caracteres no permitidos');
        } else {
            limpiarError(this);
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // Primera verificación al cargar
    validarSesionActiva();
    
    // Verificar cada 5 minutos (300000 ms)
    setInterval(validarSesionActiva, 300000);
});

// Solo verificar cuando la ventana recupera el foco si han pasado al menos 5 minutos
let ultimaVerificacion = Date.now();
window.addEventListener('focus', function() {
    const ahora = Date.now();
    if (ahora - ultimaVerificacion >= 300000) {
        validarSesionActiva();
        ultimaVerificacion = ahora;
    }
});

function validarSesionActiva() {
    fetch('../php/verificar_sesion.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (!data.sesion_activa) {
                // Mostrar mensaje antes de redirigir
                alert('Su sesión ha expirado. Por favor, inicie sesión nuevamente.');
                window.location.href = '../index.html';
            }
        })
        .catch(error => {
            console.error('Error al verificar la sesión:', error);
            // Solo redirigir si hay un error de sesión específico
            if (error.message.includes('sesión')) {
                window.location.href = '../index.html';
            }
        });
}


document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.getElementById('formulario-reportes');
    const panelReporte = document.getElementById('panel-reporte');
    const contenidoReporte = document.getElementById('contenido-reporte');
    const tituloReporte = document.getElementById('titulo-reporte');
    const botonImprimir = document.getElementById('boton-imprimir');
    const botonDescargar = document.getElementById('boton-descargar');
    const botonCancelar = document.getElementById('boton-cancelar');
    
    // Configurar fechas por defecto
    const fechaActual = new Date();
    const primerDiaMes = new Date(fechaActual.getFullYear(), fechaActual.getMonth() - 1, 1);
    const ultimoDiaMes = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), 0);
    
    document.getElementById('fecha-inicio').valueAsDate = primerDiaMes;
    document.getElementById('fecha-fin').valueAsDate = ultimoDiaMes;
    
    // Función para mostrar notificación
    function mostrarNotificacion(tipo, titulo, mensaje) {
        // Crear elementos de notificación
        const overlay = document.createElement('div');
        overlay.className = 'notificacion-overlay';
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
        overlay.style.display = 'flex';
        overlay.style.justifyContent = 'center';
        overlay.style.alignItems = 'center';
        overlay.style.zIndex = '9999';
        
        const notificacion = document.createElement('div');
        notificacion.className = `notificacion notificacion-${tipo}`;
        notificacion.style.backgroundColor = '#fff';
        notificacion.style.borderRadius = '5px';
        notificacion.style.boxShadow = '0 0 10px rgba(0,0,0,0.3)';
        notificacion.style.padding = '20px';
        notificacion.style.maxWidth = '600px';
        notificacion.style.width = '90%';
        
        notificacion.innerHTML = `
            <div class="notificacion-titulo" style="font-size: 18px; font-weight: bold; margin-bottom: 10px; color: ${tipo === 'error' ? '#ff0000' : '#003366'}">${titulo}</div>
            <div class="notificacion-mensaje" style="margin-bottom: 15px;">${mensaje}</div>
            <button class="notificacion-boton" style="padding: 8px 16px; background-color: #003366; color: white; border: none; border-radius: 4px; cursor: pointer;">Aceptar</button>
        `;
        
        overlay.appendChild(notificacion);
        document.body.appendChild(overlay);
        
        // Cerrar notificación al hacer clic
        const botonCerrar = notificacion.querySelector('.notificacion-boton');
        botonCerrar.addEventListener('click', function() {
            document.body.removeChild(overlay);
        });
        
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                document.body.removeChild(overlay);
            }
        });
    }
    
    // Manejar envío del formulario
    formulario.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Mostrar carga
        const botonGenerar = document.getElementById('boton-generar');
        const textoOriginal = botonGenerar.textContent;
        botonGenerar.disabled = true;
        botonGenerar.innerHTML = '<span class="icono-carga"></span> Generando...';
        
        // Obtener datos del formulario
        const formData = new FormData(formulario);
        
        // Verificar si es solicitud de PDF
        const esPDF = formData.get('formato') === 'pdf';
        
        // Función para descargar directamente el PDF
        function descargarPDFDirectamente() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../php/consultas-finazas-reportes.php';
            form.target = '_blank'; // Abrir en nueva pestaña
            
            // Copiar todos los campos del formulario original
            for (let [key, value] of formData.entries()) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            // Re-habilitar botón
            botonGenerar.disabled = false;
            botonGenerar.textContent = textoOriginal;
        }
        
        // Si es PDF, manejar directamente para descargar
        if (esPDF) {
            descargarPDFDirectamente();
            return;
        }
        
        // Para visualización normal (no PDF)
        fetch('../php/consultas-finazas-reportes.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    try {
                        // Intentar parsear como JSON
                        const data = JSON.parse(text);
                        throw new Error(`Error HTTP ${response.status}: ${data.mensaje || 'Error desconocido'} | Detalles: ${data.detalles || 'No disponibles'}`);
                    } catch (e) {
                        // Si no es JSON, mostrar el texto crudo
                        throw new Error(`Error HTTP ${response.status}: ${text || 'Error desconocido'}`);
                    }
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                // Mostrar error con detalles adicionales si existen
                const mensajeError = data.mensaje || 'Error desconocido';
                const detallesError = data.detalles ? `\n\nDetalles técnicos:\n${data.detalles}` : '';
                mostrarNotificacion('error', 'Error en el servidor', `${mensajeError}${detallesError}`);
                return;
            }
            
            // Mostrar resultados
            if (data.html) {
                contenidoReporte.innerHTML = data.html;
                panelReporte.style.display = 'block';
                
                // Configurar botones de acción
                botonDescargar.onclick = function() {
                    // Cambiar a formato PDF y enviar de nuevo
                    const formDataPDF = new FormData(formulario);
                    formDataPDF.set('formato', 'pdf');
                    
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../php/consultas-finazas-reportes.php';
                    form.target = '_blank'; // Abrir en nueva pestaña
                    
                    // Copiar todos los campos
                    for (let [key, value] of formDataPDF.entries()) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value;
                        form.appendChild(input);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                };
                
                botonImprimir.onclick = function() {
                    window.print();
                };
                
                // Desplazarse al panel de resultados
                panelReporte.scrollIntoView({ behavior: 'smooth' });
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            
            // Determinar el mensaje de error más detallado posible
            let mensajeError = 'Error al procesar la solicitud';
            
            if (error.message.includes('Failed to fetch')) {
                mensajeError = 'Error de conexión: No se pudo contactar al servidor. Verifique su conexión a internet.';
            } else if (error.message.includes('Error HTTP')) {
                mensajeError = error.message;
            } else if (error instanceof SyntaxError) {
                mensajeError = 'Error al procesar la respuesta del servidor: La respuesta no es un JSON válido';
            } else {
                mensajeError = `Error: ${error.message || 'Error desconocido'}`;
            }
            
            mostrarNotificacion('error', 'Error en la conexión', mensajeError);
        })
        .finally(() => {
            botonGenerar.disabled = false;
            botonGenerar.textContent = textoOriginal;
        });
    });
    
    // Manejar botón cancelar
    botonCancelar.addEventListener('click', function() {
        formulario.reset();
        
        // Restablecer fechas por defecto
        document.getElementById('fecha-inicio').valueAsDate = primerDiaMes;
        document.getElementById('fecha-fin').valueAsDate = ultimoDiaMes;
        
        // Ocultar panel de reporte
        panelReporte.style.display = 'none';
    });
});
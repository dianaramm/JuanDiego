document.addEventListener('DOMContentLoaded', function() {
    // Referencia al botón de ver calendario y modal
    const botonVerCalendario = document.getElementById('boton-ver-calendario');
    const modalCalendario = document.getElementById('modal-calendario');
    const botonCerrarCalendario = document.getElementById('boton-cerrar-calendario');
    const botonImprimirCalendario = document.getElementById('boton-imprimir-calendario');
    let calendario;
    
    // Evento para abrir el modal del calendario
    botonVerCalendario.addEventListener('click', function() {
        modalCalendario.style.display = 'block';
        inicializarCalendario();
        // Llamar a la función para ajustar los botones
        setTimeout(ajustarPosicionBotones, 100);
    });
    
    // Evento para cerrar el modal
    botonCerrarCalendario.addEventListener('click', function() {
        modalCalendario.style.display = 'none';
    });
    
    // Evento para imprimir calendario
    if (botonImprimirCalendario) {
        botonImprimirCalendario.addEventListener('click', function() {
            // Redirigir a la página de generación de PDF
            window.open('../php/generar-calendario-pdf.php', '_blank');
        });
    }
    
    // Ajustar posición de los botones al abrir el modal
    function ajustarPosicionBotones() {
        // Mover los botones a la parte superior del modal
        const botonesContainer = document.querySelector('.modal-botones');
        const calendarioTitulo = document.querySelector('#modal-calendario .modal-titulo');
        
        if (botonesContainer && calendarioTitulo) {
            const parentNode = botonesContainer.parentNode;
            parentNode.insertBefore(botonesContainer, calendarioTitulo.nextSibling);
            
            // Aplicar estilos para los botones
            botonesContainer.style.marginBottom = '20px';
            botonesContainer.style.marginTop = '10px';
            botonesContainer.style.textAlign = 'right';
        }
    }
    
    // Cerrar modal al hacer clic fuera de él
    window.addEventListener('click', function(event) {
        if (event.target == modalCalendario) {
            modalCalendario.style.display = 'none';
        }
    });
    
    // Inicializar el calendario con FullCalendar
    function inicializarCalendario() {
        if (calendario) return; // Evitar inicialización múltiple
        
        const calendarEl = document.getElementById('calendario');
        
        calendario = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                list: 'Lista'
            },
            events: function(info, successCallback, failureCallback) {
                // Cargar actividades del usuario desde el servidor
                fetch('obtener-actividades.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.actividades) {
                            // Formatear actividades para FullCalendar
                            const eventos = data.actividades.map(actividad => {
                                return {
                                    id: actividad.id_actividad,
                                    title: actividad.nombre,
                                    description: actividad.descripcion,
                                    start: actividad.fecha,
                                    backgroundColor: '#003366', // Color principal
                                    borderColor: '#002244' // Color del borde
                                };
                            });
                            successCallback(eventos);
                        } else {
                            failureCallback(data.message || 'Error al cargar las actividades');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        failureCallback('Error de conexión');
                    });
            },
            eventClick: function(info) {
                // Mostrar detalles de la actividad al hacer clic
                alert('Actividad: ' + info.event.title + '\nDescripción: ' + info.event.extendedProps.description + '\nFecha: ' + info.event.start.toLocaleDateString());
            },
            dateClick: function(info) {
                // Al hacer clic en una fecha, mostrar opción para agregar actividad
                document.getElementById('fecha-actividad').value = info.dateStr;
                modalCalendario.style.display = 'none';
                document.getElementById('nombre-actividad').focus();
            }
        });
        
        calendario.render();
    }
});
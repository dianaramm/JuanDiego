document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const calendarioEl = document.getElementById('calendario-admin');
    const filtroArea = document.getElementById('filtro-area');
    const botonImprimir = document.getElementById('boton-imprimir-calendario');
    
    // Variable para almacenar la instancia del calendario
    let calendario;
    
    // Inicializar el calendario
    inicializarCalendario();
    
    // Evento para filtrar por área
    if (filtroArea) {
        filtroArea.addEventListener('change', function() {
            calendario.refetchEvents();
        });
    }
    
    // Evento para imprimir el calendario
    if (botonImprimir) {
        botonImprimir.addEventListener('click', function() {
            // Redirigir a la página de generación de PDF con todos los eventos
            window.open('../php/generar-calendario-admin-pdf.php', '_blank');
        });
    }
    
    // Función para inicializar el calendario con FullCalendar
    function inicializarCalendario() {
        calendario = new FullCalendar.Calendar(calendarioEl, {
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
                // Obtener el valor del filtro de área
                const areaFiltro = filtroArea ? filtroArea.value : '';
                
                // Cargar actividades desde el servidor
                fetch(`../php/consulta-admin-actividades.php${areaFiltro ? '?area=' + areaFiltro : ''}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.eventos) {
                            successCallback(data.eventos);
                        } else {
                            failureCallback(data.error || 'Error al cargar las actividades');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        failureCallback('Error de conexión');
                    });
            },
            eventClick: function(info) {
                // Mostrar detalles de la actividad al hacer clic
                const evento = info.event;
                const area = evento.extendedProps.area || 'No especificada';
                const coordinador = evento.extendedProps.coordinador || 'No especificado';
                
                alert(`Actividad: ${evento.title}\nDescripción: ${evento.extendedProps.description}\nFecha: ${evento.start.toLocaleDateString()}\nCoordinador: ${coordinador}\nÁrea: ${area}`);
            },
            eventDidMount: function(info) {
                // Agregar tooltip con información
                const evento = info.event;
                const area = evento.extendedProps.area || 'No especificada';
                const coordinador = evento.extendedProps.coordinador || 'No especificado';
                
                info.el.title = `${evento.title}\nCoordinador: ${coordinador}\nÁrea: ${area}\nFecha: ${evento.start.toLocaleDateString()}`;
            }
        });
        
        calendario.render();
    }
});
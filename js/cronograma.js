// Este evento se dispara cuando el documento HTML está completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Obtener todos los botones de vista y las vistas del cronograma
    const vistaBotones = document.querySelectorAll('.boton-vista');
    const vistas = document.querySelectorAll('.vista-cronograma');

    // Agregar eventos click a los botones para cambiar entre vistas
    vistaBotones.forEach(boton => {
        boton.addEventListener('click', function() {
            const vistaSeleccionada = this.getAttribute('data-vista');
            
            // Remover clase active de todos los botones
            vistaBotones.forEach(b => b.classList.remove('active'));
            // Agregar clase active al botón clickeado
            this.classList.add('active');

            // Mostrar la vista seleccionada y ocultar las demás
            vistas.forEach(vista => {
                if (vista.classList.contains(`vista-${vistaSeleccionada}`)) {
                    vista.classList.add('active');
                } else {
                    vista.classList.remove('active');
                }
            });
        });
    });

    // Generar el calendario y cargar los eventos
    generarCalendario();
    cargarEventos();
});

// Función para generar la estructura del calendario
function generarCalendario() {
    const hoy = new Date();
    const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    const ultimoDia = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
    
    const diasCalendario = document.querySelector('.dias-calendario');
    diasCalendario.innerHTML = '';

    // Agregar días vacíos al principio del mes
    for (let i = 0; i < primerDia.getDay(); i++) {
        const diaVacio = document.createElement('div');
        diaVacio.className = 'dia';
        diasCalendario.appendChild(diaVacio);
    }

    // Agregar los días del mes actual
    for (let i = 1; i <= ultimoDia.getDate(); i++) {
        const dia = document.createElement('div');
        dia.className = 'dia';
        dia.textContent = i;
        // Marcar el día actual
        if (i === hoy.getDate()) {
            dia.classList.add('hoy');
        }
        diasCalendario.appendChild(dia);
    }
}

// Función para cargar y mostrar los eventos en todas las vistas
function cargarEventos() {
    // Hacer la petición al servidor para obtener los eventos
    fetch('../php/consulta-cronograma.php')
        .then(response => response.json())
        .then(eventos => {
            // Obtener los contenedores de las diferentes vistas
            const diasCalendario = document.querySelector('.dias-calendario');
            const cuadriculaEventos = document.querySelector('.cuadricula-eventos');
            const vistaLista = document.querySelector('.vista-lista');
            
            // Crear la estructura de la tabla para la vista de lista
            vistaLista.innerHTML = `
                <table class="tabla-eventos">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Actividad</th>
                            <th>Área</th>
                            <th>Responsable</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            `;
            
            const tablaEventos = vistaLista.querySelector('.tabla-eventos tbody');

            // Procesar cada evento y mostrarlo en todas las vistas
            eventos.forEach(evento => {
                const fecha = new Date(evento.fecha);
                
                // 1. Agregar evento al calendario
                // Calcular la posición correcta del día en el calendario
                const diaIndex = fecha.getDate() + new Date(fecha.getFullYear(), fecha.getMonth(), 1).getDay() - 1;
                const dia = diasCalendario.children[diaIndex];
                if (dia) {
                    const eventoElement = document.createElement('div');
                    eventoElement.className = 'evento';
                    eventoElement.textContent = evento.actividad;
                    eventoElement.title = `${evento.nombre} - ${evento.actividad}`;
                    dia.appendChild(eventoElement);
                }

                // 2. Agregar evento a la vista de cuadrícula
                const eventoTarjeta = document.createElement('div');
                eventoTarjeta.className = 'evento-cuadricula';
                eventoTarjeta.innerHTML = `
                    <h3>${evento.actividad}</h3>
                    <p>${new Date(evento.fecha).toLocaleDateString()}</p>
                    <p>${evento.nombre}</p>
                    <p>Área: ${obtenerNombreArea(evento.area_id)}</p>
                `;
                cuadriculaEventos.appendChild(eventoTarjeta);

                // 3. Agregar evento a la vista de lista
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${new Date(evento.fecha).toLocaleDateString()}</td>
                    <td>${evento.actividad}</td>
                    <td>${obtenerNombreArea(evento.area_id)}</td>
                    <td>${evento.nombre}</td>
                `;
                tablaEventos.appendChild(fila);
            });
        })
        .catch(error => console.error('Error al cargar eventos:', error));
}

// Función auxiliar para obtener el nombre del área según su ID
function obtenerNombreArea(area_id) {
    const areas = {
        4: 'Academia de belleza',
        5: 'Academia de cuidado de la salud',
        6: 'Apoyo psicológico',
        7: 'Artículos de belleza y aseo personal',
        8: 'Banco de alimentos',
        9: 'Bazar',
        10: 'Clínica dental',
        11: 'Comedor comunitario',
        12: 'Consulta médica',
        13: 'Escuela de computación',
        14: 'Escuela de gastronomía',
        15: 'Estimulación temprana',
        16: 'Farmacia Similares',
        17: 'Guardería',
        18: 'Preescolar',
        19: 'Tortillería'
    };
    return areas[area_id] || 'Área no especificada';
}
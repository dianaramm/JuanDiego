// Variables globales
let actividadSeleccionada = null;
let actividadIdSeleccionado = null;

// Función para agregar actividad - Mantenemos el nombre original para compatibilidad
window.agregarActividad = function () {
    const nombre = document.getElementById('nombre-actividad').value.trim();
    const descripcion = document.getElementById('descripcion-actividad').value.trim();
    const fecha = document.getElementById('fecha-actividad').value;

    // Validar que no sean campos vacíos
    if (!nombre || !descripcion || !fecha) {
        alert('Por favor complete todos los campos correctamente');
        return;
    }

    // Validar que nombre y descripción no sean sólo números
    if (/^\d+$/.test(nombre)) {
        alert('El nombre de la actividad no puede contener solo números');
        return;
    }

    if (/^\d+$/.test(descripcion)) {
        alert('La descripción no puede contener solo números');
        return;
    }

    // Validar que la fecha no sea anterior a hoy
    const fechaSeleccionada = new Date(fecha);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0); // Resetear horas para comparar solo fechas
    
    if (fechaSeleccionada < hoy) {
        alert('La fecha no puede ser anterior a hoy');
        return;
    }

    // Validar que la fecha no supere el año actual
    const anioActual = new Date().getFullYear();
    if (fechaSeleccionada.getFullYear() > anioActual) {
        alert('La fecha no puede superar el año actual');
        return;
    }

    const formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('descripcion', descripcion);
    formData.append('fecha', fecha);

    const boton = document.querySelector('.boton-agregar');
    boton.disabled = true;
    boton.textContent = 'Guardando...';

    fetch('registrar-actividad.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Actividad registrada correctamente');
                document.getElementById('form-actividad').reset();
                cargarActividades();
                resetearSeleccion();
            } else {
                alert(`Error: ${data.message}`);
                console.error('Error del servidor:', data);
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            alert(`Error al registrar la actividad: ${error.message}`);
        })
        .finally(() => {
            boton.disabled = false;
            boton.textContent = 'AGREGAR';
        });
};

// Función para formatear fecha
function formatearFecha(fecha) {
    console.log('Formateando fecha:', fecha);
    try {
        const fechaObj = new Date(fecha);
        const dia = fechaObj.getDate().toString().padStart(2, '0');
        const mes = (fechaObj.getMonth() + 1).toString().padStart(2, '0'); // +1 porque los meses van de 0-11
        const año = fechaObj.getFullYear();

        const fechaFormateada = `${dia}/${mes}/${año}`;
        console.log('Fecha formateada:', fechaFormateada);
        return fechaFormateada;
    } catch (error) {
        console.error('Error al formatear fecha:', error);
        return 'Fecha inválida';
    }
}

// Función para cargar actividades
function cargarActividades() {
    const tbody = document.getElementById('tabla-actividades');
    console.log('Iniciando carga de actividades...');

    tbody.innerHTML = '<tr><td colspan="4" class="texto-centro">Cargando actividades...</td></tr>';

    fetch('obtener-actividades.php')
        .then(response => {
            console.log('Estado de la respuesta:', response.status);
            if (!response.ok) {
                throw new Error(`Error en la respuesta del servidor: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            tbody.innerHTML = '';

            if (data.success && data.actividades && data.actividades.length > 0) {
                console.log(`Encontradas ${data.actividades.length} actividades`);
                data.actividades.forEach((actividad, index) => {
                    console.log(`Procesando actividad ${index + 1}:`, actividad);
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center">
                            <input type="radio" name="actividad" value="${actividad.id_actividad}" class="seleccion-actividad">
                        </td>
                        <td>${actividad.nombre || 'Sin nombre'}</td>
                        <td>${actividad.descripcion || 'Sin descripción'}</td>
                        <td>${formatearFecha(actividad.fecha) || 'Fecha inválida'}</td>
                    `;
                    tbody.appendChild(tr);
                });
                
                // Inicializar eventos de selección
                inicializarSeleccion();
                
            } else {
                console.log('No se encontraron actividades o hubo un error:', data);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="texto-centro">
                            ${data.success ? 'No hay actividades registradas' : 'Error: ' + data.message}
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error('Error detallado:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="texto-centro texto-error">
                        Error al cargar las actividades: ${error.message}
                    </td>
                </tr>
            `;
        });
}

// Inicializar selección de actividades
function inicializarSeleccion() {
    document.querySelectorAll('.seleccion-actividad').forEach(radio => {
        radio.addEventListener('change', function() {
            // Actualizar variables globales con el ID seleccionado
            actividadIdSeleccionado = this.value;
            
            // Habilitar botones
            document.getElementById('boton-editar').disabled = false;
            document.getElementById('boton-eliminar').disabled = false;
            
            // Resaltar fila seleccionada
            document.querySelectorAll('tr.selected').forEach(row => {
                row.classList.remove('selected');
            });
            this.closest('tr').classList.add('selected');
        });
    });
    
    // Hacer que las filas sean clickeables
    document.querySelectorAll('#tabla-actividades tr').forEach(fila => {
        fila.querySelectorAll('td:not(:first-child)').forEach(celda => {
            celda.style.cursor = 'pointer';
            celda.addEventListener('click', function() {
                const radio = fila.querySelector('.seleccion-actividad');
                if (radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            });
        });
    });
}

// Mantenemos las funciones originales para compatibilidad
window.editarActividad = function(id_actividad) {
    fetch(`obtener-actividad.php?id=${id_actividad}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const actividad = data.actividad;
                document.getElementById('editar_actividad_id').value = actividad.id_actividad;
                document.getElementById('editar_nombre').value = actividad.nombre;
                document.getElementById('editar_descripcion').value = actividad.descripcion;
                document.getElementById('editar_fecha').value = actividad.fecha;
                
                // Establecer fecha mínima
                const hoy = new Date().toISOString().split('T')[0];
                document.getElementById('editar_fecha').min = hoy;
                
                // Mostrar modal
                document.getElementById('modal-editar').style.display = 'block';
            } else {
                alert(data.message || 'Error al obtener los datos de la actividad');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos de la actividad: ' + error.message);
        });
};

// Mantenemos la función original para compatibilidad
window.eliminarActividad = function(id_actividad) {
    // Mostrar modal de confirmación
    document.getElementById('modal-mensaje').textContent = '¿Está seguro de eliminar esta actividad? Esta acción no se puede deshacer.';
    document.getElementById('modal-confirmacion').style.display = 'block';
    
    // Configurar evento para el botón confirmar
    document.getElementById('modal-confirmar').onclick = function() {
        fetch('eliminar-actividad.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id_actividad}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Actividad eliminada correctamente');
                cargarActividades();
                resetearSeleccion();
            } else {
                throw new Error(data.message || 'Error al eliminar la actividad');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar la actividad: ' + error.message);
        })
        .finally(() => {
            // Cerrar modal
            document.getElementById('modal-confirmacion').style.display = 'none';
        });
    };
};

// Función para actualizar actividad
function actualizarActividad() {
    const actividad_id = document.getElementById('editar_actividad_id').value;
    const nombre = document.getElementById('editar_nombre').value.trim();
    const descripcion = document.getElementById('editar_descripcion').value.trim();
    const fecha = document.getElementById('editar_fecha').value;
    
    // Validaciones
    if (!nombre || !descripcion || !fecha) {
        alert('Por favor complete todos los campos correctamente');
        return;
    }

    // Validar que nombre y descripción no sean sólo números
    if (/^\d+$/.test(nombre)) {
        alert('El nombre de la actividad no puede contener solo números');
        return;
    }

    if (/^\d+$/.test(descripcion)) {
        alert('La descripción no puede contener solo números');
        return;
    }

    // Validar fecha
    const fechaSeleccionada = new Date(fecha);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    
    if (fechaSeleccionada < hoy) {
        alert('La fecha no puede ser anterior a hoy');
        return;
    }
    
    // Validar que la fecha no supere el año actual
    const anioActual = new Date().getFullYear();
    if (fechaSeleccionada.getFullYear() > anioActual) {
        alert('La fecha no puede superar el año actual');
        return;
    }
    
    const formData = new FormData();
    formData.append('actividad_id', actividad_id);
    formData.append('nombre', nombre);
    formData.append('descripcion', descripcion);
    formData.append('fecha', fecha);
    
    fetch('actualizar-actividad.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Actividad actualizada correctamente');
            cerrarModalEditar();
            cargarActividades();
            resetearSeleccion();
        } else {
            throw new Error(data.message || 'Error al actualizar la actividad');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar la actividad: ' + error.message);
    });
}

// Función para resetear selección
function resetearSeleccion() {
    actividadIdSeleccionado = null;
    document.getElementById('boton-editar').disabled = true;
    document.getElementById('boton-eliminar').disabled = true;
}

// Función para cerrar modal de edición
function cerrarModalEditar() {
    document.getElementById('modal-editar').style.display = 'none';
    document.getElementById('formulario-editar').reset();
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function () {
    cargarActividades();

    const fechaInput = document.getElementById('fecha-actividad');
    const hoy = new Date().toISOString().split('T')[0];
    fechaInput.min = hoy;
    
    // Eventos para botones de acción
    document.getElementById('boton-editar').addEventListener('click', function() {
        if (actividadIdSeleccionado) {
            editarActividad(actividadIdSeleccionado);
        }
    });
    
    document.getElementById('boton-eliminar').addEventListener('click', function() {
        if (actividadIdSeleccionado) {
            eliminarActividad(actividadIdSeleccionado);
        }
    });
    
    // Configurar eventos para modal de edición
    document.getElementById('boton-actualizar').addEventListener('click', actualizarActividad);
    document.getElementById('boton-cancelar-editar').addEventListener('click', cerrarModalEditar);
    
    // Configurar eventos para modal de confirmación
    document.getElementById('modal-cancelar').addEventListener('click', function() {
        document.getElementById('modal-confirmacion').style.display = 'none';
    });
    
    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(event) {
        const modalEditar = document.getElementById('modal-editar');
        const modalConfirmacion = document.getElementById('modal-confirmacion');
        
        if (event.target == modalEditar) {
            modalEditar.style.display = 'none';
        }
        
        if (event.target == modalConfirmacion) {
            modalConfirmacion.style.display = 'none';
        }
    });
});
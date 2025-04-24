// Función para agregar actividad
window.agregarActividad = function () {
    const nombre = document.getElementById('nombre-actividad').value.trim();
    const descripcion = document.getElementById('descripcion-actividad').value.trim();
    const fecha = document.getElementById('fecha-actividad').value;

    if (!nombre || !descripcion || !fecha) {
        alert('Por favor complete todos los campos correctamente');
        return;
    }

    const fechaSeleccionada = new Date(fecha);
    const hoy = new Date();
    if (fechaSeleccionada < hoy) {
        alert('La fecha no puede ser anterior a hoy');
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
                        <td>${actividad.nombre || 'Sin nombre'}</td>
                        <td>${actividad.descripcion || 'Sin descripción'}</td>
                        <td>${formatearFecha(actividad.fecha) || 'Fecha inválida'}</td>
                        <td class="acciones" style="display: flex; gap: 10px; justify-content: center;">
                            <button type="button" style="background: none; border: none; padding: 5px 10px; cursor: pointer; display: inline-flex; align-items: center;" onclick="editarActividad(${actividad.id_actividad})">
                                <img src='../img/icono-editar.png' alt='Editar' style="width: 20px; height: 20px;">
                            </button>
                            <button type="button" style="background: none; border: none; padding: 5px 10px; cursor: pointer; display: inline-flex; align-items: center;" onclick="eliminarActividad(${actividad.id_actividad})">
                                <img src='../img/icono-eliminar.png' alt='Eliminar' style="width: 20px; height: 20px;">
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
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
// Función para editar actividad
function editarActividad(id_actividad) {
    if (!confirm('¿Desea editar esta actividad?')) return;
    window.location.href = `coordinador-cronograma-editar.php?id=${id_actividad}`;
}

// Función para eliminar actividad
function eliminarActividad(id_actividad) {
    if (!confirm('¿Está seguro de eliminar esta actividad? Esta acción no se puede deshacer.')) {
        return;
    }

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
            } else {
                throw new Error(data.message || 'Error al eliminar la actividad');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar la actividad: ' + error.message);
        });
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function () {
    cargarActividades();

    const fechaInput = document.getElementById('fecha-actividad');
    const hoy = new Date().toISOString().split('T')[0];
    fechaInput.min = hoy;
});
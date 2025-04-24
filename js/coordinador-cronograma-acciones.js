// Archivo nuevo para manejar las acciones de edición
document.addEventListener('DOMContentLoaded', function() {
    // Obtener el ID de la actividad de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    if (id) {
        cargarDatosActividad(id);
    }

    // Establecer fecha mínima en el campo de fecha
    const fechaInput = document.getElementById('fecha-actividad');
    const hoy = new Date().toISOString().split('T')[0];
    fechaInput.min = hoy;
});

function cargarDatosActividad(id) {
    fetch('../php/obtener-actividad.php?id=' + id)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener los datos');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const actividad = data.actividad;
                document.getElementById('nombre-actividad').value = actividad.nombre;
                document.getElementById('descripcion-actividad').value = actividad.descripcion;
                document.getElementById('fecha-actividad').value = actividad.fecha;
            } else {
                throw new Error(data.message || 'Error al obtener los datos de la actividad');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos de la actividad: ' + error.message);
        });
}

function guardarCronograma() {
    // Obtener los valores del formulario
    const nombre = document.getElementById('nombre-actividad').value.trim();
    const descripcion = document.getElementById('descripcion-actividad').value.trim();
    const fecha = document.getElementById('fecha-actividad').value;
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    // Validar campos
    if (!nombre || !descripcion || !fecha) {
        alert('Por favor complete todos los campos correctamente');
        return;
    }

    // Validar fecha
    const fechaSeleccionada = new Date(fecha);
    const hoy = new Date();
    if (fechaSeleccionada < hoy) {
        alert('La fecha no puede ser anterior a hoy');
        return;
    }

    const formData = new FormData();
    formData.append('actividad_id', id);
    formData.append('nombre', nombre);
    formData.append('descripcion', descripcion);
    formData.append('fecha', fecha);

    // Mostrar indicador de carga en el botón
    const boton = document.querySelector('.boton-guardar');
    boton.disabled = true;
    boton.textContent = 'Guardando...';

    fetch('../php/actualizar-actividad.php', {
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
            window.location.href = 'coordinador-cronograma.php';
        } else {
            throw new Error(data.message || 'Error al actualizar la actividad');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar la actividad: ' + error.message);
    })
    .finally(() => {
        boton.disabled = false;
        boton.textContent = 'GUARDAR';
    });
}
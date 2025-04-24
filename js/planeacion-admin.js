document.addEventListener('DOMContentLoaded', function() {
    const filtroArea = document.getElementById('filtro-area');
    const tablaPlaneacion = document.getElementById('tabla-planeacion');

    // Función de filtrado
    filtroArea.addEventListener('change', function() {
        const areaSeleccionada = this.value;
        cargarPlaneaciones(areaSeleccionada);
    });

    // Función para cargar planeaciones
    function cargarPlaneaciones(area = '') {
        fetch(`../php/consulta-listar-planeacion.php?area=${encodeURIComponent(area)}`)
            .then(response => response.text())
            .then(data => {
                tablaPlaneacion.innerHTML = data;
                // Asegurarnos que los botones se inicialicen después de cargar el contenido
                inicializarBotonesVer();
            })
            .catch(error => {
                console.error('Error:', error);
                tablaPlaneacion.innerHTML = "<tr><td colspan='5'>Error al cargar los datos de planeación.</td></tr>";
            });
    }

    // Función para manejar el clic en los botones ver
    function inicializarBotonesVer() {
        document.querySelectorAll('.boton-ver').forEach(boton => {
            boton.onclick = function(e) {
                e.preventDefault();
                const planeacionId = this.getAttribute('data-id');
                window.location.href = `../php/ver-planeacion-anual-admin.php?id=${planeacionId}`;
            };
        });
    }

    // Inicializar botones al cargar la página
    inicializarBotonesVer();

    // Cargar datos iniciales
    cargarPlaneaciones();
});
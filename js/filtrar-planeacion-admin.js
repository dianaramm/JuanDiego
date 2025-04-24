document.addEventListener('DOMContentLoaded', function() {
    const filtroArea = document.getElementById('filtro-area');
    const tablaPlaneacion = document.getElementById('tabla-planeacion');

    filtroArea.addEventListener('change', function() {
        const areaSeleccionada = this.value;
        
        fetch(`../php/consulta-listar-planeacion.php?area=${encodeURIComponent(areaSeleccionada)}`)
            .then(response => response.text())
            .then(data => {
                tablaPlaneacion.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                tablaPlaneacion.innerHTML = "<tr><td colspan='3'>Error al cargar los datos de planeación.</td></tr>";
            });
    });
});
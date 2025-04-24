document.addEventListener('DOMContentLoaded', function() {
    const filtroArea = document.getElementById('filtro-area');
    const tablaSolicitudes = document.querySelector('.tabla tbody');

    filtroArea.addEventListener('change', function() {
        const areaSeleccionada = this.value;
        
        // Realizar una solicitud AJAX para obtener los datos filtrados
        fetch(`consulta-listar-solicitud-plazas-admin.php?area=${encodeURIComponent(areaSeleccionada)}`)
            .then(response => response.text())
            .then(data => {
                tablaSolicitudes.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                tablaSolicitudes.innerHTML = "<tr><td colspan='3'>Error al cargar los datos de solicitudes de plazas.</td></tr>";
            });
    });
});
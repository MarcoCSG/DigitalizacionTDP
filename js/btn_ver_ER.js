document.getElementById('consultaBtn').addEventListener('click', function() {
    const areaElement = document.getElementById('area');
    const clasificacionElement = document.getElementById('clasificacion');
    
    // Verificar que los elementos existen
    if (!areaElement || !clasificacionElement) {
        alert('No se encontraron los campos necesarios para realizar la consulta.');
        return;
    }

    const area = areaElement.value;
    const clasificacion = clasificacionElement.value;

    // Validar que se hayan seleccionado todas las opciones necesarias
    if (!area || !clasificacion) {
        alert('Por favor seleccione todas las opciones necesarias.');
        return;
    }

    // Construir la URL con los parámetros seleccionados
    const anio = new Date().getFullYear(); // Obtener el año actual dinámicamente
    const url = `mostrarregistros.php?anio=${anio}&area=${encodeURIComponent(area)}&clasificacion=${encodeURIComponent(clasificacion)}`;

    // Redirigir a la página de resultados
    window.location.href = url;
});

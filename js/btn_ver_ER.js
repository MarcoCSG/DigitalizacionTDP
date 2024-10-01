document.getElementById('consultaBtn').addEventListener('click', function() {
    const area = document.getElementById('area').value;
    const clasificacion = document.getElementById('clasificacion').value;

    // Validar que se hayan seleccionado todas las opciones necesarias
    if (!area || !clasificacion) {
        alert('Por favor seleccione todas las opciones necesarias.');
        return;
    }

    // Construir la URL con los parámetros seleccionados
    const url = `1.2_manuales.php?area=${encodeURIComponent(area)}&clasificacion=${encodeURIComponent(clasificacion)}`;

    // Redirigir a la página de resultados
    window.location.href = url;
});

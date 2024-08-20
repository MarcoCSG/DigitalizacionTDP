// Código JavaScript para manejar los selectores y redirigir a otra página con los resultados...
document.getElementById('consultaBtn').addEventListener('click', function() {
    const area = document.getElementById('area').value;
    const periodo = document.getElementById('periodo').value;
    const clasificacion = document.getElementById('clasificacion').value;
    const documento = document.getElementById('entregaRecepcion').value;

    // Validar que se hayan seleccionado todas las opciones necesarias
    if (!area || !periodo || (!clasificacion && !documento)) {
        alert('Por favor seleccione todas las opciones necesarias.');
        return;
    }

    // Construir la URL con los parámetros seleccionados
    const url = `mostrarArchivos.php?subclasificacion=${encodeURIComponent(documento)}&clasificacion=${encodeURIComponent(clasificacion)}&area=${encodeURIComponent(area)}&periodo=${encodeURIComponent(periodo)}`;

    // Redirigir a la página de resultados
    window.location.href = url;
});

// Javascript para el formulario
const guardarButton = document.querySelector('.button-container button:first-child');
const anexarArchivoButton = document.querySelector('.button-container button:last-child');

guardarButton.addEventListener('click', () => {
    // Obtener los datos del formulario
    const no = document.getElementById('no').value;
    const denominacion = document.getElementById('denominacion').value;
    const fecha = document.getElementById('fecha').value;
    const informacionAl = document.getElementById('informacion-al').value;
    const responsable = document.getElementById('responsable').value;
    const observaciones = document.getElementById('observaciones').value;

    // Validar los datos del formulario
    if (!no || !denominacion || !fecha || !informacionAl || !responsable || !observaciones) {
        alert('Por favor, complete todos los campos del formulario.');
        return;
    }

    // Procesar los datos del formulario (enviar a un servidor, guardar en base de datos, etc.)
    console.log('Datos del formulario:', { no, denominacion, fecha, informacionAl, responsable, observaciones });

    // Mostrar un mensaje de éxito
    alert('¡Formulario guardado con éxito!');
});

anexarArchivoButton.addEventListener('click', () => {
    // Mostrar un cuadro de diálogo para seleccionar un archivo
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.click();

    // Procesar el archivo seleccionado (subir al servidor, etc.)
    fileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        console.log('Archivo seleccionado:', file);

        // Aquí puedes subir el archivo al servidor o procesarlo como necesites
    });
});
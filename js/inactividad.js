// Definir tiempo de inactividad permitido (en milisegundos)
const tiempoInactividadMaximo = 10 * 60 * 1000; // 10 minutos

let temporizador;

function resetearTemporizador() {
    clearTimeout(temporizador); // Limpiar temporizador anterior
    temporizador = setTimeout(cerrarSesion, tiempoInactividadMaximo); // Reiniciar temporizador
}

function cerrarSesion() {
    alert("Sesión expirada por inactividad. Serás redirigido.");
    window.location.href = "index.html?error=Sesión%20expirada"; // Redirigir al usuario a la página de inicio de sesión
}

// Detectar eventos de interacción del usuario
window.onload = resetearTemporizador;
window.onmousemove = resetearTemporizador;
window.onkeypress = resetearTemporizador;

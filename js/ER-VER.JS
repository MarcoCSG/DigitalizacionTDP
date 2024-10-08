document.addEventListener("DOMContentLoaded", function() {
    const areaSelect = document.getElementById("area");
    const clasificacionContainer = document.getElementById("clasificacionContainer");
    const clasificacionSelect = document.getElementById("clasificacion");
    const clasificacionSeleccionada = document.getElementById("clasificacionSeleccionada");
    const anioSelect = document.getElementById("anio"); // Agregar referencia al selector de año
    const clasificaciones = {
        presidencia_er: [
            // todas las areas
            { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "mostrarregistros.php?anio=2022" },
            { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivoTramite.php" },
            { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria" },
            // clasificaciones
            { valor: "2.1", texto: "2.1 Plan Municipal de Desarrollo" },
            { valor: "2.2", texto: "2.2 Programas Municipales" },
        ],

        tesoreria_er: [
            // todas las areas
            { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "mostrarregistros.php?anio=2022" },
            { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite" },
            { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria" },
            // clasificaciones
            { valor: "1.1", texto: "1.1 Organigrama Autorizado" },
            { valor: "2.3", texto: "2.3 Programa de Actividades Institucionales" },
            { valor: "2.4", texto: "2.4 Informes de Avance del Programa de Actividades Institucionales" },
            { valor: "2.5", texto: "2.5 Programas Generales de Inversión (PGI)" },
            { valor: "4.1", texto: "4.1 Ley de Ingresos y Proyecto Anual de la Ley de Ingresos 2022" },
            { valor: "4.2", texto: "4.2 Presupuestos de Egresos" },
            { valor: "4.3", texto: "4.3 Estados de Situación Financiera" },
            { valor: "4.4", texto: "4.4 Estados de Actividades" },
            { valor: "4.5", texto: "4.5 Estados de Variación en la Hacienda Pública" },
            { valor: "4.6", texto: "4.6 Estados de Cambios en la Situación Financiera" },
            { valor: "4.7", texto: "4.7 Estados de Flujos de Efectivo" },
            { valor: "4.8", texto: "4.8 Estados sobre Pasivos Contingentes" },
            { valor: "4.9", texto: "4.9 Notas a los Estados Financieros" },
            { valor: "4.10", texto: "4.10 Estados Analíticos del Activo" },
            { valor: "4.11", texto: "4.11 Estados Analíticos de la Deuda y Otros Pasivos" },
            { valor: "4.12", texto: "4.12 Estados Analíticos de Ingresos" },
            { valor: "4.13", texto: "4.13 Estados Analíticos del Ejercicio del Presupuesto de Egresos" },
            { valor: "4.14", texto: "4.14 Estados de Situación Financiera Detallados – LDF" },
            { valor: "4.15", texto: "4.15 Informes Analíticos de la Deuda Pública y Otros Pasivos – LDF" },
            { valor: "4.16", texto: "4.16 Informes Analíticos de Obligaciones Diferentes de Financiamientos – LDF" },
            { valor: "4.17", texto: "4.17 Balances Presupuestarios – LDF" },
            { valor: "4.18", texto: "4.18 Estados Analíticos de Ingresos Detallados – LDF" },
            { valor: "4.19", texto: "4.19 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF (Clasificación por Objeto  del Gasto)" },
            { valor: "4.20", texto: "4.20 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF (Clasificación Administrativa)" },
            { valor: "4.21", texto: "4.21 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF (Clasificación Funcional)" },
            { valor: "4.22", texto: "4.22 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF (Clasificación de Servicios Personales por Categoría)" },
            { valor: "4.23", texto: "4.23 Proyecciones de Ingresos – LDF" },
            { valor: "4.24", texto: "4.24 Proyecciones de Egresos – LDF" },
            { valor: "4.25", texto: "4.25 Resultados de Ingresos – LDF" },
            { valor: "4.26", texto: "4.26 Resultados de Egresos – LDF" },
            { valor: "4.27", texto: "4.27 Informe sobre Estudios Actuariales – LDF" },
            { valor: "4.28", texto: "4.28 Guía de Cumplimiento de la Ley de Disciplina Financiera de las Entidades Federativas y los Municipios" },
            { valor: "4.29", texto: "4.29 Auxiliar Contable de Cuentas Bancarias" },
            { valor: "4.30", texto: "4.30 Auxiliar Contable de Cuentas por Cobrar" },
            { valor: "4.31", texto: "4.31 Auxiliar Contable de Cuentas por Pagar" },
            { valor: "4.32", texto: "4.32 Acuses de la presentación de los Estados Financieros y de Obra Pública Mensuales" },
            { valor: "4.33", texto: "4.33 Acuses de la Presentación al H. Congreso del Estado, de los Informes Trimestrales de Deuda" },
            { valor: "4.34", texto: "4.34 Cuenta Pública" },
            { valor: "4.35", texto: "4.35 Arqueo de Caja" },
            { valor: "4.36", texto: "4.36 Conciliaciones Bancarias" },
            { valor: "4.37", texto: "4.37 Situación de Talonarios de Cheques" },
            { valor: "4.38", texto: "4.38 Inventario de Formas Valoradas" },
            { valor: "5.1", texto: "5.1 Existencias de Almacén" },
            { valor: "5.2", texto: "5.2 Inventario de Bienes Inmuebles, Infraestructura y Construcciones en Proceso" },
            { valor: "5.3", texto: "5.3 Inventario de Bienes Muebles" },
            { valor: "5.4", texto: "5.4 Inventario de Activos Intangibles" },
            { valor: "5.5", texto: "5.5 Relación de Bienes no Inventariados" },
            { valor: "5.6", texto: "5.6 Relación de Bienes en Préstamo o Comodato" },
            { valor: "5.15", texto: "5.15 Relación de Seguros Contratados" },
            { valor: "5.16", texto: "5.16 Relación de Combinaciones de Cajas Fuertes" },
            { valor: "5.20", texto: "5.20 Informe del Cumplimiento de Obligaciones Fiscales" },
            { valor: "5.21", texto: "5.21 Relación de Expedientes del Personal" },
            { valor: "5.22", texto: "5.22 Plantilla de Personal" },
            { valor: "5.23", texto: "5.23 Tabulador de Sueldos Autorizado" },
            { valor: "5.24", texto: "5.24 Catálogo de Puestos" },
        ],
        catastro_er: [
            // todas las areas
            { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "mostrarregistros.php?anio=2022" },
            { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite" },
            { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria" },
            // clasificaciones
            { valor: "5.7", texto: "5.7 Cédulas de Bienes Inmuebles" },
            { valor: "5.8", texto: "5.8 Inventario de Reservas Territoriales" },
            { valor: "5.9", texto: "5.9 Relación de Información Relativa a Catastro" },
        ],
        secretaria_er: [
            // todas las areas
            { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "mostrarregistros.php?anio=2022" },
            { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite" },
            { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria" },
            // clasificaciones
            { valor: "1.3", texto: "1.3 Relación de Entidades Paramunicipales" },
            { valor: "3.1", texto: "3.1 Relación de Reglamentos Municipales" },
            { valor: "3.2", texto: "3.2 Relación de Libros de Actas de Cabildo" },
            { valor: "3.3", texto: "3.3 Relación de Acuerdos de Cabildo Pendientes de Cumplir" },
            { valor: "3.4", texto: "3.4 Relación de Actas del Consejo de Planeación para el Desarrollo Municipal (COPLADEMUN)" },
            { valor: "3.5", texto: "3.5 Relación de Actas del Consejo de Desarrollo Municipal (CDM)" },
            { valor: "3.6", texto: "3.6 Relación de Juicios en Proceso Promovidos por el Ayuntamiento" },
            { valor: "3.7", texto: "3.7 Relación de Juicios en Proceso Promovidos en contra del Ayuntamiento" },
            { valor: "3.8", texto: "3.8 Relación de Contratos, Convenios o Acuerdos" },
            { valor: "5.10", texto: "5.10 Catálogo de Disposición Documental" },
            { valor: "5.12", texto: "5.12 Relación de Archivo de Concentración" },
            { valor: "5.13", texto: "5.13 Relación de Archivo Histórico" },
        ],
        contraloria_er: [
            // todas las areas
            { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "mostrarregistros.php?anio=2022" },
            { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite" },
            { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria" },
            // clasificaciones
            { valor: "8.1", texto: "8.1 Sistema de Evaluación y Fiscalización de Veracruz(SEFISVER)" },
            { valor: "8.2", texto: "8.2 Resumen de Observaciones y Recomendaciones en Proceso de Atención" },
            { valor: "8.3", texto: "8.3 Decretos que Aprueban el Informe del Resultado de la Fiscalización Superior de las Cuentas Públicas" },
        ],
        obraspublicas_er: [
            // todas las areas
            { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "mostrarregistros.php?anio=2022" },
            { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite" },
            { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria" },
            // clasificaciones
            { valor: "2.6", texto: "2.6 Reportes Trimestrales de Avances Físico-Financieros" },
            { valor: "2.7", texto: "2.7 Cierres de Ejercicios" },
            { valor: "6.1", texto: "6.1 Relación de Obras y Acciones Terminadas" },
            { valor: "6.2", texto: "6.2 Reporte de Obras y Acciones Pendientes de Terminar Física y/o Financieramente" },
            { valor: "6.3", texto: "6.3 Relación de Obras y Acciones con Contrato en Proceso de Rescisión y/o en Trámite de Recuperación de Fianzas" },
            { valor: "6.4", texto: "6.4 Inventario de Materiales para la Obra Pública y Mantenimiento" },
            { valor: "6.5", texto: "6.5 Relación de Expedientes Técnicos Unitarios de Obras Públicas" },
        ],
        regidores_er: [
            // todas las areas
            { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "mostrarregistros.php?anio=2022" },
            { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite" },
            { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria" },
            // clasificaciones
            { valor: "3.9", texto: "3.9 Informes de las Comisiones del Ayuntamiento" },
        ],
        areasUsuarias_er: [
            // todas las areas
            { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "mostrarregistros.php?anio=2022" },
            { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite" },
            { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria" },
            // clasificaciones
            { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos" },
            { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
            { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
        ],

        Utransparencia_er: [
            // todas las areas
            { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "mostrarregistros.php?anio=2022" },
            { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite" },
            { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria" },
            // clasificaciones
            { valor: "7.1", texto: "7.1 Relación de Solicitudes de Información y Solicitudes ARCO Pendientes de Atender" },
            { valor: "7.2", texto: "7.2 Relación de Recursos de Revisión en Trámite" },
            { valor: "7.3", texto: "7.3 Relación de Documentación Relativa a Transparencia, Acceso a la Información y Protección de Datos Personales" },
        ],

    };

    // Muestra clasificaciones según el área seleccionada
    areaSelect.addEventListener("change", function() {
        const selectedArea = areaSelect.value;
        clasificacionSelect.innerHTML = '<option value="">Seleccione una clasificación</option>';

        if (selectedArea && clasificaciones[selectedArea]) {
            clasificacionContainer.style.display = "block";
            clasificaciones[selectedArea].forEach(function(clasificacion) {
                const option = document.createElement("option");
                option.value = clasificacion.valor;  // Asigna el valor único (ID)
                option.textContent = clasificacion.texto;  // Muestra el texto
                if (clasificacion.form) {
                    option.setAttribute("data-form", clasificacion.form);  // Agrega la ruta del formulario como atributo
                } else {
                    option.setAttribute("data-form", "mostrarregistros.php"); // Valor por defecto si no se especifica
                }
                clasificacionSelect.appendChild(option);
            });
        } else {
            clasificacionContainer.style.display = "none";
        }
    });

    document.getElementById('consultaBtn').addEventListener('click', function() {
        const area = areaSelect.value;
        const clasificacion = clasificacionSelect.value;
        const selectedOption = clasificacionSelect.options[clasificacionSelect.selectedIndex];
        const formulario = selectedOption.getAttribute("data-form") || "mostrarregistros.php";  // Obtiene la ruta del formulario o usa por defecto

        const anio = new Date().getFullYear(); // Obtener el año actual

        if (!area || !clasificacion) {
            alert('Por favor seleccione el área y la clasificación.');
            return;
        }

        // Construir la URL con los parámetros seleccionados
        const url = `${formulario}&area=${encodeURIComponent(area)}&clasificacion=${encodeURIComponent(clasificacion)}`;
        window.location.href = url;
    });
});
document.addEventListener("DOMContentLoaded", function() {
    const areaSelect = document.getElementById("area");
    const clasificacionContainer = document.getElementById("clasificacionContainer");
    const clasificacionSelect = document.getElementById("clasificacion");

    const clasificaciones = {
        presidencia_er: ["2.1 Plan Municipal de Desarrollo 2018-2021", "2.2 Programas Municipales 2018-2021"],
        tesoreria_er: ["1.1 Organigrama Autorizado", 
                        "2.3 Programa de Actividades Institucionales 2021", 
                        "2.4 Informes de Avance del Programa de Actividades Institucionales 2021",
                        "2.5 Programas Generales de Inversión (PGI) 2018-2021",
                        "4.1 Ley de Ingresos 2021 y Proyecto Anual de la Ley de Ingresos 2022",
                        "4.2 Presupuestos de Egresos 2021 y 2022",
                        "4.3 Estados de Situación Financiera 2021",
                        "4.4 Estados de Actividades 2021",
                        "4.5 Estados de Variación en la Hacienda Pública 2021",
                        "4.6 Estados de Cambios en la Situación Financiera 2021",
                        "4.7 Estados de Flujos de Efectivo 2021",
                        "4.8 Estados sobre Pasivos Contingentes 2021",
                        "4.9 Notas a los Estados Financieros 2021",
                        "4.10 Estados Analíticos del Activo 2021",
                        "4.11 Estados Analíticos de la Deuda y Otros Pasivos 2021",
                        "4.12 Estados Analíticos de Ingresos 2021",
                        "4.13 Estados Analíticos del Ejercicio del Presupuesto de Egresos 2021",
                        "4.14 Estados de Situación Financiera Detallados – LDF 2021",
                        "4.15 Informes Analíticos de la Deuda Pública y Otros Pasivos – LDF 2021",
                        "4.16 Informes Analíticos de Obligaciones Diferentes de Financiamientos – LDF 2021",
                        "4.17 Balances Presupuestarios – LDF 2021",
                        "4.18 Estados Analíticos de Ingresos Detallados – LDF 2021",
                        "4.19 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF 2021 (Clasificación por Objeto  del Gasto)",
                        "4.20 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF 2021 (Clasificación Administrativa)",
                        "4.21 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF 2021 (Clasificación Funcional)",
                        "4.22 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF 2021 (Clasificación de Servicios Personales por Categoría)",
                        "4.23 Proyecciones de Ingresos – LDF 2021",
                        "4.24 Proyecciones de Egresos – LDF 2021",
                        "4.25 Resultados de Ingresos – LDF 2021",
                        "4.26 Resultados de Egresos – LDF 2021",
                        "4.27 Informe sobre Estudios Actuariales – LDF 2021",
                        "4.28 Guía de Cumplimiento de la Ley de Disciplina Financiera de las Entidades Federativas y los Municipios 2021",
                        "4.29 Auxiliar Contable de Cuentas Bancarias 2021",
                        "4.30 Auxiliar Contable de Cuentas por Cobrar 2021",
                        "4.31 Auxiliar Contable de Cuentas por Pagar 2021",
                        "4.32 Acuses de la presentación de los Estados Financieros y de Obra Pública Mensuales 2021",
                        "4.33 Acuses de la Presentación al H. Congreso del Estado, de los Informes Trimestrales de Deuda 2021",
                        "4.34 Cuenta Pública 2021",
                        "4.35 Arqueo de Caja",
                        "4.36 Conciliaciones Bancarias",
                        "4.37 Situación de Talonarios de Cheques",
                        "4.38 Inventario de Formas Valoradas",
                        "5.1 Existencias de Almacén",
                        "5.2 Inventario de Bienes Inmuebles, Infraestructura y Construcciones en Proceso",
                        "5.3 Inventario de Bienes Muebles",
                        "5.4 Inventario de Activos Intangibles",
                        "5.5 Relación de Bienes no Inventariados",
                        "5.6 Relación de Bienes en Préstamo o Comodato",
                        "5.15 Relación de Seguros Contratados",
                        "5.16 Relación de Combinaciones de Cajas Fuertes",
                        "5.20 Informe del Cumplimiento de Obligaciones Fiscales",
                        "5.21 Relación de Expedientes del Personal",
                        "5.22 Plantilla de Personal",
                        "5.23 Tabulador de Sueldos Autorizado",
                        "5.24 Catálogo de Puestos",
                    ],
        catastro_er: ["5.7 Cédulas de Bienes Inmuebles", 
                    "5.8 Inventario de Reservas Territoriales",
                    "5.9 Relación de Información Relativa a Catastro"
                ],
        secretaria_er: ["1.3 Relación de Entidades Paramunicipales", 
                    "3.1 Relación de Reglamentos Municipales", 
                    "3.2 Relación de Libros de Actas de Cabildo 2018-2021",
                    "3.3 Relación de Acuerdos de Cabildo Pendientes de Cumplir",
                    "3.4 Relación de Actas del Consejo de Planeación para el Desarrollo Municipal (COPLADEMUN) 2018-2021",
                    "3.5 Relación de Actas del Consejo de Desarrollo Municipal (CDM) 2018-2021",
                    "3.6 Relación de Juicios en Proceso Promovidos por el Ayuntamiento",
                    "3.7 Relación de Juicios en Proceso Promovidos en contra del Ayuntamiento",
                    "3.8 Relación de Contratos, Convenios o Acuerdos",
                    "5.10 Catálogo de Disposición Documental",
                    "5.12 Relación de Archivo de Concentración", 
                    "5.13 Relación de Archivo Histórico",
                ],
        contraloria_er: ["8.1 Sistema de Evaluación y Fiscalización de Veracruz(SEFISVER)", 
                        "8.2 Resumen de Observaciones y Recomendaciones en Proceso de Atención",
                        "8.3 Decretos que Aprueban el Informe del Resultado de la Fiscalización Superior de las Cuentas Públicas 2017, 2018, 2019 y 2020",
                        ],
        obraspublicas_er: ["2.6 Reportes Trimestrales de Avances Físico-Financieros 2021", 
                        "2.7 Cierres de Ejercicios 2018-2021", 
                        "6.1 Relación de Obras y Acciones Terminadas 2018-2021",
                        "6.2 Reporte de Obras y Acciones Pendientes de Terminar Física y/o Financieramente 2021",
                        "6.3 Relación de Obras y Acciones con Contrato en Proceso de Rescisión y/o en Trámite de Recuperación de Fianzas 20182021",
                        "6.4 Inventario de Materiales para la Obra Pública y Mantenimiento",
                        "6.5 Relación de Expedientes Técnicos Unitarios de Obras Públicas 2018-2021",
                        ],
        regidores_er: ["3.9 Informes de las Comisiones del Ayuntamiento 2018-2021", 
                        ],

        areas_er: ["1.2 Relación de Manuales Administrativos",
                        "5.11 Relación de Archivo de Trámite",
                        "9.1 Actividades de Atención Prioritaria",
                    ], 
                        
        areasUsuarias_er: ["5.14 Relación de Archivos Electrónicos",
                        "5.17 Relación de Claves de Acceso",
                        "5.19 Relación de Sellos Oficiales",
                        ],
                        
        Utransparencia_er: ["7.1 Relación de Solicitudes de Información y Solicitudes ARCO Pendientes de Atender",
                        "7.2 Relación de Recursos de Revisión en Trámite",
                        "7.3 Relación de Documentación Relativa a Transparencia, Acceso a la Información y Protección de Datos Personales",
                        ],

    };

    // Muestra clasificaciones según el área seleccionada
    areaSelect.addEventListener("change", function() {
        const selectedArea = areaSelect.value;
        clasificacionSelect.innerHTML = '<option value="">Seleccione una clasificación</option>';

        if (selectedArea) {
            clasificacionContainer.style.display = "block";
            clasificaciones[selectedArea].forEach(function(clasificacion) {
                const option = document.createElement("option");
                option.value = clasificacion;
                option.textContent = clasificacion;
                clasificacionSelect.appendChild(option);
            });
        } else {
            clasificacionContainer.style.display = "none";
        }
    });
});

document.getElementById('consultaBtn').addEventListener('click', function() {
    const area = document.getElementById('area').value;
    const clasificacion = document.getElementById('clasificacion').value;

    // Validar que se hayan seleccionado las opciones necesarias
    if (!area || !clasificacion) {
        alert('Por favor seleccione el área y la clasificación.');
        return;
    }

    // Construir la URL con los parámetros seleccionados
    const url = `mostrarArchivosER.php?area=${encodeURIComponent(area)}&clasificacion=${encodeURIComponent(clasificacion)}`;

    // Redirigir a la página de resultados
    window.location.href = url;
});


/// Función para obtener los parámetros de la URL
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// Mapeo entre los valores y los textos de clasificación
const clasificacionesMap = {
            "1.1": "1.1 Organigrama Autorizado",
            "1.2": "1.2 Relación de Manuales Administrativos",
            "1.3": "1.3 Relación de Entidades Paramunicipales",
            "2.1": "2.1 Plan Municipal de Desarrollo 2018-2021",
            "2.2": "2.2 Programas Municipales 2018-2021",
            "2.3": "2.3 Programa de Actividades Institucionales 2021",
            "2.4": "2.4 Informes de Avance del Programa de Actividades Institucionales 2021",
            "2.5": "2.5 Programas Generales de Inversión (PGI) 2018-2021 o Dirección de Obras Públicas",
            "2.6": "2.6 Reportes Trimestrales de Avances Físico-Financieros 2021 - Dirección de Obras Públicas",
            "2.7": "2.7 Cierres de Ejercicios 2018-2021 - Dirección de Obras Públicas",
            "3.1": "3.1 Relación de Reglamentos Municipales, Dirección Jurídica o equivalente.",
            "3.2": "3.2 Relación de Libros de Actas de Cabildo 2018-2021",
            "3.3": "3.3 Relación de Acuerdos de Cabildo Pendientes de Cumplir",
            "3.4": "3.4 Relación de Actas del Consejo de Planeación para el Desarrollo Municipal (COPLADEMUN) 2018 2021",
            "3.5": "3.5 Relación de Actas del Consejo de Desarrollo Municipal (CDM) 2018-2021",
            "3.6": "3.6 Relación de Juicios en Proceso Promovidos por el Ayuntamiento, Dirección Jurídica o equivalente.",
            "3.7": "3.7 Relación de Juicios en Proceso Promovidos en contra del Ayuntamiento, Dirección Jurídica o equivalente.",
            "3.8": "3.8 Relación de Contratos, Convenios o Acuerdos, Dirección  Jurídica o equivalente.",
            "3.9": "3.9 Informes de las Comisiones del Ayuntamiento 2018-2021 - Regidores",
            "4.1": "4.1 Ley de Ingresos 2021 y Proyecto Anual de la Ley de Ingresos 2022",
            "4.2": "4.2 Presupuestos de Egresos 2021 y 2022",
            "4.3": "4.3 Estados de Situación Financiera 2021",
            "4.4": "4.4 Estados de Actividades 2021",
            "4.5": "4.5 Estados de Variación en la Hacienda Pública 2021",
            "4.6": "4.6 Estados de Cambios en la Situación Financiera 2021",
            "4.7": "4.7 Estados de Flujos de Efectivo 2021",
            "4.8": "4.8 Estados sobre Pasivos Contingentes 2021",
            "4.9": "4.9 Notas a los Estados Financieros 2021",
            "4.10": "4.10 Estados Analíticos del Activo 2021",
            "4.11": "4.11 Estados Analíticos de la Deuda y Otros Pasivos 2021",
            "4.12": "4.12 Estados Analíticos de Ingresos 2021",
            "4.13": "4.13 Estados Analíticos del Ejercicio del Presupuesto de Egresos 2021",
            "4.14": "4.14 Estados de Situación Financiera Detallados – LDF 2021",
            "4.15": "4.15 Informes Analíticos de la Deuda Pública y Otros Pasivos – LDF 2021",
            "4.16": "4.16 Informes Analíticos de Obligaciones Diferentes de Financiamientos – LDF 2021",
            "4.17": "4.17 Balances Presupuestarios – LDF 2021",
            "4.18": "4.18 Estados Analíticos de Ingresos Detallados – LDF 2021",
            "4.19": "4.19 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF 2021 (Clasificación por Objeto del Gasto)",
            "4.20": "4.20 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF 2021 (Clasificación Administrativa)",
            "4.21": "4.21 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF 2021 (Clasificación Funcional)",
            "4.22": "4.22 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF 2021 (Clasificación de Servicios Personales por Categoría)",
            "4.23": "4.23 Proyecciones de Ingresos – LDF 2021",
            "4.24": "4.24 Proyecciones de Egresos – LDF 2021",
            "4.25": "4.25 Resultados de Ingresos – LDF 2021",
            "4.26": "4.26 Resultados de Egresos – LDF 2021",
            "4.27": "4.27 Informe sobre Estudios Actuariales – LDF 2021",
            "4.28": "4.28 Guía de Cumplimiento de la Ley de Disciplina Financiera de las Entidades Federativas y los Municipios 2021",
            "4.29": "4.29 Auxiliar Contable de Cuentas Bancarias 2021",
            "4.30": "4.30 Auxiliar Contable de Cuentas por Cobrar 2021",
            "4.31": "4.31 Auxiliar Contable de Cuentas por Pagar 2021",
            "4.32": "4.32 Acuses de la presentación de los Estados Financieros y de Obra Pública Mensuales 2021",
            "4.33": "4.33 Acuses de la Presentación al H. Congreso del Estado, de los Informes Trimestrales de Deuda 2021",
            "4.34": "4.34 Cuenta Pública 2021",
            "4.35": "4.35 Arqueo de Caja",
            "4.36": "4.36 Conciliaciones Bancarias",
            "4.37": "4.37 Situación de Talonarios de Cheques",
            "4.38": "4.38 Inventario de Formas Valoradas",
            "5.1": "5.1 Existencias de Almacén",
            "5.2": "5.2 Inventario de Bienes Inmuebles, Infraestructura y Construcciones en Proceso",
            "5.3": "5.3 Inventario de Bienes Muebles",
            "5.4": "5.4 Inventario de Activos Intangibles",
            "5.5": "5.5 Relación de Bienes no Inventariados",
            "5.6": "5.6 Relación de Bienes en Préstamo o Comodato",
            "5.7": "5.7 Cédulas de Bienes Inmuebles",
            "5.8": "5.8 Inventario de Reservas Territoriales",
            "5.9": "5.9 Relación de Información Relativa a Catastro",
            "5.10": "5.10 Catálogo de Disposición Documental",
            "5.11": "5.11 Relación de Archivo de Trámite",
            "5.12": "5.12 Relación de Archivo de Concentración",
            "5.13": "5.13 Relación de Archivo Histórico",
            "5.14": "5.14 Relación de Archivos Electrónicos",
            "5.15": "5.15 Relación de Seguros Contratados",
            "5.16": "5.16 Relación de Combinaciones de Cajas Fuertes o las Áreas que procedan",
            "5.17": "5.17 Relación de Claves de Acceso",
            "5.18": "5.18 Relación de Llaves",
            "5.19": "5.19 Relación de Sellos Oficiales",
            "5.20": "5.20 Informe del Cumplimiento de Obligaciones Fiscales",
            "5.21": "5.21 Relación de Expedientes del Personal",
            "5.22": "5.22 Plantilla de Personal",
            "5.23": "5.23 Tabulador de Sueldos Autorizado",
            "5.24": "5.24 Catálogo de Puestos",
            "6.1": "6.1 Relación de Obras y Acciones Terminadas 2018-2021",
            "6.2": "6.2 Reporte de Obras y Acciones Pendientes de Terminar Física y/o Financieramente 2021",
            "6.3": "6.3 Relación de Obras y Acciones con Contrato en Proceso de Rescisión y/o en Trámite de Recuperación de Fianzas 2018 - 2021",
            "6.4": "6.4 Inventario de Materiales para la Obra Pública y Mantenimiento",
            "6.5": "6.5 Relación de Expedientes Técnicos Unitarios de Obras Públicas 2018-2021",
            "7.1": "7.1 Relación de Solicitudes de Información y Solicitudes ARCO Pendientes de Atender — Unidad de Transparencia",
            "7.2": "7.2 Relación de Recursos de Revisión en Trámite — Unidad de Transparencia",
            "7.3": "7.3 Relación de Documentación Relativa a Transparencia, Acceso a la Información y Protección de Datos Personales — Unidad de Transparencia",
            "8.1": "8.1 Sistema de Evaluación y Fiscalización de Veracruz (SEFISVER)",
            "8.2": "8.2 Resumen de Observaciones y Recomendaciones en Proceso de Atención",
            "8.3": "8.3 Decretos que Aprueban el Informe del Resultado de la Fiscalización Superior de las Cuentas Públicas 2017, 2018, 2019 y 2020",
            "8.4": "8.4 Seguimiento a los Procedimientos de Investigación y Substanciación de las Observaciones de Carácter Administrativo y Acciones Implementadas para la Atención de las Recomendaciones Determinadas por el ORFIS",
            "9.1": "9.1 Actividades de Atención Prioritaria",
        };
// Mostrar el texto de la clasificación seleccionada
document.addEventListener('DOMContentLoaded', function() {
    const clasificacion = getQueryParam('clasificacion');
    if (clasificacion && clasificacionesMap[clasificacion]) {
        document.getElementById('clasificacionSeleccionada').textContent = clasificacionesMap[clasificacion];
    } else {
        document.getElementById('clasificacionSeleccionada').textContent = "Clasificación no válida o no seleccionada.";
    }
});
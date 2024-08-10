document.getElementById('area').addEventListener('change', function() {
    const periodoContainer = document.getElementById('periodoContainer');
    const periodo = document.getElementById('periodo');
    const clasificacionContainer = document.getElementById('clasificacionContainer');
    const clasificacion = document.getElementById('clasificacion');
    const documentosContainer = document.getElementById('documentosContainer');
    const entregaRecepcion = document.getElementById('entregaRecepcion');

    // Limpiar opciones anteriores
    periodo.innerHTML = '<option value="">Seleccione un periodo</option>';
    clasificacion.innerHTML = '<option value="">Seleccione una clasificación</option>';
    entregaRecepcion.innerHTML = '<option value="">Seleccione un documento</option>';
    clasificacionContainer.style.display = 'none';
    documentosContainer.style.display = 'none';

    if (this.value) {
        periodoContainer.style.display = 'block';

        // Opciones específicas para el área seleccionada
        let opcionesPeriodo = [
            {value: 'enero', text: 'ENERO'},
            {value: 'febrero', text: 'FEBRERO'},
            {value: 'marzo', text: 'MARZO'},
            {value: 'abril', text: 'ABRIL'},
            {value: 'mayo', text: 'MAYO'},
            {value: 'junio', text: 'JUNIO'},
            {value: 'julio', text: 'JULIO'},
            {value: 'agosto', text: 'AGOSTO'},
            {value: 'septiembre', text: 'SEPTIEMBRE'},
            {value: 'octubre', text: 'OCTUBRE'},
            {value: 'noviembre', text: 'NOVIEMBRE'},
            {value: 'diciembre', text: 'DICIEMBRE'},
            {value: 'anual', text: 'ANUAL'}
        ];

        // Agregar opciones al select de periodo
        opcionesPeriodo.forEach(opcion => {
            const optionElement = document.createElement('option');
            optionElement.value = opcion.value;
            optionElement.textContent = opcion.text;
            periodo.appendChild(optionElement);
        });
    } else {
        periodoContainer.style.display = 'none';
    }
});

document.getElementById('periodo').addEventListener('change', function() {
    const clasificacionContainer = document.getElementById('clasificacionContainer');
    const documentosContainer = document.getElementById('documentosContainer');
    const areaSeleccionada = document.getElementById('area').value;
    const entregaRecepcion = document.getElementById('entregaRecepcion');
    const clasificacion = document.getElementById('clasificacion');

    clasificacionContainer.style.display = 'none';
    documentosContainer.style.display = 'none';

    if (this.value === 'anual') {
        // Mostrar el select de documentos y ocultar el de clasificación
        documentosContainer.style.display = 'block';
        clasificacionContainer.style.display = 'none';

        // Agregar opciones de documentos específicos por área
        let opcionesDocumentos = [];
        if (areaSeleccionada === 'presidencia') {
            opcionesDocumentos = [
                {value: 'presidencia-1', text: '2.1 Plan Municipal de Desarrollo 2022-2025'},
                {value: 'presidencia-2', text: '2.2 Programas Municipales 2022-2025'}
            ];
        } else if (areaSeleccionada === 'tesoreria') {
            opcionesDocumentos = [
                {value: 'tesoreria-1', text: '1.1 Organigrama Autorizado'},
                {value: 'tesoreria-2', text: '2.3 Programa de Actividades Institucionales 2021'},
                {value: 'tesoreria-3', text: '2.4 Informes de Avance del Programa de Actividades Institucionales 2021'},
                {value: 'tesoreria-3', text: '2.5 Programas Generales de Inversión (PGI) 2018-2021'},
                {value: 'tesoreria-3', text: '4.1 Ley de Ingresos 2021 y Proyecto Anual de la Ley de Ingresos 2022'},
                {value: 'tesoreria-3', text: '4.2 Presupuestos de Egresos 2021 y 2022'},
                {value: 'tesoreria-3', text: '4.3 Estados de Situación Financiera 2021'},
                {value: 'tesoreria-3', text: '4.4 Estados de Actividades 2021'},
                {value: 'tesoreria-3', text: '4.5 Estados de Variación en la Hacienda Pública 2021'},
                {value: 'tesoreria-3', text: '4.6 Estados de Cambios en la Situación Financiera 2021'},
                {value: 'tesoreria-3', text: '4.7 Estados de Flujos de Efectivo 2021'},
                {value: 'tesoreria-3', text: '4.8 Estados sobre Pasivos Contingentes 2021'},
                {value: 'tesoreria-3', text: '4.9 Notas a los Estados Financieros 2021'},
                {value: 'tesoreria-3', text: '4.10 Estados Analíticos del Activo 2021'},
                {value: 'tesoreria-3', text: '4.11 Estados Analíticos de la Deuda y Otros Pasivos 2021'},
                {value: 'tesoreria-3', text: '4.12 Estados Analíticos de Ingresos 2021'},
                {value: 'tesoreria-3', text: '4.13 Estados Analíticos del Ejercicio del Presupuesto de Egresos 2021'},
                {value: 'tesoreria-3', text: '4.14 Estados de Situación Financiera Detallados – LDF 2021'},
                {value: 'tesoreria-3', text: '4.15 Informes Analíticos de la Deuda Pública y Otros Pasivos – LDF 2021'},
                {value: 'tesoreria-3', text: '4.16 Informes Analíticos de Obligaciones Diferentes de Financiamientos – LDF 2021'},
                {value: 'tesoreria-3', text: '4.17 Balances Presupuestarios – LDF 2021'},
                {value: 'tesoreria-3', text: '4.18 Estados Analíticos de Ingresos Detallados – LDF 2021'},
                {value: 'tesoreria-3', text: '4.19 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF 2021 (Clasificación por Objeto  del Gasto)'},
                {value: 'tesoreria-3', text: '4.20 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF 2021 (Clasificación Administrativa)'},
                {value: 'tesoreria-3', text: '4.21 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF 2021 (Clasificación Funcional)'},
                {value: 'tesoreria-3', text: '4.22 Estados Analíticos del Ejercicio del Presupuesto de Egresos Detallados – LDF 2021 (Clasificación de Servicios Personales por Categoría)'},
                {value: 'tesoreria-3', text: '4.23 Proyecciones de Ingresos – LDF 2021'},
                {value: 'tesoreria-3', text: '4.24 Proyecciones de Egresos – LDF 2021'},
                {value: 'tesoreria-3', text: '4.25 Resultados de Ingresos – LDF 2021'},
                {value: 'tesoreria-3', text: '4.26 Resultados de Egresos – LDF 2021'},
                {value: 'tesoreria-3', text: '4.27 Informe sobre Estudios Actuariales – LDF 2021'},
                {value: 'tesoreria-3', text: '4.28 Guía de Cumplimiento de la Ley de Disciplina Financiera de las Entidades Federativas y los Municipios 2021'},
                {value: 'tesoreria-3', text: '4.29 Auxiliar Contable de Cuentas Bancarias 2021'},
                {value: 'tesoreria-3', text: '4.30 Auxiliar Contable de Cuentas por Cobrar 2021'},
                {value: 'tesoreria-3', text: '4.31 Auxiliar Contable de Cuentas por Pagar 2021'},
                {value: 'tesoreria-3', text: '4.32 Acuses de la presentación de los Estados Financieros y de Obra Pública Mensuales 2021'},
                {value: 'tesoreria-3', text: '4.33 Acuses de la Presentación al H. Congreso del Estado, de los Informes Trimestrales de Deuda 2021'},
                {value: 'tesoreria-3', text: '4.34 Cuenta Pública 2021'},
                {value: 'tesoreria-3', text: '4.35 Arqueo de Caja'},
                {value: 'tesoreria-3', text: '4.36 Conciliaciones Bancarias'},
                {value: 'tesoreria-3', text: '4.37 Situación de Talonarios de Cheques'},
                {value: 'tesoreria-3', text: '4.38 Inventario de Formas Valoradas'},
                {value: 'tesoreria-3', text: '5.1 Existencias de Almacén'},
                {value: 'tesoreria-3', text: '5.2 Inventario de Bienes Inmuebles, Infraestructura y Construcciones en Proceso'},
                {value: 'tesoreria-3', text: '5.3 Inventario de Bienes Muebles'},
                {value: 'tesoreria-3', text: '5.4 Inventario de Activos Intangibles'},
                {value: 'tesoreria-3', text: '5.5 Relación de Bienes no Inventariados'},
                {value: 'tesoreria-3', text: '5.6 Relación de Bienes en Préstamo o Comodato'},
                {value: 'tesoreria-3', text: '5.15 Relación de Seguros Contratados'},
                {value: 'tesoreria-3', text: '5.16 Relación de Combinaciones de Cajas Fuertes'},
                {value: 'tesoreria-3', text: '5.20 Informe del Cumplimiento de Obligaciones Fiscales'},
                {value: 'tesoreria-3', text: '5.21 Relación de Expedientes del Personal'},
                {value: 'tesoreria-3', text: '5.22 Plantilla de Personal'},
                {value: 'tesoreria-3', text: '5.23 Tabulador de Sueldos Autorizado'},
                {value: 'tesoreria-3', text: '5.24 Catálogo de Puestos'}
            ];
        } else if (areaSeleccionada === 'catastro') {
            opcionesDocumentos = [
                {value: 'catastro-1', text: '5.7 Cédulas de Bienes Inmuebles'},
                {value: 'catastro-1', text: '5.8 Inventario de Reservas Territoriales'},
                {value: 'catastro-1', text: '5.9 Relación de Información Relativa a Catastro'}
            ];
        } else if (areaSeleccionada === 'secretaria') {
            opcionesDocumentos = [
                {value: 'secretaria-1', text: '1.3 Relación de Entidades Paramunicipales'},
                {value: 'secretaria-1', text: '3.1 Relación de Reglamentos Municipales'},
                {value: 'secretaria-1', text: '3.2 Relación de Libros de Actas de Cabildo 2018-2021'},
                {value: 'secretaria-1', text: '3.3 Relación de Acuerdos de Cabildo Pendientes de Cumplir'},
                {value: 'secretaria-1', text: '3.4 Relación de Actas del Consejo de Planeación para el Desarrollo Municipal (COPLADEMUN) 2018-2021'},
                {value: 'secretaria-1', text: '3.5 Relación de Actas del Consejo de Desarrollo Municipal (CDM) 2018-2021'},
                {value: 'secretaria-1', text: '3.6 Relación de Juicios en Proceso Promovidos por el Ayuntamiento'},
                {value: 'secretaria-1', text: '3.7 Relación de Juicios en Proceso Promovidos en contra del Ayuntamiento'},
                {value: 'secretaria-1', text: '3.8 Relación de Contratos, Convenios o Acuerdos'},
                {value: 'secretaria-1', text: '5.10 Catálogo de Disposición Documental'},
                {value: 'secretaria-1', text: '5.12 Relación de Archivo de Concentración'},
                {value: 'secretaria-1', text: '5.13 Relación de Archivo Histórico'},
            ];
        } else if (areaSeleccionada === 'contraloria') {
            opcionesDocumentos = [
                {value: 'contraloria-1', text: '8.1 Sistema de Evaluación y Fiscalización de Veracruz(SEFISVER)'},
                {value: 'contraloria-1', text: '8.2 Resumen de Observaciones y Recomendaciones en Proceso de Atención'},
                {value: 'contraloria-1', text: '8.3 Decretos que Aprueban el Informe del Resultado de la Fiscalización Superior de las Cuentas Públicas 2017, 2018, 2019 y 2020'},
                {value: 'contraloria-1', text: '8.3 Decretos que Aprueban el Informe del Resultado de la Fiscalización Superior de las Cuentas Públicas 2017, 2018, 2019 y 2020'},

            ];
        } else if (areaSeleccionada === 'obras-publicas') {
            opcionesDocumentos = [
                {value: 'obras-publicas-1', text: '2.6 Reportes Trimestrales de Avances Físico-Financieros 2021'},
                {value: 'obras-publicas-1', text: '2.7 Cierres de Ejercicios 2018-2021'},
                {value: 'obras-publicas-1', text: '6.1 Relación de Obras y Acciones Terminadas 2018-2021'},
                {value: 'obras-publicas-1', text: '6.2 Reporte de Obras y Acciones Pendientes de Terminar Física y/o Financieramente 2021'},
                {value: 'obras-publicas-1', text: '6.3 Relación de Obras y Acciones con Contrato en Proceso de Rescisión y/o en Trámite de Recuperación de Fianzas 20182021'},
                {value: 'obras-publicas-1', text: '6.4 Inventario de Materiales para la Obra Pública y Mantenimiento'},
                {value: 'obras-publicas-1', text: '6.5 Relación de Expedientes Técnicos Unitarios de Obras Públicas 2018-2021'},
            ];
        } else if (areaSeleccionada === 'regidurias') {
            opcionesDocumentos = [
                {value: 'regidurias-1', text: '3.9 Informes de las Comisiones del Ayuntamiento 2018-2021'},
            ];
        } else if (areaSeleccionada === 'areas') {
            opcionesDocumentos = [
                {value: 'areas-1', text: '1.2 Relación de Manuales Administrativos'},
                {value: 'areas-1', text: '5.11 Relación de Archivo de Trámite'},
                {value: 'areas-1', text: '9.1 Actividades de Atención Prioritaria'},
            ];
        } else if (areaSeleccionada === 'areasUsuarias') {
            opcionesDocumentos = [
                {value: 'areasUsuarias-1', text: '5.14 Relación de Archivos Electrónicos'},
                {value: 'areasUsuarias-1', text: '5.17 Relación de Claves de Acceso'},
                {value: 'areasUsuarias-1', text: '5.19 Relación de Sellos Oficiales'},
            ];
        } else if (areaSeleccionada === 'Utransparencia') {
            opcionesDocumentos = [
                {value: 'regidurias-1', text: '7.1 Relación de Solicitudes de Información y Solicitudes ARCO Pendientes de Atender'},
                {value: 'regidurias-1', text: '7.2 Relación de Recursos de Revisión en Trámite'},
                {value: 'regidurias-1', text: '7.3 Relación de Documentación Relativa a Transparencia, Acceso a la Información y Protección de Datos Personales'},
            ];
        } 
        // Limpiar y agregar nuevas opciones de documentos
        entregaRecepcion.innerHTML = '<option value="">Seleccione un documento</option>';
        opcionesDocumentos.forEach(opcion => {
            const optionElement = document.createElement('option');
            optionElement.value = opcion.value;
            optionElement.textContent = opcion.text;
            entregaRecepcion.appendChild(optionElement);
        });

    } else if (this.value) {
        // Mostrar el select de clasificación si no es "anual"
        clasificacionContainer.style.display = 'block';

        // Mostrar opciones de clasificación específicas por periodo y área
        let opcionesClasificacion = [];
        if (areaSeleccionada === 'presidencia') {
            if (this.value === 'enero' || this.value === 'febrero') {
                opcionesClasificacion = [
                    {value: 'of-enviados', text: 'marco'},
                    {value: 'of-recibidos', text: 'O.F Recibidos'}
                ];
            } 
        } else if (areaSeleccionada === 'tesoreria') {
            if (this.value === 'enero' || this.value === 'febrero') {
                opcionesClasificacion = [
                    {value: 'solicitudes', text: 'gonzalez'},
                    {value: 'of-recibidos', text: 'salomon'}
                ];
            }
        } 
        // Continua con las demás áreas y periodos...

        // Limpiar y agregar nuevas opciones de clasificación
        clasificacion.innerHTML = '<option value="">Seleccione una clasificación</option>';
        opcionesClasificacion.forEach(opcion => {
            const optionElement = document.createElement('option');
            optionElement.value = opcion.value;
            optionElement.textContent = opcion.text;
            clasificacion.appendChild(optionElement);
        });
    }
});

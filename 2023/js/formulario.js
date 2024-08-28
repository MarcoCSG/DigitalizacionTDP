document.getElementById('area').addEventListener('change', function() {
    const clasificacionContainer = document.getElementById('clasificacionContainer');
    const clasificacion = document.getElementById('clasificacion');
    const documentosContainer = document.getElementById('documentosContainer');
    const entregaRecepcion = document.getElementById('entregaRecepcion');
    const periodoContainer = document.getElementById('periodoContainer');
    const periodo = document.getElementById('periodo');

    // Limpiar las opciones anteriores
    clasificacion.innerHTML = '<option value="">Seleccione una clasificación</option>';
    entregaRecepcion.innerHTML = '<option value="">Seleccione un documento</option>';
    periodo.innerHTML = '<option value="">Seleccione un periodo</option>';

    // Ocultar todos los contenedores
    clasificacionContainer.style.display = 'none';
    documentosContainer.style.display = 'none';
    periodoContainer.style.display = 'none';

    if (this.value) {
        // Mostrar el select de clasificación
        clasificacionContainer.style.display = 'block';

        // Opciones de clasificación basadas en el área seleccionada
        let opcionesClasificacion = [];
        if (this.value === 'presidencia') {
            opcionesClasificacion = [
                {value: 'documentosG', text: 'DOCUMENTOS GENERALES'},
            ];
        } else if (this.value === 'sindicatura') {
            opcionesClasificacion = [
                {value: 'secretariaP', text: 'SECRETARÍA PARTICULAR'},
                {value: 'juridico', text: 'JURÍDICO'},
                {value: 'patrimonio', text: 'PATRÍMONIO'},
                {value: 'gobernacion', text: 'GOBERNACIÓN'}
            ];
        } else if (this.value === 'secretaria') {
            opcionesClasificacion = [
                {value: 'archivoHistorico', text: 'ARCHIVO HISTORICO'},
                {value: 'actasYacuerdos', text: 'TRAMITES ACTAS Y ACUERDOS'},
                {value: 'reclutamiento', text: 'RECLUTAMIENTO'},
                {value: 'cronista', text: 'CRONISTA MUNICIPAL'},
                {value: 'oficialia', text: 'OFICIALIA DE PARTES'},
                {value: 'registroCivil', text: 'REGISTRO CIVIL'},
                {value: 'enlace', text: 'ENLACE SRE'},

            ];
        } else if (this.value === 'regidores') {
            opcionesClasificacion = [
                {value: 'documentosG', text: 'DOCUMENTOS GENERALES'},
            ];
        } else if (this.value === 'tesoreria') {
            opcionesClasificacion = [
                {value: 'catastro', text: 'CATASTRO'},
                {value: 'comercio', text: 'COMERCIO'},
                {value: 'ingresos', text: 'INGRESOS'},
                {value: 'egresos', text: 'EGRESOS Y PRESUPUESTO'},
                {value: 'contabilidad', text: 'CONTABILIDAD Y BANCOS'},
                {value: 'fiscal', text: 'EJECUCION FISCAL'},
                {value: 'documentosG', text: 'DOCUMENTACION GENERAL'},

            ];
        } else if (this.value === 'contraloria') {
            opcionesClasificacion = [
                {value: 'AuObras', text: 'AUDITORIA DE OBRA PUBLICA'},
                {value: 'AuFinanciera', text: 'AUDITORIA FINANCIERA '},
                {value: 'AuLegalidad', text: 'AUDITORIA DE LEGALIDAD Y DESEMPEÑO'},
                {value: 'investigacion', text: 'INVENTIGACION '},
                {value: 'substanciacion', text: 'SUBSTANCIACION'},
            ];
        } else if (this.value === 'obraspublicas') {
            opcionesClasificacion = [
                {value: 'ramo', text: 'RAMO 033'},
                {value: 'asentamiento', text: 'ASENTAMIENTOS HUMANOS'},
                {value: 'planeacion', text: 'PLANEACION Y PROYECTOS'},
                {value: 'costo', text: 'COSTOS Y PRESUPUESTOS'},
                {value: 'caminos', text: 'CAMINOS MUNICIPALES'},
                {value: 'licencias', text: 'LICENCIAS Y PERMISOS '},
                {value: 'supervicion', text: 'SUPERVICION DE OBRA PUBLICA '},
                {value: 'seguimiento', text: 'SEGUIMIENTO Y CONTROL'},
                {value: 'mantenimientoUrbano', text: 'MANTENIMIENTO URBANO'},
                {value: 'mantenimientoEdificios ', text: 'MANTENIMIENTO DE EDIFICIOS'},
            ];
        }

        // Llenar el select de clasificación
        opcionesClasificacion.forEach(opcion => {
            const optionElement = document.createElement('option');
            optionElement.value = opcion.value;
            optionElement.textContent = opcion.text;
            clasificacion.appendChild(optionElement);
        });
    }
});

document.getElementById('clasificacion').addEventListener('change', function() {
    const documentosContainer = document.getElementById('documentosContainer');
    const entregaRecepcion = document.getElementById('entregaRecepcion');

    // Limpiar las opciones anteriores
    entregaRecepcion.innerHTML = '<option value="">Seleccione un documento</option>';
    documentosContainer.style.display = 'none';

    if (this.value) {
        // Mostrar el select de documentos
        documentosContainer.style.display = 'block';

        // Opciones de documentos basadas en la clasificación seleccionada
    //*PRESIDENCIA 
        let opcionesDocumentos = [];
        if (this.value === 'documentosG') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
    //?SINDICATURA
        } else if (this.value === 'secretariaP') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'juridico') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'patrimonio') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'gobernacion') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
    //!secretaria
        } else if (this.value === 'archivoHistorico') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'actasYacuerdos') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'reclutamiento') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'cronista') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'oficialia') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'registroCivil') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'enlace') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
    //todo:TESORERIA
        } else if (this.value === 'catastro') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'comercio') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'ingresos') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'egresos') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'contabilidad') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'fiscal') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
    //*CONTRALORIA
        } else if (this.value === 'AuObras') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'AuFinanciera') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'AuLegalidad') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'investigacion') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'substanciacion') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
    //! OBRAS PUBLICAS
        } else if (this.value === 'ramo') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'asentamiento') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'planeacion') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'costo') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'caminos') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'licencias') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'supervicion') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'seguimiento') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'mantenimientoUrbano') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        } else if (this.value === 'mantenimientoEdificios') {
            opcionesDocumentos = [
                {value: 'SoliciEyR', text: 'SOLICITUDES  ENVIDAS Y RECIBIDAS'},
                {value: 'gestiones', text: 'GESTIONES INSTITUCIONALES'},
                {value: 'informes', text: 'INFORMES DE TRASNPARENCIA '},
                {value: 'oficios', text: 'OFICIOS ENVIADOS Y RECIBIDOS'},
                {value: 'otros', text: 'OTROS DOCUMENTALES'},
            ];
        }

        // Llenar el select de documentos
        opcionesDocumentos.forEach(opcion => {
            const optionElement = document.createElement('option');
            optionElement.value = opcion.value;
            optionElement.textContent = opcion.text;
            entregaRecepcion.appendChild(optionElement);
        });
    }
});

document.getElementById('entregaRecepcion').addEventListener('change', function() {
    const periodoContainer = document.getElementById('periodoContainer');
    const periodo = document.getElementById('periodo');

    // Limpiar las opciones anteriores
    periodo.innerHTML = '<option value="">Seleccione un periodo</option>';
    periodoContainer.style.display = 'none';

    if (this.value) {
        // Mostrar el select de periodo
        periodoContainer.style.display = 'block';

        // Opciones de periodo (estas son generales, pero pueden adaptarse)
        const opcionesPeriodo = [
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

        // Llenar el select de periodo
        opcionesPeriodo.forEach(opcion => {
            const optionElement = document.createElement('option');
            optionElement.value = opcion.value;
            optionElement.textContent = opcion.text;
            periodo.appendChild(optionElement);
        });
    }
});

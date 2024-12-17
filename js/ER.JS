


document.addEventListener("DOMContentLoaded", function () { 
    const areaSelect = document.getElementById("area");
    const clasificacionContainer = document.getElementById("clasificacionContainer");
    const clasificacionSelect = document.getElementById("clasificacion");
    const municipioInput = document.getElementById("municipio"); // Campo oculto del municipio
    const consultaBtn = document.getElementById("consultaBtn");
    const resultado = document.getElementById("resultado");
    
    /** 
    * ! CLASIFICACIONES UNICAMENTE PARA CREAR REPORTES
    */
    const datos = {
        "MUNICIPIO DE MISANTLA, VER": {
            "areas": {
                "presidencia_er": { 
                    texto: "PRESIDENCIA MARCO",
                    clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php" },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
            "tesoreria_er": {
                texto: "TESORERÍA",
                clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "4.35", texto: "4.35 Arqueo de Caja" },//AL FINAL 
                    { valor: "4.36", texto: "4.36 Conciliaciones Bancarias" },//AL FINAL 
                    { valor: "4.37", texto: "4.37 Situación de Talonarios de Cheques", form: "4.37_situacionTalonarioCheques.php" },
                    { valor: "4.38", texto: "4.38 Inventario de Formas Valoradas", form: "4.38_inv_formas_valoradas.php"  },
                    { valor: "5.1", texto: "5.1 Existencias de Almacén", form: "5.1_existencias_almacen.php"},
                    { valor: "5.5", texto: "5.5 Relación de Bienes no Inventariados", form: "5.5_relacion_bienes_no_inventariados.php" },
                    { valor: "5.6", texto: "5.6 Relación de Bienes en Préstamo o Comodato", form: "5.6_relacion_bienes_comodato_prestamo.php" },
                    { valor: "5.7", texto: "5.7 Cédulas de Bienes Inmuebles" },
                    { valor: "5.8", texto: "5.8 Inventario de Reservas Territoriales", form: "5.8_Inventario_reservas_territoriales.php" },
                    { valor: "5.9", texto: "5.9 Relación de Información Relativa a Catastro", form: "5.9_relacion_informacion_relativa.php"  },
                    { valor: "5.15", texto: "5.15 Relación de Seguros Contratados",form:"5.15_relacion_seguros_contratados.php" },
                    { valor: "5.16", texto: "5.16 Relación de Combinaciones de Cajas Fuertes" },
                    { valor: "5.20", texto: "5.20 Informe del Cumplimiento de Obligaciones Fiscales" },
                    { valor: "5.21", texto: "5.21 Relación de Expedientes del Personal" },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php" },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
            "catastro_er": {
                texto: "CATASTRO",
                clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "5.7", texto: "5.7 Cédulas de Bienes Inmuebles" },
                    { valor: "5.8", texto: "5.8 Inventario de Reservas Territoriales" },
                    { valor: "5.9", texto: "5.9 Relación de Información Relativa a Catastro", form: "5.9_relacion_informacion_relativa.php" },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
                "secretaria_er": {
                    texto: "SECRETARIA",
                    clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "1.3", texto: "1.3 Relación de Entidades Paramunicipales", form:"1.3_relacion_entidades_paramunicipales.php"},
                    { valor: "3.1", texto: "3.1 Relación de Reglamentos Municipales" , form:"3.1_relacion_de_reglamentos.php"},
                    { valor: "3.2", texto: "3.2 Relación de Libros de Actas de Cabildo", form:"3.2_relacion_libros_cabildo.php" },
                    { valor: "3.3", texto: "3.3 Relación de Acuerdos de Cabildo Pendientes de Cumplir", form:"3.3_relacion_acuerdos_cabildo_pendientes.php" },
                    { valor: "3.4", texto: "3.4 Relación de Actas del Consejo de Planeación para el Desarrollo Municipal (COPLADEMUN)", form:" 3.4_relacion_actas_consejo_planeacion.php" },
                    { valor: "3.5", texto: "3.5 Relación de Actas del Consejo de Desarrollo Municipal (CDM)", form:"3.5_relacion_actas_CDM.php" },
                    { valor: "3.6", texto: "3.6 Relación de Juicios en Proceso Promovidos por el Ayuntamiento", form: "3.6_relacion_juicios_enProceso.php" },
                    { valor: "3.7", texto: "3.7 Relación de Juicios en Proceso Promovidos en contra del Ayuntamiento", form: "3.7_relacion_juicios_enContra.php" },
                    { valor: "3.8", texto: "3.8 Relación de Contratos, Convenios o Acuerdos", form:"3.8_relacion_contratos_convenios.php" },
                    { valor: "5.12", texto: "5.12 Relación de Archivo de Concentración", form: "5.12_relacion_archivo_concentracion.php" },
                    { valor: "5.13", texto: "5.13 Relación de Archivo Histórico", form: "5.13_relacion_archivo_historico.php" },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
                "contraloria_er": {
                    texto: "CONTRALORIA",
                    clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "8.1", texto: "8.1 Sistema de Evaluación y Fiscalización de Veracruz(SEFISVER)" },
                    { valor: "8.2", texto: "8.2 Resumen de Observaciones y Recomendaciones en Proceso de Atención" },
                    { valor: "8.3", texto: "8.3 Decretos que Aprueban el Informe del Resultado de la Fiscalización Superior de las Cuentas Públicas" },
                    { valor: "8.4", texto: "8.4 Seguimiento a los Procedimientos de Investigación y Substanciación de las Observaciones de Carácter Administrativo y Acciones Implementadas para la Atención de las Recomendaciones Determinadas por el ORFIS" },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },

                ],
            },
            "obraspublicas_er": {
                    texto: "OBRAS PUBLICAS",
                    clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "6.3", texto: "6.3 Relación de Obras y Acciones con Contrato en Proceso de Rescisión y/o en Trámite de Recuperación de Fianzas" },
                    { valor: "6.4", texto: "6.4 Inventario de Materiales para la Obra Pública y Mantenimiento" },
                    { valor: "6.5", texto: "6.5 Relación de Expedientes Técnicos Unitarios de Obras Públicas" },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
            "regidores_er": {
                texto: "REGIDORES",
                clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
            "Utransparencia_er": {
                texto: "UNIDAD DE TRANSPARENCIA",
                clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "7.1", texto: "7.1 Relación de Solicitudes de Información y Solicitudes ARCO Pendientes de Atender" },
                    { valor: "7.2", texto: "7.2 Relación de Recursos de Revisión en Trámite" },
                    { valor: "7.3", texto: "7.3 Relación de Documentación Relativa a Transparencia, Acceso a la Información y Protección de Datos Personales" },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ]
            },
            "juridico_er": {
                texto: "JURIDICO",
                clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "3.1", texto: "3.1 Relación de Reglamentos Municipales" , form:"3.1_relacion_de_reglamentos.php"},
                    { valor: "3.6", texto: "3.6 Relación de Juicios en Proceso Promovidos por el Ayuntamiento", form: "3.6_relacion_juicios_enProceso.php" },
                    { valor: "3.7", texto: "3.7 Relación de Juicios en Proceso Promovidos en contra del Ayuntamiento", form: "3.7_relacion_juicios_enContra.php" },
                    { valor: "3.8", texto: "3.8 Relación de Contratos, Convenios o Acuerdos", form:"3.8_relacion_contratos_convenios.php"  },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
        },
    },

        "MUNICIPIO DE SANTIAGO TUXTLA, VER": {
            "areas": {
                "presidencia_er": { 
                    texto: "PRESIDENCIA - SANTIAGO",
                    clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
            "tesoreria_er": {
                texto: "TESORERÍA",
                clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "4.35", texto: "4.35 Arqueo de Caja" },//AL FINAL 
                    { valor: "4.36", texto: "4.36 Conciliaciones Bancarias" },//AL FINAL 
                    { valor: "4.37", texto: "4.37 Situación de Talonarios de Cheques", form: "4.37_situacionTalonarioCheques.php"},
                    { valor: "4.38", texto: "4.38 Inventario de Formas Valoradas", form: "4.38_inv_formas_valoradas.php" },
                    { valor: "5.1", texto: "5.1 Existencias de Almacén", form: "5.1_existencias_almacen.php" },
                    { valor: "5.5", texto: "5.5 Relación de Bienes no Inventariados", form: "5.5_relacion_bienes_no_inventariados.php" },
                    { valor: "5.6", texto: "5.6 Relación de Bienes en Préstamo o Comodato", form: "5.6_relacion_bienes_comodato_prestamo.php" },
                    { valor: "5.7", texto: "5.7 Cédulas de Bienes Inmuebles" },
                    { valor: "5.8", texto: "5.8 Inventario de Reservas Territoriales", form: "5.8_Inventario_reservas_territoriales.php" },
                    { valor: "5.9", texto: "5.9 Relación de Información Relativa a Catastro", form: "5.9_relacion_informacion_relativa.php" },
                    { valor: "5.15", texto: "5.15 Relación de Seguros Contratados",form:"5.15_relacion_seguros_contratados.php" },
                    { valor: "5.16", texto: "5.16 Relación de Combinaciones de Cajas Fuertes" },
                    { valor: "5.20", texto: "5.20 Informe del Cumplimiento de Obligaciones Fiscales" },
                    { valor: "5.21", texto: "5.21 Relación de Expedientes del Personal" },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
            "catastro_er": {
                texto: "CATASTRO",
                clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "5.7", texto: "5.7 Cédulas de Bienes Inmuebles" },
                    { valor: "5.8", texto: "5.8 Inventario de Reservas Territoriales" },
                    { valor: "5.9", texto: "5.9 Relación de Información Relativa a Catastro", form: "5.9_relacion_informacion_relativa.php"  },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
                "secretaria_er": {
                    texto: "SECRETARIA",
                    clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "1.3", texto: "1.3 Relación de Entidades Paramunicipales", form:"1.3_relacion_entidades_paramunicipales.php" },
                    { valor: "3.1", texto: "3.1 Relación de Reglamentos Municipales", form:"3.1_relacion_de_reglamentos.php" },
                    { valor: "3.2", texto: "3.2 Relación de Libros de Actas de Cabildo", form:"3.2_relacion_libros_cabildo.php" },
                    { valor: "3.3", texto: "3.3 Relación de Acuerdos de Cabildo Pendientes de Cumplir", form:"3.3_relacion_acuerdos_cabildo_pendientes.php" },
                    { valor: "3.4", texto: "3.4 Relación de Actas del Consejo de Planeación para el Desarrollo Municipal (COPLADEMUN)", form:" 3.4_relacion_actas_consejo_planeacion.php"},
                    { valor: "3.5", texto: "3.5 Relación de Actas del Consejo de Desarrollo Municipal (CDM)", form: "3.5_relacion_actas_CDM.php" },
                    { valor: "3.6", texto: "3.6 Relación de Juicios en Proceso Promovidos por el Ayuntamiento", form: "3.6_relacion_juicios_enProceso.php" },
                    { valor: "3.7", texto: "3.7 Relación de Juicios en Proceso Promovidos en contra del Ayuntamiento", form: "3.7_relacion_juicios_enContra.php" },
                    { valor: "3.8", texto: "3.8 Relación de Contratos, Convenios o Acuerdos", form:"3.8_relacion_contratos_convenios.php"  },
                    { valor: "5.12", texto: "5.12 Relación de Archivo de Concentración", form: "5.12_relacion_archivo_concentracion.php" },
                    { valor: "5.13", texto: "5.13 Relación de Archivo Histórico", form: "5.13_relacion_archivo_historico.php" },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
                "contraloria_er": {
                    texto: "CONTRALORIA",
                    clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "8.1", texto: "8.1 Sistema de Evaluación y Fiscalización de Veracruz(SEFISVER)" },
                    { valor: "8.2", texto: "8.2 Resumen de Observaciones y Recomendaciones en Proceso de Atención" },
                    { valor: "8.3", texto: "8.3 Decretos que Aprueban el Informe del Resultado de la Fiscalización Superior de las Cuentas Públicas" },
                    { valor: "8.4", texto: "8.4 Seguimiento a los Procedimientos de Investigación y Substanciación de las Observaciones de Carácter Administrativo y Acciones Implementadas para la Atención de las Recomendaciones Determinadas por el ORFIS" },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },

                ],
            },
            "obraspublicas_er": {
                    texto: "OBRAS PUBLICAS",
                    clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "6.3", texto: "6.3 Relación de Obras y Acciones con Contrato en Proceso de Rescisión y/o en Trámite de Recuperación de Fianzas" },
                    { valor: "6.4", texto: "6.4 Inventario de Materiales para la Obra Pública y Mantenimiento" },
                    { valor: "6.5", texto: "6.5 Relación de Expedientes Técnicos Unitarios de Obras Públicas" },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
            "regidores_er": {
                texto: "REGIDORES",
                clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
            "areasUsuarias_er": {
                texto: "AREAS USUARIAS",
                clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
            "Utransparencia_er": {
                texto: "UNIDAD DE TRANSPARENCIA",
                clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "7.1", texto: "7.1 Relación de Solicitudes de Información y Solicitudes ARCO Pendientes de Atender" },
                    { valor: "7.2", texto: "7.2 Relación de Recursos de Revisión en Trámite" },
                    { valor: "7.3", texto: "7.3 Relación de Documentación Relativa a Transparencia, Acceso a la Información y Protección de Datos Personales" },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ]
            },
            "juridico_er": {
                texto: "JURIDICO",
                clasificaciones: [
                    // todas las areas
                    { valor: "1.2", texto: "1.2 Relación de Manuales Administrativos", form: "1.2_manuales.php" },
                    { valor: "5.11", texto: "5.11 Relación de Archivo de Trámite", form: "5.11_archivotramite.php" },
                    { valor: "5.18", texto: "5.18 Relación de Llaves", form: "5.18_relacionllaves.php" },
                    { valor: "9.1", texto: "9.1 Actividades de Atención Prioritaria", form: "9.1_actividadesDeAtencion.php"},
                    // clasificaciones
                    { valor: "3.1", texto: "3.1 Relación de Reglamentos Municipales" , form:"3.1_relacion_de_reglamentos.php"},
                    { valor: "3.6", texto: "3.6 Relación de Juicios en Proceso Promovidos por el Ayuntamiento", form: "3.6_relacion_juicios_enProceso.php" },
                    { valor: "3.7", texto: "3.7 Relación de Juicios en Proceso Promovidos en contra del Ayuntamiento", form: "3.7_relacion_juicios_enContra.php" },
                    { valor: "3.8", texto: "3.8 Relación de Contratos, Convenios o Acuerdos", form:"3.8_relacion_contratos_convenios.php"  },
                    // clasificaciones areas usuarias
                    { valor: "5.14", texto: "5.14 Relación de Archivos Electrónicos", form:"5.14_relacion_archivos_electronicos.php"  },
                    { valor: "5.17", texto: "5.17 Relación de Claves de Acceso" },
                    { valor: "5.19", texto: "5.19 Relación de Sellos Oficiales" },
                ],
            },
        },
    },
};

// Función para cargar las áreas según el municipio
function cargarAreas() {
    const municipio = municipioInput.value; // Obtener el municipio del campo oculto
    const areas = datos[municipio]?.areas || {};

    areaSelect.innerHTML = '<option value="">Seleccione una opción</option>'; // Resetear el select

    for (const [clave, areaData] of Object.entries(areas)) {
        const option = document.createElement('option');
        option.value = clave;
        option.textContent = areaData.texto; // Mostrar el texto definido
        areaSelect.appendChild(option);
    }

    // Ocultar el contenedor de clasificaciones al cambiar el municipio
    clasificacionContainer.style.display = 'none';
    clasificacionSelect.innerHTML = '<option value="">Seleccione una clasificación</option>';
}

// Función para cargar las clasificaciones según el área seleccionada
function cargarClasificaciones() {
    const municipio = municipioInput.value;
    const areaSeleccionada = areaSelect.value;
    const clasificaciones = datos[municipio]?.areas[areaSeleccionada]?.clasificaciones || [];

    clasificacionSelect.innerHTML = '<option value="">Seleccione una clasificación</option>'; // Resetear el select

    clasificaciones.forEach(clasificacion => {
        const option = document.createElement('option');
        option.value = clasificacion.valor;
        option.textContent = clasificacion.texto;
        clasificacionSelect.appendChild(option);
    });

    // Mostrar u ocultar el contenedor de clasificaciones según si hay opciones
    clasificacionContainer.style.display = clasificaciones.length > 0 ? 'block' : 'none';
}

// Event listener para cuando se cambia el área
areaSelect.addEventListener('change', cargarClasificaciones);

// Event listener para el botón de búsqueda
consultaBtn.addEventListener('click', function () {
    const municipio = municipioInput.value;
    const areaSeleccionada = areaSelect.value;
    const clasificacionSeleccionada = clasificacionSelect.value;
    const anioFijo = 2022; // Año fijo

    // Validaciones
    if (!areaSeleccionada) {
        alert('Por favor, selecciona un área.');
        return;
    }

    if (!clasificacionSeleccionada) {
        alert('Por favor, selecciona una clasificación.');
        return;
    }

    // Buscar la clasificación seleccionada para obtener el 'form' si existe
    const clasificacion = datos[municipio]?.areas[areaSeleccionada]?.clasificaciones.find(c => c.valor === clasificacionSeleccionada);

    if (clasificacion && clasificacion.form) {
        // Construir la URL con los parámetros seleccionados y el año fijo 2022
        const params = new URLSearchParams({
            anio: anioFijo,
            area: areaSeleccionada,
            clasificacion: clasificacionSeleccionada
        });

        const url = `${clasificacion.form}?${params.toString()}`;
        window.location.href = url;
    } else {
        // Si no hay 'form', puedes manejar la búsqueda de otra manera, por ejemplo, mostrar resultados en la página
        // Aquí un ejemplo simple de cómo mostrar un mensaje
        resultado.innerHTML = `<p>Has seleccionado la clasificación: ${clasificacionSeleccionada}</p>`;
    }
});

// Inicializar las áreas al cargar la página
cargarAreas();
});
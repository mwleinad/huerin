/* 
	JSON Document 
	------------------------------------------------------------------------------------------------------
	------------------------------------------------------------------------------------------------------
		
	JSON encargado de la multientidad
	
	pa_a: Particulares España GBP
	
	
	------------------------------------------------------------------------------------------------------
*/

// Variables
var mailContacto 			= "";
var numeroTelefono  		= "";
var numeroTelefonoCab 		= "902 30 10 00";
var urlInformacion  		= "";
var urlFormularioContacto  	= "";

// Variables para el traductor
var JLI_derechos 	= "@ Banco Popular. Todos los derechos reservados";
var JLI_aviso 		= "Aviso legal";

var JLI_nif_Cif = "NIF / CIF";

var JLI_tipoUsuario = "Particulares";
var JLI_Acceso		= "Acceso de usuarios registrados";
var JLI_explain1	= "";
var JLI_explain2	= "";
var JLI_servicio_atencion_cliente 	= "";
var JLI_num_atencion_cliente_texto  = " " + numeroTelefono;
var JLI_num_atencion_cliente_texto2 = " o ";
var JLI_num_atencion_cliente 		= JLI_num_atencion_cliente_texto + JLI_num_atencion_cliente_texto2;
var JLI_num_atencion_cliente2 		= JLI_num_atencion_cliente_texto;

var JLI_sabermas	= "";
var JLI_informacion = "Información";
var JLI_solicitud   = "Solicitud de Contratación";
var JLI_tarifas     = "Tarifas"; 
var JLI_empresas	= "";
var JLI_demo 		= "Demo";
var JLI_olvidoClave = "¿Olvidó su clave?";
var JLI_primerAcceso = "Primer acceso";

var JLI_atencion_cliente 			= "Atención al cliente";
var JLI_seleccionContrato 			= "Selección de contratos";
var JLI_irEmpresasParticulares		= "Ir a empresas"; // "Ir a particulares"

var JLI_masInformacion 					= "Más información";
var JLI_clavesProvisionales 			= "Acceso con claves provisionales";
var JLI_modificarClavesProvisionales 	= "Modificar claves provisionales";
var JLI_seleccionContratoTitulo 		= "Selección de Contrato para Identificación: Detalle.";
var JLI_dnie 							= "Acceso con dni-e";
var JLI_cierreSesion 					= "Cierre de sesion";

var JLI_textoCabecera_OTE = "Recuerde que para finalizar la orden debe imprimir este documento, firmarlo y llevarlo a la sucursal del Banco Popular m&aacute;s cercana.";
var JLI_pie_OTE = "DIE / MOD. SER098 (1-03) Banco Popular Espa&ntilde;ol, S.A., Domicilio social: Vel&aacute;zquez, 34, esquina a Goya, 35, MADRID. Reg. Merc. de Madrid: T. 174. F. 44. H. 5458. Inscrip. 1&ordm;. N.I.F. A-28000727 www.bancopopular.es";

var urlHome 		= "";
var JLI_home_title 	= "Logo bancopopular.es";
var JLI_home_alt 	= "Logo bancopopular.es";

var JLI_tooltip_logo = "Volver a posición integral";
var JLI_tooltip_families = "Desplegar/Plegar listado de"; //Parametrizado según familia
var JLI_tooltip_favorites = "Operaciones favoritas"; 
var JLI_tooltip_alerts = "Alertas";
var JLI_tooltip_eBuzon = "Correspondencia";
var JLI_tooltip_points = "Puntos Banca Multicanal";
var JLI_tooltip_present = "Nuevo regalo";
var JLI_tooltip_banners = "Ir a"; //Parametrizado según campaña
var JLI_tooltip_mobile = "Disponible en Banca móvil";

var JLI_cuenta_transf_internacional = "Cuenta de cargo: ";
var JLI_columna_valor = "Valor liquidativo";

var JLI_texto_sol_tarjeta = "Si no hubiera recibido la tarjeta de coordenadas, solicite una en su oficina.";
var JLI_texto_title = "Bienvenido a Banco Popular";

// Variable JSON

var gbp_mult = 
{
	"plantilla_general":
		[		 
			{						 
				 "esPortugal_Activo"			: "false",			
				 // Pie de la aplicacion.
				 "alineacion_footer"			: "right",
				 "copyright"					: JLI_derechos,			
				 "aviso_texto"					: JLI_aviso,			
				 "aviso_url"					: "#",		//Aviso Legal.
				 "mailContacto_Activo"			: "true",
				 "tipo_Color_links"				: true,					//Cambia el color de los links
				 "mensajepromoreactclaves"		: true,
			 	 "anyadirFavoritosImagen"		: false,				//Icono de Añadir Favoritos 
			 	 "eliminarIntervinientes"		: false,				//Eliminar Añadir intervinientes
			 	 "mutuel"						: "Targobank"				 	 
			}	 
		],		
	 "idiomas": 
		[ 
		   	{ 
		  		 // Indica si los idiomas estan activos
		  		 "idiomas_Activo"				: "true",
			  
		  		 // Indica si estan activos cada uno de los idiomas
		  		 "idioma_esp_activo"			: "true",
			 	 "idioma_esp_texto"				: "Español",
			  
			 	 "idioma_cat_activo"			: "true",
			 	 "idioma_cat_texto"				: "Català",	
			  
			 	 "idioma_alm_activo"			: "true",
			 	 "idioma_alm_texto"				: "Deutsch",
			  
			 	 "idioma_eng_activo"			: "true",
			 	 "idioma_eng_texto"				: "English",
			  
			 	 "idioma_eus_activo"			: "true",
			 	 "idioma_eus_texto"				: "Euskera",
			  
			 	 "idioma_fra_activo"			: "true",
			 	 "idioma_fra_texto"				: "Français",
			  
			 	 "idioma_gal_activo"			: "true",
			 	 "idioma_gal_texto"				: "Galego",
			  
			 	 "idioma_por_activo"			: "true",
			 	 "idioma_por_texto"				: "Português"
		   	}
		],		
	"acceso": 
		[
	        { 
	        	 "tituloTexto"						: "Banco Popular: Banco online, Depósitos, Tarjetas, Nóminas, Fondos de Inversión",
	        	 "metaDescription"					: "Ofrece servicios de banca por Internet, red de sucursales y cajeros, simuladores para cálculos financieros y atención al cliente",
	        	 "metaKeywords"						: "Banco Popular, Banco online, Depósitos, Tarjetas, Nominas, planes de pensiones, fondos de inversión, banco andalucia, banco castilla, banco vasconia, banco credito balear, escredit, banco galicia,",	        	 
	        	 "tipoUsuario_Activo"				: "true",
	        	 "tipoUsuario"						: JLI_tipoUsuario,		//Tipo usuario: particulares,empresa...
	        	 "acceso"  							: JLI_Acceso,			//Título de la página.
				 
	        	 // Enlace Home
	        	 "enlace_Home_Activo"  				: "false",
				 "enlace_Home_Url"		    		: urlHome,
				 "enlace_Home_Title"         		: JLI_home_title,
				 "enlace_Home_Alt"         			: JLI_home_alt,
				 
	        	 // Enlace de información.
	        	 "enlace_Informacion_Activo"  		: "true",
				 "enlace_Informacion_Url"		    : urlInformacion,
				 "enlace_Informacion_Texto"         : JLI_informacion,
				 
				 // Enlace de solicitud de contratos.
				 "enlace_SolicitudContratos_Activo"	  : "true",
				 "enlace_SolicitudContratos_Url"      : "/esp/gbp/htm/contratacion_DNIe.html",
				 "enlace_SolicitudContratos_Texto"    : JLI_solicitud,
				 
				 
				 //Enlace de tarifas.
				 "enlace_Tarifas_Activo"			: "true",
				 "enlace_Tarifas_Url"		  	    : "#",
				 "enlace_Tarifas_Texto"             : JLI_tarifas,
				 "enlace_Tarifas_Cabecera"			: "false",
				 "enlace_Tarifas_Pie"				: "true",
				 
				 //Enlace de empresas.
				 "enlace_IrEmpresas_Activo"			: "false",
				 "enlace_IrEmpresas_Url"		    : "",
				 "enlace_IrEmpresas_Texto"			: JLI_empresas,				 				 				
				 
				 //Enlace Demo.
				 "enlace_modoDemo_Activo"			: "true",
				 "enlace_modoDemo_Url"				: "", 
				 "enlace_modoDemo_Texto"			: JLI_demo,
				 
				 // Ayuda elegir tipo identificación.
				 "enlace_elegir_Activo"				: "true",
				 "enlace_elegir_Url"				: "/esp/gbp/htm/claves.htm?TB_iframe=true&height=290&width=740",
				 
				 // Identificación del tipo de usuario.
				 "identificacionTipo_Activo"		: "true",
				 
				 // Recordatorio de clave.
				 "recordatorioClave_Activo"			: "true",	
				 "recordatorioClave_Texto"			: JLI_olvidoClave,
				 
				 // Primer acceso.
				 "primerAcceso_Activo"				: "false",
				 "primerAcceso_Texto"				: JLI_primerAcceso,
				 
				 "accesoDniElectronio_Activo"		: "true",
				 
				 // Texto adicional de la página.
				 "textoAdicional_Activo"			: "true",
				 "textoAdicional_1"					: JLI_explain1,
				 "textoAdicional_2"					: JLI_explain2,				 
				 
				 "mailContacto"						: mailContacto,
				 
				 // Opcion de saber mas.
				 "saberMas_Activo"					: "true",
				 "saberMas_Texto"					: JLI_sabermas,
				 "saberMas_Modal"					: "true",
				 "saberMas_Url"						: "/AppGBP/esp/gbp/jsp/info_seguridad.jsp?TB_iframe=true&height=470&width=740",
				 
				 // Opcion de atencion al cliente.
				 "atencionCliente_Activo"			: "true",
				 "atencionCliente"					: JLI_servicio_atencion_cliente, 
				 "atencionClienteNumero"			: JLI_num_atencion_cliente,

				 // Opcion de seguridad.
				 "seguridad_Activo"					: "true",
				 "seguridad_url"    				: "/esp/gbp/htm/seguridad.htm?xxx=&TB_iframe=true&height=410&width=740",
				 
				 // Informacion de seguridad de pagina modal
				 "infoSeguridad_Texto"				: JLI_masInformacion,
				 "infoSeguridad_url"				: "/esp/gbp/htm/seguridad.htm",
					 
				 // Titulo claves provisionales
				 "tituloBienvenida"  				: JLI_clavesProvisionales,
				 
				 // Titulo modificacion de clave provisional
				 "tituloModificacionClave"			: JLI_modificarClavesProvisionales,
				 
				 // Imagen botón
				 "imagenBoton_Activo"				: "false",
				 "imagenBoton_claseCSS"				: "botonECOM",
				 
				 // Cambio tipo de perfil
				 "cambioTipoPerfil"					: "false",
				 "tipoPerfil"						: "",
				 
				 //Desconexion de la aplicacion.
				 "tituloDesconexion"				: JLI_cierreSesion,
			 	 "desconexion_imagen"				: "true",				//Icono de Desconexión
				 "desconexion_url"					: ""
	        }
	    ],
	"dnie":
		[
		    {
		    	"tituloTexto"						: JLI_dnie,
		    	"logo"                              : true
		    }			 
		],
	"seleccion_contrato": 
		[
		 	{
		 		"tituloTexto"						: JLI_seleccionContratoTitulo,
		 		
		 		//Formato de hora: 
		 		// 	true 	-> 			dd de mm de AAAA
		 		// 	false 	->			
		 		"formatoHora_ddmmaaaa"				: "true",	
		 		"buscador"							: "true",
		 		"tipo_cabecera"						: "particular"		 	}		 
		],
	
	"seleccion_intervinientes": 
	   	[
		 	{
		 		  "nif"							: JLI_nif_Cif
			}		 
		],
	"cabecera": 
		[
		 {
				 // Opcion de atencion al cliente.
				 "atencionCliente_Activo"				: "true",
				 "atencionCliente_Texto"				: JLI_atencion_cliente,
				 "atencionCliente_urlLink"				: "true", //Define si el texto se trata como link o como texto plano
				 "atencionCliente_Url"					: "/esp/gbp/htm/contacto_gbp.html?height=390&width=810&TB_iframe=true&xxx=",
				 "atencionCliente_Numero"				: numeroTelefono,
				 "atencionCliente_Mail"					: mailContacto,
				 
				 // Datos cabecera cambio contraseña.
				 "atencionCliente_NumeroCab"			: numeroTelefonoCab,
					 
				 // Opcion de Seleccion de Contrato.
				 "seleccionContrato_Activo"				: "true",
				 "seleccionContrato_Texto"				: JLI_seleccionContrato,
				 "seleccionContrato_Url"				: "",	
					 
				 // Opcion de ir a particulares/empresas.
				 "ir_Activo"							: "true",
				 "ir_Texto"								: JLI_irEmpresasParticulares,
				 "ir_Url"								: "",
					 
				 // Opcion de menu.	
				 "menu_Activo"							: "true"
				 
		 	}
		],
	"posicion_integral" : 
		[
		 	{
		 		 "fecha"                             	: true, //activa la fecha en el titulo de posicion integral
		 		 
				 "alertas"                           	: false, //activa el link de alertas de posicion integral
				 "texto_tarjetas_activar"            	: true, //activa el texto 'tiene tarjetas para activar'				 
				 "listar_noticias"                   	: true, //lista en la caja 'alertas' las noticias disponibles para el cliente
				 				 
				 "var_continuar"                     	: false, //pendiente de Antonio
				 "span_saldo_contabilistico"         	: false, //activa el <span> de  'Saldo contabilístico'
				 "columna_saldo"             			: false, //modifica la columna 'Saldo' de la tabla 'Cuentas Vista'
				 "tipo_tarjeta_num_familia"				: false, //Dependiendo del numero de familia que recibamos se escribe el numero de tarjeta de una forma u otra
				 "num_de_cuenta"                    	: false, //modifica el 'numero de cuenta' de la tabla 'Cuentas Vista'
				 "num_balance"                       	: true, //muestra el numero de balance bajo la columna 'Saldo' en la tabla 'Cuentas Vista' en 'posicion integral'
				
				 "link_ver_mas1"                     	: false, //settea url del link 'ver mas'
				 "link_ver_mas2"                     	: false, //settea url del link 'ver mas'
				 "link_ver_mas3"                     	: true, //settea url del link 'ver mas'
				
			     "operacion_impagados_pendientes"    	: false, //mete el link a 02_151_pio 
			     "listado_cuotas_leasing"            	: true, //mete el link de 02_629_in2 con idItem=fin_mif_000_000_000_000
			     "listado_cuotas_leasing_lea"        	: false, //mete el link de 02_629_in2 con idItem=fin_lea_000_000_000_000
			     "listado_cuotas_renting"            	: false, //mete el link de 02_630_in2 con idItem=fin_ren_000_000_000_000
			     "listado_cuotas_renting_mif"        	: true, //mete el link de 02_630_in2 con idItem=fin_mif_000_000_000_000
			     "disposiciones"                     	: true, //mete el link de 02_232_rel
			
			     "grupos"								: true, //muestra la posición integral agrupando las familias
			     
				 //tooltips de diferentes elementos de la web
				 "tooltips_activo"						: true, 
				 "tooltip_logo"							: JLI_tooltip_logo,
				 "tooltip_families"						: JLI_tooltip_families,
				 "tooltip_favorites"					: JLI_tooltip_favorites,
				 "tooltip_alerts"						: JLI_tooltip_alerts,
				 "tooltip_eBuzon"						: JLI_tooltip_eBuzon,
				 "tooltip_points"						: JLI_tooltip_points,
				 "tooltip_present"						: JLI_tooltip_present,
				 "tooltip_banners"						: JLI_tooltip_banners,
				 "tooltip_mobile"						: JLI_tooltip_mobile,
				
				 "aniade_cuenta_patrimonial_form_hidden": false, //mete los valores de 'idExternaContrato' y 'cuentaPatrimonial' en los input hidden del form
				 "input_Producto_form_hidden"        	: true, //mete los productos '547' y '540' en los input hidden del form
				 "iselect_numero_Depositante"        	: false, //mete el valor 'numeroDepositante' en los input hidden del form
				 "item_Tarjeta"                      	: false, //mete el valor de 'item_Tarjeta' en el href de 'ver más'
				 "item_Tarjeta2"                     	: false, //mete el valor de 'item_Tarjeta' en el href de 'ver más'
				 
				 "mostrar_enlace_graficas"				:true
			}
		],
	"errores"	:
		[
		 	{
		 		"enlace_error_contrato"					: true
		 	}
		 ],
	"buscadorSucursales": true,			// activa el buscador y selección de sucursales para operaciones.
	"pantalla_ok"	:
		[
		 	{
		 		"control_aumento_limites_tarjeta"		: true	//¿mostrar distintos mensajes de OK para operativa de cambio de límites?
		 	}
		 ],
	"pantalla_cambio_limites"	:
		[
		 	{
		 		"cargar_favoritos_portugal"		: false,
		 		"mostrar_explicaportugal"		: false,
		 		"crear_operationdatabar_portugal": false,
		 		"identificador_perfil1014"		: "B02",
		 		"texto_inserte_limite"			: true,
		 		"texto_cambiar_temporal"		: true
		 	}
		 ],
	"campo_origen_perfil_440": "A",	//Valor del campo origen del perfil 440 
	"tarjetas"	:
		[
		 	{
		 		"identificador_perfil1014"	: "B21"
		 	}
		 ],
	"campanias"	:
		[
		 	{
		 		"pagos_agrupados"					: false,
		 		"emision_recibos"					: false,
		 		"movil_agregador"					: false,
		 		"pan_pin"							: true,
		 		"ampliacion_capital_ECOM"			: false,	//se pone false porque no se tiene que añadir a todos, tiene público objetivo.
		 		"url_ampliacion_capital"			: "/Bpemotor?_SD=" + parent.SDTEMP + "&codOperacion=000754&_ABT_FROM_PART=04_045_in&idItem=aho_val_ofi_000_000_000&numFamilia=04&pagina=000&actualizar=S",
		 		"param_panpin_s_ac"					: "A",
		 		"param_panpin_GL_trans"				: "ECLU",
		 		"cierre_DiaD"						: true
		 	}
		 ],
	"transfEntreEntidades":				// Impresión documento transferencia entre entidades.
		[
		 	{
		 		"textoCabecera"	: JLI_textoCabecera_OTE,
		 		"pie"			: JLI_pie_OTE,
				"cabeceraImagen": "/esp/gbp/bin/img/traspaso_02-pa_a.jpg",
				"firmaImagen"	: "/esp/gbp/bin/img/bancofirma-pa_a.jpg"
		 	}
		],
	"Transferencias":					// Impresión documento transferencia entre entidades.
		[
		 	{
		 		"avisoDescubierto"						: false,
		 		"textoDescubierto"						: "",
		 		"cuenta_transf_internacional"			: JLI_cuenta_transf_internacional
		 	}
		],
	"remitenteCorreo":					// Remitente del correo de transferencias
		[
			{
				"entidad"						: "Banco Popular",
				"webEntidad"					: "bancopopular.es",
				"from"							: "info@bancopopular.es"
			}
		],			
	"Epigrafes":					// PDFs de epigrafes
		[
		 	{
		 		"Epigrafe12_modificacion_efactura"				: "/esp/gbp/bin/pdf/epig12.pdf",
		 		"Epigrafe12_contratacion_efactura"				: "/esp/gbp/bin/pdf/epig12.pdf"
		 	}
		],	
	"ebuzon":
		[
		 	{
		 		"enlace_duplicado_domicilio"				: true
		 	}
		],

	"Cartera_Fondos":
		[
		 	{
		 		"columna_valor_liquidacion"	: JLI_columna_valor
		 	}
		],	
	
	"Contrataciones":
		[
		 	{
		 		"mostrarSucursal"	: false,
		 		"sucursal"			: "Of. Principal Madrid (0229 7000)"
		 	}
		],	
		
		"TelefonoParticulares":
		[
		 	{
		 		"mostrarTelefono"	: true		 		
		 	}
		],	

	"ihcExclusionContratos": "53_122_in_par",

	"iconoLinkNuevaVentana": true,		//Indica, para las pantallas que lo incluyan, si en la entidad esta activo el icono con enlace a una ventana nueva.
	
	"MostrarLiteralEbuzon": false,
	
	"MostrarNuevaImagenCreditoSolicitar": false,     //Nuevas imagenes en la distribuidora de solicitar prestamos
	
	/** ACTIVACION TARJETA DE COORDENADAS**/
	"activacionTarjeta":
		[
		 	{
		 		"texto_sol_tarjeta"				: JLI_texto_sol_tarjeta,
		 		"telefono_consulta"				: "902 19 88 19",
	 			"telefono_clave_admin"			: "902 19 88 19",
	 			"texto_title"					: JLI_texto_title,
	 			"nombre_web"					: "Banco Popular"
		 	}	
		 ]
		
};

/* 
 * ***************************************************************************************************************
 * 											 	CUADERNOS				
 * 										( relacion_cuadernos.htx )
 * 
 *  NOTA: Parametrizacion de las url de los cuadernos que se muestran en la pagina: 
 *  	Ficheros > Gestión del servicio > Solicitud inc/exc de ficheros
 *
 * ***************************************************************************************************************
 */


/* Constantes */
var JLI_INC = "Incluido";
var JLI_EXC = "Excluido";

var JLI_txt1="Adeudos domiciliados (Norma - 19 A.E.B.)";
var JLI_txt2="Remesas de efectos (Norma - 32 A.E.B.)";
var JLI_txt3="Movimientos de cuentas (Norma - 43 A.E.B.)";
var JLI_txt4="Cobros por ventanilla (Norma - 57 A.E.B.)";
var JLI_txt5="Anticipos de créditos (Norma - 58 A.E.B.)";
var JLI_txt6="Fichero con formato libre (previo acuerdo con el Banco)";
var JLI_txt7="Transferencias/cheques  (Norma - 34 A.E.B.)";
var JLI_txt8="Pagos detallados";
var JLI_txt9="Confirming Popular";
var JLI_txt10="Pagos a proveedores";
var JLI_txt11="Ordenes internacionales de pago";
var JLI_txt12="SDD Core - Presentaciones C19-14";
var JLI_txt13="SDD Core - Solicitudes de Cancelación C19-14";
var JLI_txt14="Ingreso electrónico de cheques";
var JLI_mensaje = "Solicite más información en su sucursal o en el teléfono";

JLI_mensaje+=top.telefonoContacto;

var JLI_titulo = "Información";


//Array que incluye los codigos de los cuadernos
var Codigos_Servicio = new Array(
	"601",JLI_txt1,
	"602",JLI_txt2,
	"603",JLI_txt7,
	"604",JLI_txt3,
	"605",JLI_txt5,
	"606",JLI_txt4,
	"607",JLI_txt8,
	"608",JLI_txt9,
	"609",JLI_txt10,
	"610",JLI_txt11,
	"611",JLI_txt6,
	"612",JLI_txt14
//	"622",JLI_txt12,
//	"623",JLI_txt13
	);

// Array que incluye la URL de los cuadernos
var url_cuadernos = new Array(	
		"",
		"",
		"",
		"",
		"",
		"/esp/gbp/htm/00_900_ventana.html?TB_iframe=true&height=172&width=711",
		"/esp/gbp/htm/00_900_ventana.html?TB_iframe=true&height=172&width=711",
		"http://www.bancoiones-banca-online/programa-de-gestion-de-empresas/confirming-popular/confirming-popular.htm",
		"/esp/gbp/htm/00_900_ventana.html?TB_iframe=true&height=172&width=711",
		"http://www.barama-de-gestion-de-empresas/transferencias-al-extranjero/transferencias-al-extranjero.htm",
		"/esp/gbp/htm/00_900_ventana.html?TB_iframe=true&height=172&width=711",
		"/esp/gbp/htm/00_900_ventana.html?TB_iframe=true&height=172&width=711"
	);
var arrayErrors;			
var modeId;					
function Control(formName, listControls)
{
	
	var arrErrors = new Array();
	var element;  
	
	var strWarningResult = "";
	var fSubmit = true;  
	var JLI_textError1 = "Lamentablemente no se puede continuar, ya que se han encontrado los siguientes errores:";
	var JLI_textError2 = "<br><br>";
	var objectForm = document.getElementById(formName);
	var nameFormVariable;
	var nameControl;
	arrayErrors = new Array();

	   
	if (arguments.length == 1) 
	{	  
		modeId = false;
		$.each(objectForm, function(item, option)
		{strWarningResult += ControlElement( option );});
	}
	else if ( arguments.length == 2 ) 
	{
		modeId = true;
		for ( var nCont = 0; nCont < listControls.length; nCont++ ) 
		{
			option = document.getElementById(listControls[nCont]);
			strWarningResult += ControlElement(option);
		}		
	}	
	for ( var nCont = 0; nCont < arrayErrors.length; nCont++ ) 
	{
		if ( arrayErrors[nCont].value )
			eval("$(\"#" + arrayErrors[ nCont ].ident + "\").css(\"background-color\",\"#FCC5BC\");");
		else	
			eval("$(\"#" + arrayErrors[ nCont ].ident + "\").css(\"background-color\",\"\");");	
	}
	if( strWarningResult == "" )
	{
		$("#warning").css("display","none");
	}
	else
	{
		fSubmit = false;
		$("#warning .text").html( JLI_textError1 +'<br>'+ strWarningResult + JLI_textError2);
	    //$("#warning .imagen").attr("title", "Error");
	    $("#warning").attr("title", "Error");
		$("#warning").css( "display", "block" );
		self.scroll(1,1);		
	}
	return fSubmit;
}
function ControlElement(option)
{
	
	var strWarningResult = ""; 
	var strResult = ""; 
	var strObjectType = "";
	var nValueLen = "";
	var fOptional = "true";
	var strRuler = "";
	var strTextRuler = "";
	var strParams = "";
	var aosParams = new Array();
	if (option.lang != "")
	{
		strParams = option.lang;
		aosParams = strParams.split("|");
		strObjectName = aosParams[0];
		strObjectType = aosParams[1];
		nValueLen = aosParams[2];
		fOptional = aosParams[3];
		strRuler = aosParams[4];
		strTextRuler = aosParams[5];
		strResult = ControlObject(option, strObjectName, option.value, strObjectType, nValueLen, fOptional, strRuler, strTextRuler);
		if (strResult != "") {
			strWarningResult += "<br>- " + strResult;
			eval("$(\"#" + option.id + "\").css(\"background-color\",\"#FCC5BC\");");
		}
		else 
			eval("$(\"#" + option.id + "\").css(\"background-color\",\"\");");
	}
	return strWarningResult;
}	
function ControlObject(object, strName, strValue, ObjectType, nValueLen, fOptional, ruler, textRuler)
{
	var strResult = "";
	var JLI_text_Error1 = "Debe escribir al menos";
	var JLI_text_Error2 = "dígitos en el campo";
	var JLI_text_Error3 = ", por favor, revíselo.";
	var JLI_text_Error4 = "No ha escrito ninguna fecha, insértela con el formato día-mes-año ( dd-mm-yyyy ).";
	var JLI_text_Error5 = "Escriba una fecha valida con el formato día-mes-año ( dd-mm-yyyy ).";
	var JLI_text_Error6 = "Escriba una hora con el formato hora:minuto:segundos ( 00:00:00 ).";
	var JLI_text_Error7 = "Escriba una hora valida con el formato hora:minuto:segundos ( 00:00:00 ).";
	var JLI_text_Error8 = "El valor ingresado no es un número entero en el campo ";
	var JLI_text_Error9 = ", por favor, revíselo.";
	var JLI_text_Error10 = "Debe escribir un número entero en el campo ";
	var JLI_text_Error11= "Debe escribir un importe válido en el campo ";
	var JLI_text_Error12= "</b> separando los decimales con una coma";
	var JLI_text_Error12_2= "</b> separando los decimales con una coma o un punto";	
	var JLI_text_Error13= "El valor ingresado no es numérico, por favor, revíselo.";
	var JLI_text_Error14= "Debe escribir al menos <b>";
	var JLI_text_Error15= "</b> dígitos en el campo, por favor, revíselo.";
	var JLI_text_Error16= "Debe escribir dos decimales y separarlos con un punto ('.')";
	var JLI_text_Error17= "El campo <b>";
	var JLI_text_Error18= "</b> no puede estar vacío, por favor, revíselo.";
	var JLI_text_Error19= "No se han escrito en el campo <b>";
	var JLI_text_Error20= "</b> todos los caracteres requeridos, por favor, revíselo.";
	var JLI_text_Error21= "No ha escrito ningún <b>email</b>, por favor, revíselo.";
	var JLI_text_Error22= "No ha escrito un email válido, por favor, revíselo.";
	var JLI_text_Error23= "Debe elegir una opción en el campo <b>";
	var JLI_text_Error24= ".</b>";
	var JLI_text_Error26= "Por favor, escriba un <b>Nombre de Beneficiario</b> con caracteres validos.";
	var JLI_text_Error25= "El campo <b>Nombre de Beneficiario</b> no puede estar vacío, por favor, revíselo.";
	var JLI_text_Error27 = "No ha escrito ninguna fecha, insértela con el formato día/mes/año ( dd/mm/aaaa ) en el campo ";
	var JLI_text_Error28 = "Escriba una fecha válida con el formato día/mes/año ( dd/mm/aaaa ) en el campo ";
	var JLI_text_Error29 = "Debe escribir un importe válido sin signo en el campo <b>";
	var JLI_text_Error30 = "El valor del campo <b>";
	var JLI_text_Error31 = "</b> y su <b>confirmación</b> deben ser iguales, por favor, revíselo.";
	var JLI_text_Error32 = "Carácter no válido. Solamente puede introducir números, letras (sin acentos) y los caracteres especiales (‘-‘ y ‘/’) en este campo."; 
	var JLI_text_Error33 = "Solamente puede introducir letras  en este campo";
	var JLI_text_Error34 = "Carácter no válido. Solamente puede componerse de letras sin acentos (se diferencia entre minúsculas y mayúsculas), números y símbolos especiales (asterisco, punto, guión y  símbolo más)";
	var JLI_text_Error35 = ", por favor, rev&iacute;selo. ";

	
	if (fOptional == "true" && strValue.length == 0) 
		return strResult;
	else 
	{
		if ( ruler != "" ) 
		{
			if ( ruler.indexOf( "Function(" ) != -1 ) 
			{strResult = eval(ruler);}
			else 
			{
				var fRegRuler = new RegExp(ruler);
				if (!fRegRuler.test(strValue)) 
					strResult = textRuler;
			}
		}
		if (nValueLen != 0 && strValue.length < nValueLen) 
			strResult = JLI_text_Error1 + " " + nValueLen + " " + JLI_text_Error2 + ' <b>' + strName + '</b>' + JLI_text_Error3;
		if (ObjectType == "Date") 
		{
			var strDate = /^([0|1|2][0-9]|3[0|1])(\/|-)(0[1-9]|1[012])\2(\d{4})$/;
			if (strValue.length == 0) 
				strResult = JLI_text_Error4;
			else 
			{
				if (!strDate.test(strValue)) 
					strResult = JLI_text_Error5;
			}
		}else if (ObjectType == "Date1") 
		{			
			if (strValue.length != 0) 
				strResult = valiDateFunction( strValue,  strName );				
			else 
			{				
				strResult = JLI_text_Error27 + strName + JLI_text_Error3;
			}
		}else if (ObjectType == "Date2") 
		{
			var strDate = /^([0|1|2][0-9]|3[0|1])(\/|-)(0[1-9]|1[012])\2(\d{4})$/;
			if (strValue.length == 0) 
				strResult = JLI_text_Error27 + strName;
			else 
			{
				if (!strDate.test(strValue)) 
					strResult = JLI_text_Error28 + strName;
			}
		}
		else if (ObjectType == "Date3") 
		{
			var strDate = /^([0|1|2][0-9]|3[0|1])(\/|-)(0[1-9]|1[012])\2(\d{4})$/;
			if ( strValue.length != 0 && strValue !='dd/mm/aaaa')		 
			{
				if (!strDate.test(strValue)) 
					strResult = JLI_text_Error28 + strName;
			}
		}
		else if (ObjectType == "Date_dma"){
			var strDate = /^(0?[1-9]|[12][0-9]|3[01]|[d]{2})[-\/.](0?[1-9]|1[012]|[m]{2})[-\/.]([0-9]{4}|[a]{4})$/;
			if( strValue.length != 0 && !strDate.test(strValue) ){
				if( textRuler.length == 0 ){
					strResult = JLI_text_Error28 + strName;
				}else{
					strResult = textRuler + strName;
				}
			}
		}else if (ObjectType == "Date_amd"){
			var strDate = /^([0-9]{4}|[a]{4})[-\/.](0?[1-9]|1[012]|[m]{2})[-\/.](0?[1-9]|[12][0-9]|3[01]|[d]{2})$/;
			if ( strValue.length != 0 && !strDate.test(strValue) ){
				if( textRuler.length == 0 ){
					strResult = JLI_text_Error28 + strName;
				}else{
					strResult = textRuler + strName;
				}
			}
		}else if (ObjectType == "RegulerCaracterExpresion") 
		{ 
			var strCaracter = /^[^+~\'*»ºª\^¨«éáí!óúàèìòùÉÁÍÓçÇãÃâÂêÊîÎ,;:\-\_õÕôÔûÛ-€´`ÚÀÈÌ.ÒÙ@£=?çÇ§#\$\{\[\]\}\(\)%&\/<>\\|\"]*$/;
			if (strValue.length == 0) 
				strResult = JLI_text_Error25;
			else 
			{
				if (!strCaracter.test(strValue)) 
					strResult = JLI_text_Error26;
			}
		}  
		else if (ObjectType == "Time") 
		{
			var strTime = /^(0\d|1\d|2[0-3]):([0-5]\d):([0-5]\d)$/;
			if (strValue.length == 0) 
				strResult = JLI_text_Error6;
			else 
			{
				if (!strTime.test(strValue)) 
					strResult = JLI_text_Error7;
			}
		}
		else if (ObjectType == "Number") 
		{
			var strNumber = /^(?:\+|-)?\d+$/;
			if (isNaN(strValue)) 
				strResult = JLI_text_Error8 +' <b>'+ strName + '</b> ' + JLI_text_Error35;
			else
			{
				if (!strNumber.test(strValue)) 
					strResult = JLI_text_Error10 +' <b>'+ strName + '</b>'+ JLI_text_Error9 ;
			}
		}
		else if (ObjectType == "equal") 
		{
			var strNumber = /^(?:\+|-)?\d+$/;
			if (isNaN(strValue)) 
				strResult = JLI_text_Error8 + strName + JLI_text_Error9;
			else if (!strNumber.test(strValue)) 
				strResult = JLI_text_Error10 +'<b>'+ strName +'</b>'+ JLI_text_Error9;
			var objectComparation = document.getElementById(textRuler);
			var comparationName = objectComparation.lang.split("|")[0];
			if (strValue != objectComparation.value) {
				if (strResult == "")
					strResult = JLI_text_Error30 + textRuler + JLI_text_Error31;
				else
					strResult += "<br>- " + JLI_text_Error30 + comparationName + JLI_text_Error31;
			}
		}
		else if (ObjectType == "Amount") 
		{
			var strAmount = /^(\d*)(\.\d{3})*((\,)(\d*))?$/;
			if (!strAmount.test(strValue) || (strValue.length == 0 && fOptional == "false")) 
				strResult = JLI_text_Error11 +'<b>'+ strName +'</b>'+ JLI_text_Error12;
		}
		else if (ObjectType == "Amount2")
		{
			var strAmount = /^(\d{1,})((\,|\.)(\d{1,4}))?$/;
			if (!strAmount.test(strValue) || (strValue.length == 0 && fOptional == "false"))
		 		strResult = JLI_text_Error11 +'<b>'+ strName +'</b>'+ JLI_text_Error12_2;
		}
		else if (ObjectType == "Amount3")
		{
			var strAmount = /^(\d{1,})((\,)(\d{1,4}))?$/;
			if (!strAmount.test(strValue) || (strValue.length == 0 && fOptional == "false"))
		 		strResult = JLI_text_Error29 + strName +","+ JLI_text_Error12;
		}
		else if (ObjectType == "Amount4")
		{
			var strAmount = /^(?:\+|-)(\d{1,})((\,)(\d{1,4}))?$/;
			if (!strAmount.test(strValue) || (strValue.length == 0 && fOptional == "false"))
		 		strResult = JLI_text_Error29 + strName +","+ JLI_text_Error12;
		}		
		else if (ObjectType == "Float")
		{
			var strFloat = /^(?:\+|-)?\d+\.\d*$/;
			if (isNaN(strValue)) 
				strResult = JLI_text_Error13;
			else 
			{
				if (nValueLen != 0 && strValue.length < nValueLen) 
					strResult = JLI_text_Error14 + nValueLen + JLI_text_Error15;
				else 
					if (!strFloat.test(strValue)) 
						strResult = JLI_text_Error16;
			}
		}
		else if (ObjectType == "String")
		{
				if (strValue.length == 0) 
					strResult = JLI_text_Error17 + strName + JLI_text_Error18;
				else 
				{
					if (strValue.length < nValueLen) 
						strResult = JLI_text_Error19 + strName + JLI_text_Error20;
				}
		}
		else if (ObjectType == "Email")
		{
			var strEmail = /^[A-Z, a-z, 0-9,\-,_,\.]+\@[A-Z, a-z, 0-9,\-,_]+\.[A-Z, a-z, 0-9,_]+/;
			
			if (strValue.length == 0) 
				strResult = JLI_text_Error21;
			else 
			{
				if (!strEmail.test(strValue)) 
					strResult = JLI_text_Error22;
			}
		}
		else if (ObjectType == "wordNumber")
		{
			var strEmail = /^[A-Z a-z 0-9 ñ Ñ \-,\/]+$/;
			
			if (strValue.length != 0) {
				if (!strEmail.test(strValue)) 
					strResult = JLI_text_Error32;
			}
		}
		else if (ObjectType == "wordNumber2")
		{
			var strEmail = /^[A-Z a-z 0-9 ñ Ñ \+\.\*\-]+$/;
			
			if (strValue.length != 0) {
				if (!strEmail.test(strValue)) 
					strResult = JLI_text_Error34;
			}
		}
		else if (ObjectType == "word")
		{
			var strEmail = /^[a-z A-Z ñ Ñ \s]+$/;
			
			if (strValue.length != 0) {
				if (!strEmail.test(strValue)) 
					strResult = JLI_text_Error33;
			}
		}
		else if (ObjectType == "Select")
		{
			if (fOptional != "true" && (strValue == "" || strValue == "default")) 
				strResult =  JLI_text_Error23 + strName + JLI_text_Error24;
		}
		else if (ObjectType == "mensajerror")
		{
		   if (fOptional != "true" && (strValue == "" || strValue == "default")) 
				strResult =  textRuler;
		}
		else if ( ObjectType == "Boolean" )	// Objeto Boolean
		{
			options = strObjectName.split( "," );
			if ( strValue == "false" )
			{
				strResult = textRuler;
				for (var nCont = 0; nCont < options.length; nCont++)
				{ 
					arrayErrors.push({
						ident: options[ nCont ],
						value: true
					});
				}
			}
			else if ( modeId ) 
			{
				for (var nCont = 0; nCont < options.length; nCont++) 
				{
					arrayErrors.push({
						ident: options[ nCont ],
						value: false
					});
				}
			}
		}
		return strResult;
	}
}
function ControlDecimalFunction(strValue, strName, nDecimal)
{
	var strResult = ""; 
	var aosCutStr = new Array();
	var JLI_text_Error1= "Debe escribir ";
	var JLI_text_Error2=  " decimales en el campo ";
	var JLI_text_Error3= " separandolos por una coma.";
	var JLI_text_Error4= "Debe escribir al menos ";
	var JLI_text_Error5= "Debe escribir solo ";
	/* Si tiene decimales y no ha puesto la coma */
	if (nDecimal > 0 && strValue.indexOf(",") == -1) 
		strResult = JLI_text_Error1 + nDecimal + JLI_text_Error2 +'<b>'+ strName +'</b>'+ JLI_text_Error3;
	else 
		if (strValue.indexOf(",") != -1)
		{aosCutStr = strValue.split(",");
			if (aosCutStr[1].length < nDecimal) 
				strResult = JLI_text_Error4 + nDecimal + JLI_text_Error2 + strName + JLI_text_Error3;
			else 
				if (aosCutStr[1].length > nDecimal) // Si tiene mas decimales que los requeridos.
					strResult = JLI_text_Error5 + nDecimal + JLI_text_Error2 + strName + JLI_text_Error3;
		}
	return strResult;
}

function ControlRangoFunction(strValue, strName, limInf, limSup)
{
	var strResult = "";
	var nAux;
	var JLI_text_Error1 = "El campo";
	var JLI_text_Error2 =  " debe tener un valor comprendido entre  ";
	nAux = new Number(strValue.replace(",", "."))
	if ((nAux < limInf) || (nAux > limSup)) 
		strResult = JLI_text_Error1 + '<b>' + strName + '</b>' + JLI_text_Error2 + limInf + " y " + limSup;
	return strResult;
}
function ControlMoreThanFunction( strValue, strName, limInf )
{
	var strResult = "";
	var nAux;
	var JLI_text_Error1 = "El campo ";
	var JLI_text_Error2 =  "debe tener un valor mayor que ";
	nAux = new Number(strValue.replace(",", "."))
	if (nAux < limInf) 
		strResult = JLI_text_Error1  +'<b>'+ strName +'</b> '+ JLI_text_Error2 + limInf;
	return strResult;
}
function ControlLessThanFunction(strValue, strName, limSup)
{
	var strResult = "";
	var nAux;
	var JLI_text_Error1 = "El campo ";
	var JLI_text_Error2 =  " debe tener un valor menor que ";
	nAux = new Number(strValue.replace(",", "."));
	if (nAux > limSup) 
		strResult = JLI_text_Error1 +'<b>'+ strName +'</b>'+ JLI_text_Error2 + limSup;
	return strResult;
}

function ControlLessAndThanFunction(strValue, strName, limSup , limInf)
{

	var strResult = "";
	var nAux;
	var JLI_text_Error1 = "El campo ";
	var JLI_text_Error2 =  " debe tener un valor menor que ";
	var JLI_text_Error3 =  "</b> debe tener un valor mayor que ";
	
	nAux = new Number(strValue.replace(",", "."));
	
	if (nAux > limSup) 
		strResult = JLI_text_Error1 +'<b>'+ strName +'</b>'+ JLI_text_Error2 + limSup;
	return strResult;
}
 
function IsMultFunction(strValue, strName, mult){
	var strResult = "";
	var nAux;
	var JLI_text_Error1 = "El campo <b>";
	var JLI_text_Error2 = "</b> debe ser múltiplo de ";
	nAux = new Number(strValue);
	if ((nAux % mult) != 0) 
		strResult = JLI_text_Error1 + strName + JLI_text_Error2 + mult;
	return strResult;
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
/// <summary>
///  	Funcion que valida la fecha introducida por el usuario
/// </summary>
/// <param name="strDate">
///		Valor del objeto a validar
/// </param>
/// <param name="strName">
///		Nombre del campo a validar
/// </param>
///
/////////////////////////////////////////////////////////////////////////////////////////////////////////////  

function valiDateFunction( strDate , strName )
{    
	var JLI_validateError1 = "Escriba una <b>fecha</b> valida con el formato día/mes/año ( dd/mm/aaaa ).";
	var JLI_validateError2 = "No ha escrito un <b>Año</b> válido en el campo <b>Fecha</b>" ;
	var JLI_validateError3 = "No ha escrito un <b>Mes</b> válido en el campo <b>Fecha</b>" ;
	var JLI_validateError4 = "No ha escrito un <b>Día</b> válido en el campo <b>Fecha</b>" ;
	var JLI_validateError5 = "No ha escrito ninguna <b>fecha</b>, insértela con el formato día/mes/año ( dd/mm/aaaa ).";
	
		
	// Se crea un string  
	var Fecha= new String( strDate )	
  
  	// Se comprueba si se recibe una cadena
  	if ( Fecha != undefined && Fecha.value != "" )
  	{
	        //Se comprueba el formato de la cadena
	        if ( !/^\d{2}\/\d{2}\/\d{4}$/.test(Fecha) )
	        {				
	            	// alert("formato de fecha no valido (dd/mm/aaaa)");
	            	return JLI_validateError1;
	    	}
	    	    
	    
			// Cadena Año  
			var Ano= new String( Fecha.substring( Fecha.lastIndexOf("/")+1,Fecha.length ) ); 
			// Cadena Mes  
			var Mes= new String( Fecha.substring( Fecha.indexOf("/")+1,Fecha.lastIndexOf("/") ) );  
			// Cadena Dia  
			var Dia= new String( Fecha.substring( 0,Fecha.indexOf("/") ) ); 
			
			// alert("Dia + Mes + Ano: " + Dia +"/"+ Mes +"/"+ Ano);
			
			
			// Valido el año  
			if ( isNaN( Ano ) || Ano.length < 4 || parseFloat( Ano ) < 1900 )
			{  
			    // alert('Ano invalido');  
				return JLI_validateError2;
			}  
			// Valido el Mes  
			if ( isNaN( Mes ) || parseFloat( Mes ) < 1 || parseFloat( Mes ) > 12 )
			{  
				 //alert(JLI_validateError3);  
				return JLI_validateError3;
			}  
			// Valido el Dia  
			if ( isNaN( Dia ) || parseInt( Dia, 10 ) < 1 || parseInt( Dia, 10 ) > 31 )
			{  
				// alert('Dia invalido');  
				return JLI_validateError4;
			}  
			
			
			
			if ( Mes == 4 || Mes == 6 || Mes == 9 || Mes == 11 ) 
			{  
				numDias=30;
			}
			else if ( Mes == 2  ) 
			{  
				if ( comprobarSiBisisesto( Ano ) )
					numDias=29;
				else
					numDias=28;
			} 
			else
				numDias=31; 
			
			 
			if ( Dia > numDias || Dia == 0 )
			{
				// alert('Dia invalido');  
				return JLI_validateError4;
	        }
	            	
	        // alert("FECHA CORRECTA");
	        return "";
      }				
	return JLI_validateError5;	
} 


//////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
/// <summary>
/// 	Funcion que Comprueba si un aÃ±o es bisiesto
/// </summary>
///
/////////////////////////////////////////////////////////////////////////////////////////////////////////////  

function comprobarSiBisisesto(anio)
{
	if ( ( anio % 100 != 0 ) && ( ( anio % 4 == 0 ) || ( anio % 400 == 0 ) ) ) 
  		return true;
	else 
  		return false;
}


//////////////////////////////////////////////////////////////////////////////////////////////////
///
/// <summary>
///   Función que controla un campo de tipo teléfono
///   idcampoTelefono: Identificador del campo de tipo teléfono (id).
///   bPermitirFijo: true si debe permitir números de teléfono fijo (8 y 9) y false si sólo es válido móviles.
///   idPpaisAsociado: en donde se permita teléfonos de varios países, el campo país del teléfono (id).
///   valorEspana: El valor que corresponde a España ya que en distintas pantallas tienen distinto valor.
///   longMaxima: valor que se tiene que poner al maxlength para otros países si está definido en la pantalla.
/// </summary>
///
//////////////////////////////////////////////////////////////////////////////////////////////////
function CampoTelefonoFunction(idcampoTelefono,bPermitirFijo, idPaisAsociado, valorEspana, longMaxima)
{
	var numero = ($("#" + idcampoTelefono).attr("value"));
	var lang = ($("#" + idcampoTelefono).attr("lang"));
	var aosCutStr = new Array();
	var descCampo = "";
	var textoMensajeError = "El campo <b>";
	var bTelefonoEspanol;
	var valorPaisAsociado = "";

	if (lang.indexOf("|") != -1) {
		aosCutStr = lang.split("|");
		descCampo = aosCutStr[0];
		//Si no tengo que controlar el país, no toco el lang
		if ((idPaisAsociado !=  undefined) && (idPaisAsociado != "")) {
			actualizarLangMovil(idcampoTelefono, bPermitirFijo, '', idPaisAsociado, valorEspana, longMaxima, 'true');
		}
	} else {
		descCampo = ($("#" + idcampoTelefono).attr("name"));
	}
	
	textoMensajeError = textoMensajeError + descCampo + "</b>";
	
	if (bPermitirFijo) {
		textoMensajeError = textoMensajeError + " debe comenzar por 6, 7, 8 ó 9.";
	} else {
		textoMensajeError = textoMensajeError + " debe comenzar por 6 ó 7.";
	}
	
	if ((idPaisAsociado ==  undefined) || (idPaisAsociado == "")) {
		bTelefonoEspanol = true; // Si no tengo que controlar país, indico que es español para controlar el número de teléfono.
	} else {
		valorPaisAsociado = ($("#" + idPaisAsociado).attr("value"));
		if (valorPaisAsociado == valorEspana) {
			bTelefonoEspanol = true;
		} else {
			bTelefonoEspanol = false;
		}
	}
	
	if (bTelefonoEspanol) {
		if (!esNumeroMovil(numero, bPermitirFijo) ) { 
			return textoMensajeError;
		} else {
			return "";
		}
	} else {
		return "";
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////
///
/// <summary>
///   Función que comprueba si un número empieza por un dígito permitido para teléfono móvil
///   actualmente es válido 6 y 7
///   En algunas pantallas también se permitían fijos, por lo que se permite el 8 y 9
///   para indicar donde se debe permitir fijos y donde no, se usa la variable booleana bPermitirFijo
///   Esta función es llamada por CampoTelefonoFunction
/// </summary>
///
//////////////////////////////////////////////////////////////////////////////////////////////////
function esNumeroMovil(numero,bPermitirFijo)
{
   if ((numero.substr(0, 1) == 6) || (numero.substr(0, 1) == 7) ) {
	   return true;
   } else if ((bPermitirFijo) && ((numero.substr(0, 1) == 8) || (numero.substr(0, 1) == 9))) {
	   return true;
   } else { 		
	   return false;
   }
}

//////////////////////////////////////////////////////////////////////////////////////////////////
///
/// <summary>
///   Función que pone los controles correctos al móvil para el país actual, debe estar definido antes de llamar 
///   en las páginas con país y teléfono se debe llamar en la carga de la pantalla y en el cambio del país.
/// </summary>
///
//////////////////////////////////////////////////////////////////////////////////////////////////
function actualizarLangMovil(idcampoTelefono, bPermitirFijo, strPonerOpcional, idPais, valorEspana, longMaxima, strNoBorrar)
{
	var lang = ($("#" + idcampoTelefono).attr("lang"));
	var campo = "#" + idcampoTelefono;
	var aosCutStr = new Array();
	var descCampo = "";
	var bEsOpcional;
	var atributoMovil = "";
	var valorPais = ($("#" + idPais).attr("value"));
	var sFuncion = "";

	if (lang.indexOf("|") != -1) {
		aosCutStr = lang.split("|");
		descCampo = aosCutStr[0];
		if ((strPonerOpcional ==  undefined) || (strPonerOpcional ==  "")) {
			if (aosCutStr.length > 3) {
				bEsOpcional = aosCutStr[3];
			}
		} else {
			bEsOpcional = (strPonerOpcional == "true");
		}
		
		if (aosCutStr.length > 4) {
			sFuncion = aosCutStr[4];
		}
		
		inicializarLangMovil(idcampoTelefono, descCampo, bPermitirFijo, bEsOpcional, sFuncion, idPais, valorEspana, longMaxima, strNoBorrar);
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////
///
/// <summary>
///   Función que pone los controles correctos al móvil iniciales cuando no se puede poner en la creación del campo. 
///   puede ser usado en páginas con país o sin él, se debe llamar en la carga de la pantalla.
/// </summary>
///     
//////////////////////////////////////////////////////////////////////////////////////////////////
function inicializarLangMovil(idcampoTelefono, descCampo, bPermitirFijo, bEsOpcional, sFuncion, idPais, valorEspana, longMaxima, strNoBorrar)
{
	var campoTelefono = "#" + idcampoTelefono;
	var atributoMovil = "";
	var bTelefonoEspanol;
	
	if ((sFuncion ==  undefined) || (sFuncion ==  "")) {
		sFuncion = "CampoTelefonoFunction('"+ idcampoTelefono + "', " + bPermitirFijo;
		if ((idPais ==  undefined) || (idPais == "")) {
			sFuncion = sFuncion + ")";
		} else {
			sFuncion = sFuncion + ", '" + idPais + "', '" + valorEspana + "'";
			if ((longMaxima ==  undefined) || (longMaxima == "")) {
				sFuncion = sFuncion + ")";
			} else {
				sFuncion = sFuncion + ", '" + longMaxima +  "')";
			}
		}
	}
	
	var nLongitudActual = ($(campoTelefono).attr("maxlength"));
	var nLongitudFinal;
	var bNoBorrar;
	if ((idPais ==  undefined) || (idPais == "")) {
		bTelefonoEspanol = true; // Si no tengo que controlar país, indico que es español para controlar el número de dígitos.
		nLongitudFinal = "9";
	} else {
		valorPaisAsociado = ($("#" + idPais).attr("value"));
		if (valorPaisAsociado == valorEspana) {
			bTelefonoEspanol = true;
			nLongitudFinal = "9";
		} else {
			bTelefonoEspanol = false;
			//Como no es español no tiene que estar limitado a 9 dígitos
			if ((longMaxima ==  undefined) || (longMaxima ==  "")) {
				nLongitudFinal = "";
			} else {
				nLongitudFinal = longMaxima;
			}
		}
	}
	
	if (bTelefonoEspanol) {
		//Como es español sí tiene que estar limitado a 9 dígitos
		atributoMovil =  descCampo + "|Number|9|"; 
	} else {
		//Como no es español no tiene que estar limitado a 9 dígitos
		atributoMovil = descCampo + "|Number||";
	}
	atributoMovil = atributoMovil + bEsOpcional + "|" + sFuncion + "|";
	$(campoTelefono).attr("lang", atributoMovil);
	
	if ((nLongitudFinal ==  "") || (nLongitudFinal <= 0)) {
		$(campoTelefono).removeAttr("maxlength");
	} else {
		$(campoTelefono).attr("maxlength", nLongitudFinal);
	}
	
	//En Explorer no funciona le removeAttr, se queda a 0 y no deja introducir ningún dígito
	//inicialmente, las páginas sin maxlength en Explorer tienen el valor 2147482647
	var long = ($(campoTelefono).attr("maxlength"));
	if (long == 0) {
		$(campoTelefono).attr("maxlength", "2147483647");
	}
	
	if ((strNoBorrar ==  undefined) || (strNoBorrar ==  "")) {
		bNoBorrar = false;
	} else {
		bNoBorrar = (strNoBorrar == 'true');
	}
	
	if ((nLongitudActual != nLongitudFinal) && (!bNoBorrar)) {
		//El tamaño máximo ha cambiado, por lo tanto han cambiado el combo, borrar el número de teléfono si no estoy ya en la validación
		$(campoTelefono).attr("value", "");
	}
	
}

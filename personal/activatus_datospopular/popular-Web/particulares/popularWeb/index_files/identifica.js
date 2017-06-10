function im_chkNumeric(p_chkText){
	return (p_chkText.match(/^(\d*)$/g)!=null);
}
function t_acceso(formulario,p_pa){
	if (p_pa=="b"){	
		if (formulario.tipo != undefined){	
			switch (formulario.tipo.selectedIndex){		
				case 0: return "UP";
				case 1:	return "DE";
			}
		}else{
			return "UP";
		}
	}else if (p_pa=="f"){
		return "UN";
	}else {
		switch (formulario.tipo.selectedIndex){		
			case 0: return "UN";
			case 1:	return "TJ";
			case 2:	return "DE";
		}
	}
}
function comprueba(){}

function ctrl_in(p_acc,formu){
	$("#warning").css("display", "none");
	
	if (formu.PAN_IN.value == ""){
		if (p_acc=="UN"||p_acc=="UP"||p_acc=="DE"){
			
				/* Se muestra la caja de errores */
				$("#warning .text").html(JLI_txt10);
				$("#warning").css( "display", "block" );
				
				/* Nos posicionamos al inicio de la página */
				self.scroll(1,1);				
				
			}
		if (p_acc=="TJ"||p_acc=="TP"){
				/* Se muestra la caja de errores */
				$("#warning .text").html(JLI_txt12);
				$("#warning").css( "display", "block" );
				
				/* Nos posicionamos al inicio de la página */
				self.scroll(1,1);
			}		
		formu.PAN_IN.focus();
		return false;
	}
	if (formu.contras_IN.value == ""){		
		if (p_acc=="UN"||p_acc=="UP"||p_acc=="DE"){
				/* Se muestra la caja de errores */
				$("#warning .text").html(JLI_txt11);
				$("#warning").css( "display", "block" );
				
				/* Nos posicionamos al inicio de la página */
				self.scroll(1,1);
			}
		if (p_acc=="TJ"||p_acc=="TP"){
				/* Se muestra la caja de errores */
				$("#warning .text").html(JLI_txt13);
				$("#warning").css( "display", "block" );
				
				/* Nos posicionamos al inicio de la página */
				self.scroll(1,1);
			}		
		formu.contras_IN.focus();
		return false;
	}
   if (p_acc=="TJ"||p_acc=="TP"){
	   
		if ((!im_chkNumeric(formu.PAN_IN.value)) || (formu.PAN_IN.value.length!=16))
			{/* Se muestra la caja de errores */
			 $("#warning .text").html(JLI_txt3);
			 $("#warning").css( "display", "block" );
			 self.scroll(1,1);
			 formu.PAN_IN.focus();
			 return false;
			}
	}
	return true;
}
function fchgID(idioma){
	var l_form=document.identifica;
	var l_loc=new String(top.document.location);
	var patron=new RegExp("p_id="+get_idi(),"gi");
	top.location.href=l_loc.replace(patron,"p_id="+idioma);
}
function Pulsado(){
	fTest();
	return false;
}
function Pulsado2(){
	if(document.identifica.boton.value==JLI_txtboton) Pulsado();
}
function f_Intro(evnt)
{var l_code=(document.layers)?evnt.which:evnt;
 if (l_code==13) Pulsado2();
}
function fTest(){
	if (isCookiesEnabled())
		document.identifica.action = "/Bpemotor/?id=login";
	else
		document.identifica.action = "/esp/gbp/htm/salida.htm?codigoerror=615_A&opt=" + optMultientidad;

	if(saveDatas()==false)
		fLink(0);
	else
		fLink(1);
}
function fLink(p_modo){
	if(p_modo==0)
		document.identifica.boton.value=JLI_txtboton;
	else
		document.identifica.boton.value=JLI_cone;
}

function AbrirDemo(){
	document.identifica.PAN_IN.value="1111111111111111";
	document.identifica.contras_IN.value="1111";
	document.identifica.tipo.selectedIndex = 1;
	document.identifica.GL_pf.value="P";
	document.identifica.tipoPerfil.value="P";
	Pulsado2();
}

function cambia_texto(){
	var formu= document.identifica;
	var objpass = document.getElementById("pass");
	var objusu = document.getElementById("usu");
	formu.PAN_IN.focus();
	objpass.innerHTML='';
	objusu.innerHTML='';
	switch (formu.tipo.selectedIndex){
		case 1:{objpass.innerHTML=JLI_txt9;
			objusu.innerHTML=JLI_txt8;
			return false;}
		case 0:{objpass.innerHTML=JLI_txt7;
			objusu.innerHTML=JLI_txt6;
			return false;}
	}
}

function isCookiesEnabled()
{
	/* s ecomprueba el método directo de javascript*/
	var fReturn = (navigator.cookieEnabled) ? true : false;

	/* si falla se prueba  acrear la cookie, leerla y borrarla */
	if (typeof navigator.cookieEnabled == "undefined" && !cookieEnabled)
	{ 
		createTestCookie();
	    if (readTestCookie() != null) 
	    {
	    	fReturn = true;
	    	eraseTestCookie();
	    }
	}
	return fReturn;
}

function createTestCookie() 
{
	$.cookie("loginCookie", "1", {path: "/", expires: 1});

}
function eraseTestCookie(name) 
{
	document.cookie = "loginCookie=0;expires=-1;path=/";
}

function readTestCookie() 
{
	var nameEQ = "loginCookie=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) 
	{
		var c = ca[i];
		while (c.charAt(0) == ' ') 
			c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) 
			return c.substring(nameEQ.length + 1, c.length);
	}
	return null;
}




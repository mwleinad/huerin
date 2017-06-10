/* 
	JS Document 
	------------------------------------------------------------------------------------------------------
	------------------------------------------------------------------------------------------------------
		
	JS encargado de crear los submenus que aparecen en la cabecera de la aplicacion 
	
	------------------------------------------------------------------------------------------------------
*/	


	var ActiveSubMenu = null;									
	var version = getInternetExplorerVersion();					
	
	var multientidad = null; 	// 26-05-2011: CJD1531H - Multientidad
					
	var country  = "a"; // Por defecto
				
	function getInternetExplorerVersion() 
	{
	    var rv = -1; 
	
	    if (navigator.appName == 'Microsoft Internet Explorer') 
	      {     var ua = navigator.userAgent;
	            var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
	            if (re.exec(ua) != null)
		            rv = parseFloat(RegExp.$1);
	      }
	 return rv;				
	}
	
	function WindowResize()
	{
		for( var nCont=0; nCont< menu.tab.length; nCont++ )
		{	

			if( $.browser.msie && version == "7" || version == "8" )subPosition = parseInt(menu.tab[nCont].subposition);
			else subPosition = parseInt(menu.tab[nCont].subposition) + $("#pagina").attr("offsetLeft");	
				
			eval("$(\"#submenu_" + menu.tab[nCont].id + "\").css(\"left\",\"" + subPosition + "px\");");
			if( menu.tab[nCont].id == ActiveSubMenu && typeof document.body.style.maxHeight === "undefined" )
				$("#hideDivIframeSubmenus, #overlayCapSubmenus").css("left", subPosition );
		}

		alertLeft = $("#pagina").attr("offsetLeft") + parseInt(716);
			eval("$(\"#alertas\").css(\"left\",\"" + alertLeft + "px\");");
		WindowScroll();
	}
	
    function WindowScroll()
    {     
          if (parent.frames["cabecera"]!=null)
             parent.frames["cabecera"].scroll( $(document).scrollLeft(), 0);
 

          if ( jQuery.browser.version == "7.0" )                   
              offsetTop = document.documentElement.scrollTop;
          else
              offsetTop = $(document).scrollTop();

          offsetLeft = $(document).scrollLeft();         
          $("#submenus").css("top", offsetTop);    
          if( typeof document.body.style.maxHeight === "undefined" )
                $("#hideDivIframeSubmenus, #overlayCapSubmenus").css("top", offsetTop );                  
    }



	function SubmenuMake( id )
	{
		if( ActiveSubMenu == id)
		{
			eval("$(\"#submenu_" + id + "\").hide();");
			ActiveSubMenu = null;
			coverRemove();
		}
		else
		{		
			if( ActiveSubMenu != null )	eval("$(\"#submenu_" + ActiveSubMenu + "\").hide();");
			if (typeof document.body.style.maxHeight === "undefined") 
			{
				if (document.getElementById("hideDivIframeSubmenus") === null) $("<iframe src='javascript:false' id='hideDivIframeSubmenus'></iframe><div id='overlayCapSubmenus' />").insertBefore("#submenus");				
			
				$("#hideDivIframeSubmenus, #overlayCapSubmenus").css( "height", parseInt( $("#submenu_" + id).height(), 10 ) );
				$("#hideDivIframeSubmenus, #overlayCapSubmenus").css( "width", parseInt( $("#submenu_" + id).width(), 10 ) );
				$("#hideDivIframeSubmenus, #overlayCapSubmenus").css("top", $(document).scrollTop() );			
				$("#hideDivIframeSubmenus, #overlayCapSubmenus").css("left", parseInt( $("#submenu_" + id).css("left"), 10 ) + $(document).scrollLeft() );		
			}

			eval("$(\"#submenu_" + id + "\").show();");
			eval("$(\"#submenu_" + id + "\").css(\"visibility\",\"visible\");");
			ActiveSubMenu = id;
		}
	}
	

	function coverRemove(){	$("#hideDivIframeSubmenus, #overlayCapSubmenus").remove();}
 

	/////////////////////////////////////////////////////////////////////////////
	///
	/// <summary>
	///		Función que centraliza el control de abandono
	/// </summary>
	/// <remarks>
	///		Comprueba si la pagina requiere control de abandono.
	/// </remarks>
	/// <param name="nameFunction">
	///		Función de navegación, a ejecutar despues del control
	/// </param>
	///
	///////////////////////////////////////////////////////////////////////////// 

	function  optContinue( nameFunction )
	{
		var objControl  		= parent.frames["cabecera"].opExitControl,			// Cargamos en memoria Obj Json.			
			aosId 				= ( typeof idItem == "undefined" || idItem == null ) 
								? aosId = new Array() : aosId = idItem.split("_"), 	// Convertimos en Array el idItem.
			flagEndTransaction 	= false;											// Flag para control de pagina de Ok y Ko
		
		var iditemOperIda="arp_clv_ccl_000_000_000";		
		/* Se almacena de manera global el arbol con las páginas a controlar */
		jsonIdGlobal = objControl;		
		/*Si existe el idItem se comprueba si es la pagina de Ok o KO para saltar el control de abandono*/	
		
		/*tema del usupanpin*/
		if(parent.InputValue('mainContainer', 'numUsuario', 'datos', false).substring(7) == 'TJ')
		{		
			//evitar cualquier accion sobre menus que no lleve iditem
			if(nameFunction.split(",")[1]!=undefined)
				  iditemOperIda=nameFunction.split(",")[1].replace(/'/g,"").replace(/\)/g,"").replace(/ /g,"");
			else
				 iditemOperIda="siniditem";
		}
		
		if((parent.InputValue('mainContainer', 'numUsuario', 'datos', false).substring(7) == 'TJ')&& (parent.getitempinpan(iditemOperIda)))
		{	
			//si accedemos desde mis claves menu directamente 
			if(iditemOperIda=="arp_clv_ccl_000_000_000")
				location.href="/AppGBP/esp/gbp/jsp/53_298_pio2.jsp?idItem=arp_clv_cfi_000_000_000&idioma=esp&p_ent=pa&vcab=F&soli=C";
			//location.href="/AppGBP/esp/gbp/jsp/olvclaves.jsp?idItem="+iditemOperIda+"&idioma=" + parent.InputValue('mainContainer', 'idioma', 'datos', false) + "&par_ent=" + parent.InputValue('mainContainer', 'userType', 'datos', false) + "&par_ac=" + parent.InputValue('mainContainer', 'ac', 'datos', false) + "&par_pa=" + parent.InputValue('mainContainer', 'country', 'datos', false)+ "&vcab=F&par_sd=" + parent.InputValue('mainContainer', '_SD', 'datos', false)+ "&par_fecha=" + parent.InputValue('mainContainer', 'fechaHoyAnno', 'datos', false);			
			else if(parent.panpin("9444",iditemOperIda,0))
				location.href="/Bpemotor/?_SD="+parent.InputValue('mainContainer', '_SD', 'datos', false)+"&_ABT_FROM_PART=jspError&codOperacion=009444&idItem="+iditemOperIda+"&litError=Operacion solo accesible con claves de usuario de Banca Multicanal&codError=9444";
			else
				eval(nameFunction);
		}
		else{//caso no usuario TJ para aband-oper.
		
		if ( !(typeof idItem == "undefined" || idItem == null) )
		{		
			/* Se revisa que la pagina NO sea la de Ok ni la de KO ( ya que estas nos se controlan ) */
			if (  (idItem.search("OK") != -1) || (idItem.search("KO") != -1) )
				flagEndTransaction = true;	 
			
			/* Se comprueba que sea una operativa controlada y que no sea la pagina de OK o KO */
			if ( ( validateOper( aosId,jsonIdGlobal ) )  &&	flagEndTransaction == false )					
			{					
				/* Se modifica la cadena de los thickbox para evitar cortes de url */
				nameFunction = nameFunction.replace("TB_iframe","TBiframe");			
				
				/* Link del thickbox a abrir */
				if ((idItem.search("cnf") == -1))
					var	link ="/esp/gbp/htm/abortShowWindow.html?"+
							  "nameFunction="  + escape(nameFunction) +									
							  "&TB_iframe=true&height=200&width=650";
				/* Páginas _cnf par mensaje pendiente de firma */
				else{
					
					var	link ="/esp/gbp/htm/mensPendFirmaTB1.html?"+
							  "nameFunction="  + escape(nameFunction) +
							  "&TB_iframe=true&height=230&width=650";
				}
				
				/* Si tenemos cargada la librería (thickBox.js) */
				if(tb_show)
				{					
					
					/* Si no existe el thickbox, se abre, en caso contrario solo se redirige */
					if ( document.getElementById("TB_window") == null )
						tb_show(null, link, false);	
					else
					{
						/* Se modifica la url xra identificar si se accede desde un thickbox */
						link ="/esp/gbp/htm/abortShowWindow.html?"+
						"nameFunction="  + escape(nameFunction) +									
						"&isThickB=true&TB_iframe=true&height=200&width=650";							
						
						/* Se recoge el name del iframe del thicbox ya que cambia cada vez que se abre */
						var objName = document.getElementById("TB_iframeContent");			

						/* Se realiza la redireccion dentro del thickbox  */
						top.frames["datos"].frames[objName.name].location.href=link;	
					}
				}
				/* Cargamos la librería */
				else
				{				
					/* Se carga el archivo de thickbox que no existe */	
					var optConinueType 	= document.createElement('script'),
						head 			= document.getElementsByTagName('head').item(0);
					
					optConinueType.type = 'text/javascript';
					optConinueType.src = '/esp/gbp/src/thickbox.js';	
					head.appendChild(optConinueType);
					
					/* Se abre el thickbox */
					tb_show(null, link, false); 	
				}			
			}
			/* Hacemos la redirección ya que no es una página controlada o es la página de OK o KO*/
			else
			{					
				/* Se realiza la redirección */				
				eval(nameFunction);
			}
			

			
			
		}
		/* Si la operativa todavía no tiene asignado idItem seguimos con la ejecución  */
		else		
		{
			/* Se ejecuta la función recibida */
			eval(nameFunction);
		}
	   }//cierre usuario TJ
		
	}

	
	/////////////////////////////////////////////////////////////////////////////
	///
	/// <summary>
	///		Función que valida los idItem, para saber si hay que controlar la página
	/// </summary>
	/// <remarks>
	///		Funcion recursiva que recorre los bloques del idItem comprobando su existencia en el json.
	///		Cada llamada se hace pasando un nodo menos del arbol */
	/// </remarks>
	/// <param name="vBloquesIdItem">
	///		Vector con los bloques el idItem de la página actual
	/// </param>
	/// <param name="node">
	///		Arbol del json, con las claves de las páginas a controlar
	/// </param>	
	/// <returns>
	/// 	True: Página a controlar
	///		False: Página que no lleva control
	/// </returns>
	///////////////////////////////////////////////////////////////////////////// 
	
	function validateOper ( vBloquesIdItem , node )
	{
		var auxId;		
		
		/* Paginas distributivas */
		if ( typeof node[vBloquesIdItem[0]] == "object" && typeof node[vBloquesIdItem[0]].show != "undefined" && vBloquesIdItem[1]=="000")			
			return false;							
		/* Fin idItem */
		else if ( vBloquesIdItem[0]=="000" )
			return true;
		/* No existe la bloque del idItem */
		else if (typeof node[vBloquesIdItem[0]] == "undefined")
			return false;
		else
		{				
			/* Se almacena el nuevo arbol, eliminando el nodo superior */
			auxId   = node[vBloquesIdItem[0]];	
			
			/* Se almacena el nuevo arbol, eliminando el nodo superior */
			return validateOper(vBloquesIdItem.splice(1,vBloquesIdItem.length),auxId);
		}
	}
	
	/////////////////////////////////////////////////////////////////////////////
	///
	/// <summary>
	///		Función que redirecciona desde el menu lateral
	/// </summary>
	/// <param name="locationUrl">
	///		Dirección
	/// </param>
	/// <param name="idItem">
	///		idItem
	/// </param>
	///////////////////////////////////////////////////////////////////////////// 
	
	function menusRedirection ( locationUrl , idItem )
	{
		var topOption 		= arguments[2] || false;
		
		/* Se realiza la redirección */
		top.frames['datos'].MakeLocation( locationUrl , idItem , topOption );
		
		/* Se elimina el contenedor de los datos si esta creado. */
		parent.ContainerRemove();
	}
	
	/////////////////////////////////////////////////////////////////////////////
	///
	/// <summary>
	///		Función que redirecciona desde el menu horizontal
	/// </summary>
	/// <param name="locationUrl">
	///		Dirección
	/// </param>
	/// <param name="idItem">
	///		idItem
	/// </param>
	///////////////////////////////////////////////////////////////////////////// 
	
	function HorizontalMenusRedirection ( locationUrl , idItem )
	{
		
		if ( locationUrl.search("_ABT_FROM_PART=broker") != -1 ){
			urlLaunchFunction(locationUrl,'SimBroker');
		}else if( locationUrl.search("bancopopular.pt") != -1 ){	
				urlLaunchFunction(locationUrl,'bancopopular.pt');				
		}else{
			
			/* Se realiza la redirección */				
			top.frames['datos'].MakeLocation( locationUrl , idItem , true );
			
			/*Se oculta la capa*/
			$("#submenu_" + idItem).hide(); 	
			
			/* Se elimina el contenedor de los datos si esta creado. */
			parent.ContainerRemove();		
		}
		
		
	}
	
	/////////////////////////////////////////////////////////////////////////////
	///
	/// <summary>
	///		Función que redirecciona desde un thickbox
	/// </summary>
	/// <param name="linkUrl">
	///		Dirección
	/// </param>
	///
	///////////////////////////////////////////////////////////////////////////// 
	
	function linksThickboxRedirection ( linkUrl )
	{
		/* Se modifica la url, para dejarla en el formato original */
		linkUrl = linkUrl.replace("TBiframe","TB_iframe");
		
	
		
		/* Si no existe el thickbox, se abre, en caso contrario solo se redirige */
		if ( document.getElementById("TB_window") == null )
			tb_show(null, linkUrl, false);
		else
		{
			/* Se recoge el name del iframe del thicbox ya que cambia cada vez que se abre */
			var objName = document.getElementById("TB_iframeContent");			

			/* Se realiza la redireccion dentro del thickbox  */
			top.frames["datos"].frames[objName.name].location.href=linkUrl;	
		}
	}
	
	function MakeLocation( url, id, topOption )
	{
		if( topOption )
			Headframe.ActiveMenuTab(id);
		if (url.search("_ABT_FROM_PART=broker") != -1 ){
			urlLaunchFunction(url,'SimBroker');
		}else if( url.search("bancopopular.pt") != -1 ){
			
			urlLaunchFunction(url,'bancopopular.pt');
			
		}	
		else
		{
			if( url.indexOf("Function") != -1 )	    eval( url );
			else if( url.indexOf("idItem") != -1 )	document.location.href = url;
			else	document.location.href = url + "?idItem=" + id;
		}
	}
	

	function SubmenuMakeLocation( url, id )
	{
		Headframe.ActiveMenuTab(id);		
		
		if( url.indexOf("Function") != -1 )	eval( url );
		else if( url.indexOf("idItem") != -1 )
			document.location.href = url;
		else
			document.location.href = url + "?idItem=" + id;	
	}
	
	/* Se modifica la funcion eliminando los espacios en blanco y los puntos debido a problemas con el IE8 - MRA5391r */
	function urlLaunchFunction(link,name)
	{
		name = name.replace(/[ ]/g, "");
		name = name.replace(/[.]/g, "");

		if(arguments.length < 1)name = 'new';{
		
			window.open(link, name, 'width=800,height=600,screenX=0,screenY=0,top=0,left=0,status=yes,resizable=yes,toolbar=yes,scrollbars=yes,location=yes,menubar=yes');
		}
	}


	$(document).ready(function()
	{
		(function()
		{	
			// 26-05-2011: CJD1531H - Multientidad
			if( typeof parent.menu != "undefined" 
				&& parent.gbp_mult != "undefined" 
				&& parent.menu.tab[ parent.menu.tab.length-1].subposition != "" )
			{	
			
				menu = parent.menu;
				var online = parent.InputValue('mainContainer', 'online', 'cabecera', false) == "1" ? true : false; // Valor de online.

				// ----------------------------------------------------------------
				// 26-05-2011: CJD1531H - Multientidad
				// ----------------------------------------------------------------
				
				var gbp_mult = parent.gbp_mult;
				
				// Por defecto
				var userTypeSub = "pa";
				
				if (parent.InputValue('mainContainer', 'userType', 'cabecera', false) != null )
					userTypeSub = parent.InputValue('mainContainer', 'userType', 'cabecera', false);		
												
				if (parent.InputValue('mainContainer', 'country', 'datos', false) != null )	
					country  = parent.InputValue('mainContainer', 'country', 'datos', false);			
				
				var head 	 = document.getElementsByTagName('head').item(0);
								
				multientidad = "" + userTypeSub + "_" + country;
				
				// Añadimos el CSS de la multientidad
				var cssMult = document.createElement('link');	
	     		
				cssMult.href = "/esp/gbp/css/gbp-mult-" + userTypeSub + "_" + country + ".css";
				cssMult.type = "text/css";
				cssMult.rel = "stylesheet";	
				
				head.appendChild(cssMult);
				
				
				var UseroperaPP 	= document.createElement('script');
				UseroperaPP.type = 'text/javascript';
				UseroperaPP.src = '/esp/gbp/src/gestionOperPP.js';	
			    head.appendChild(UseroperaPP);
				
				// ----------------------------------------------------------------
				// FIN
				// ----------------------------------------------------------------						
				
				$("#pagina").before('<div id="submenus" />');
				for( var nCont = 0; nCont< menu.tab.length; nCont++ )
				{										
					$('#submenus').append('<div id="submenu_' + menu.tab[nCont].id + '" />');

					eval("$(\"#submenu_" + menu.tab[nCont].id + "\").css(\"position\",\"absolute\");");
					eval("$(\"#submenu_" + menu.tab[nCont].id + "\").css(\"width\",\"204px\");");
					eval("$(\"#submenu_" + menu.tab[nCont].id + "\").css(\"cursor\",\"pointer\");");
					eval("$(\"#submenu_" + menu.tab[nCont].id + "\").css(\"visibility\",\"hidden\");");
					eval("$(\"#submenu_" + menu.tab[nCont].id + "\").css(\"z-index\",\"100\");");

						
					if( $.browser.msie &&  version == "7" || version == "8" )
						subPosition = parseInt(menu.tab[nCont].subposition);
					else
						subPosition = parseInt(menu.tab[nCont].subposition) + $("#pagina").attr("offsetLeft");		
					
					eval("$(\"#submenu_" + menu.tab[nCont].id + "\").css(\"left\",\"" + subPosition + "px\");");

					eval("$(\"#submenu_" + menu.tab[nCont].id + "\").append('<ul id=\"submenu-ul_" + menu.tab[nCont].id + "\" />');");

					for( var nSubCont = 0; nSubCont<menu.tab[nCont].level.length; nSubCont++ )
					{			

						var submenuPrint = true;

						if( !online )if( menu.tab[nCont].level[nSubCont].agentFile == "false" ) submenuPrint = false;

						if( submenuPrint )
						{
							var srtLocation = "dfdf";
							var strId = "fdfd";
							
							eval("$(\"#submenu-ul_" + menu.tab[nCont].id + "\").append('<li id=\"submenu-li_" + menu.tab[nCont].level[nSubCont].id + "\" />');");
							eval("$(\"#submenu-li_" + menu.tab[nCont].level[nSubCont].id + "\").append('<a id=\"submenu-a_" + menu.tab[nCont].level[nSubCont].id + "\" href=\"#\">" + menu.tab[nCont].level[nSubCont].name + "</a>');"); //
							eval("$(\"#submenu-a_" + menu.tab[nCont].level[nSubCont].id + "\").hover(function() { $(this).css(\"text-decoration\",\"underline\"); }, function() { $(this).css(\"text-decoration\",\"none\");} );"); 													
							
							//eval("$(\"#submenu-a_" + menu.tab[nCont].level[nSubCont].id + "\").click( function(){ SubmenuMakeLocation( \"" + menu.tab[nCont].level[nSubCont].location + "\", \"" + menu.tab[nCont].level[nSubCont].id + "\" );  return false; })");
							//eval("$(\"#submenu-a_" + menu.tab[nCont].level[nSubCont].id + "\").click( function(){ optContinue( \"" + menu.tab[nCont].level[nSubCont].location + "\" );  })");
							eval("$(\"#submenu-a_" + menu.tab[nCont].level[nSubCont].id + "\").click(function(){top.frames['datos'].optContinue(\"top.frames['datos'].HorizontalMenusRedirection( '" + menu.tab[nCont].level[nSubCont].location + "', '" + menu.tab[nCont].level[nSubCont].id  + "' )\");} )");
							
							//$("#submenu-a_" + menu.tab[nCont].level[nSubCont].id ).click( top.frames['datos'].optContinue("dfdfd") );
							
							
							if( nSubCont != menu.tab[nCont].level.length-1 )eval("$(\"#submenu-li_" + menu.tab[nCont].level[nSubCont].id + "\").append('<div id=\"submenuSeparator\" style=\"background-color:#FFFFFF;min_height:1px;overflow:hidden;border:0px solid #FFFFFF;width:180px;margin-left:7px\" ></div>');");	
							if( nSubCont == menu.tab[nCont].level.length )$("#submenu-li_" + menu.tab[nCont].level[nSubCont].id ).attr("style","padding-bottom: 0;");
						}
					}
					
					// ----------------------------------------------------------------
					// 26-05-2011: CJD1531H - Multientidad
					// ----------------------------------------------------------------
					eval("$(\"#submenu_" + menu.tab[nCont].id + "\").append('<div id=\"submenuFooter\"><img src=\"/esp/gbp/bin/img/submenuBackgroundFooter-" + multientidad + ".gif\" /></div>');");
					// ----------------------------------------------------------------
					// FIN
					// ----------------------------------------------------------------	
				}
				
				$("#submenus li").addClass( "submenus-li-" + multientidad );		// 26-05-2011: CJD1531H - Multientidad
				$(document).click( function(){ SubmenuMake( ActiveSubMenu ); });
				$(window).resize( function() { WindowResize() } );
				$(window).scroll(function() { WindowScroll() } );
			}
			else
			{
				setTimeout( arguments.callee, 10 );
			}
		}());
	});
	
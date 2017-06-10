/*
 * Thickbox 3.1 - One Box To Rule Them All.
 * By Cody Lindley (http://www.codylindley.com)
 * Copyright (c) 2007 cody lindley
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
*/
var tb_pathToImage = "/esp/gbp/bin/img/loadingAnimation.gif";
$(document).ready(function(){  
	tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
	imgLoader = new Image();// preload image
	imgLoader.src = tb_pathToImage;
});


function tb_init(domChunk){
	$(domChunk).click(function(){
	var t = this.title || this.name || null;
	var a = this.href || this.alt;
	var g = this.rel || false;
	tb_show(t,a,g);
	this.blur();
	return false;
	});
}

function tb_reinit(domChunk) {
	$(domChunk).unbind("click");
	tb_init(domChunk);
}

function FindParameterTH(strUrl, strParameter)
{	var strParameters;		// Cadena con los parámetros.
	var astrParameters;		// Array con los parámetros.
	var astrFieldValue;		// Campo y valor en curso.
	var nParameter;			// Contador.
	strParameters = strUrl.substring(strUrl.indexOf('?') + 1, strUrl.length);
	astrParameters = strParameters.split('&');
	for ( nParameter = 0; nParameter < astrParameters.length; nParameter++ )
	{	astrFieldValue = astrParameters[nParameter].split('=');
		if ( astrFieldValue[0] == strParameter )	return astrFieldValue[1];
	}
	return "";
}
function getInternetExplorerVersion() 
{    var rv = -1; 
     if (navigator.appName == 'Microsoft Internet Explorer') 
	 {     var ua = navigator.userAgent;
	        var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null) rv = parseFloat(RegExp.$1);    
	 }
	 return rv;
}
var _ie78 = false;
var ver = getInternetExplorerVersion();
if (ver > -1) _ie78=((ver==7.0)||(ver==8.0));
function tb_show(caption, url, imageGroup){
	var JLI_cerrar = "Cerrar";
	try{
//		JLI_cerrar =(parent.InputValue('mainContainer', 'country', 'datos', false) == 'b') ? "Fechar" : "Cerrar";
		JLI_cerrar = (parent.InputValue('mainContainer','id','datos',false) == 'por') ? "Fechar" : JLI_Cerrar;
	}catch (e){
		try{
//			JLI_cerrar = (FindParameterTH(location.href, "p_pa") == 'b') ? "Fechar" : "Cerrar";
			JLI_cerrar = ( FindParameterTH(location.href, "p_id") == 'por') ? "Fechar" : JLI_Cerrar;
		}catch (e) {
			JLI_cerrar = "Cerrar";
		}			
	}	
	try {if (typeof document.body.style.maxHeight === "undefined") {//if IE 6
			$("body", "html").css({height: "100%",width: "100%"});
			$("html").css("overflow", "hidden");
			if (document.getElementById("TB_HideSelect") === null) {//iframe to hide select elements in ie6
				$("body").append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div><div id='TB_window'></div>");
				$("#TB_overlay").click(tb_remove);
			}
		}
		else {//all others
			if (document.getElementById("TB_overlay") === null) {
				$("body").append("<div id='TB_overlay'></div><div id='TB_window'></div>");
				$("#TB_overlay").click(tb_remove);
			}
		}
		if (tb_detectMacXFF()) 
			$("#TB_overlay").addClass("TB_overlayMacFFBGHack");
		else 
			$("#TB_overlay").addClass("TB_overlayBG");
		if (caption === null) {caption = "";}
		$("body").append("<div id='TB_load'><img src='" + imgLoader.src + "' /></div>");//add loader to the page
		$('#TB_load').show();//show loader
		var baseURL =(url.indexOf("?") !== -1)?url.substr(0, url.indexOf("?")):url;
		var urlString = /\.jpg$|\.jpeg$|\.png$|\.gif$|\.bmp$/;
		var urlType = baseURL.toLowerCase().match(urlString);
		if (urlType == '.jpg' || urlType == '.jpeg' || urlType == '.png' || urlType == '.gif' || urlType == '.bmp') {
			TB_PrevCaption = "";
			TB_PrevURL = "";
			TB_PrevHTML = "";
			TB_NextCaption = "";
			TB_NextURL = "";
			TB_NextHTML = "";
			TB_imageCount = "";
			TB_FoundURL = false;
			if (imageGroup) {
				TB_TempArray = $("a[@rel=" + imageGroup + "]").get();
				for (TB_Counter = 0; ((TB_Counter < TB_TempArray.length) && (TB_NextHTML === "")); TB_Counter++) {
					var urlTypeTemp = TB_TempArray[TB_Counter].href.toLowerCase().match(urlString);
					if (!(TB_TempArray[TB_Counter].href == url)) {
						if (TB_FoundURL) {
							TB_NextCaption = TB_TempArray[TB_Counter].title;
							TB_NextURL = TB_TempArray[TB_Counter].href;
							TB_NextHTML = "<span id='TB_next'>&nbsp;&nbsp;<a href='#'>Next &gt;</a></span>";
						}
						else {
							TB_PrevCaption = TB_TempArray[TB_Counter].title;
							TB_PrevURL = TB_TempArray[TB_Counter].href;
							TB_PrevHTML = "<span id='TB_prev'>&nbsp;&nbsp;<a href='#'>&lt; Prev</a></span>";
						}
					}
					else {
						TB_FoundURL = true;
						TB_imageCount = "Image " + (TB_Counter + 1) + " of " + (TB_TempArray.length);
					}
				}
			}
			imgPreloader = new Image();
			imgPreloader.onload = function(){
				imgPreloader.onload = null;
				var pagesize = tb_getPageSize();
				var x = pagesize[0] - 150;
				var y = pagesize[1] - 150;
				var imageWidth = imgPreloader.width;
				var imageHeight = imgPreloader.height;
				if (imageWidth > x) {
					imageHeight = imageHeight * (x / imageWidth);
					imageWidth = x;
					if (imageHeight > y) {
						imageWidth = imageWidth * (y / imageHeight);
						imageHeight = y;
					}
				}
				else 
					if (imageHeight > y) {
						imageWidth = imageWidth * (y / imageHeight);
						imageHeight = y;
						if (imageWidth > x) {
							imageHeight = imageHeight * (x / imageWidth);
							imageWidth = x;
						}
					}
				
				TB_WIDTH = imageWidth + 30;
				TB_HEIGHT = imageHeight + 60;
				if ( screen.width < 1024 )   
			 		TB_WIDTH = Math.abs((screen.width * 92) / 100);			 	 
	
				if ( screen.height < 768 )	
		 			TB_HEIGHT = Math.abs((screen.height * 40) / 100);

				$("#TB_window").append("<a href='' id='TB_ImageOff' title='Close'><img id='TB_Image' src='" + url + "' width='" + imageWidth + "' height='" + imageHeight + "' alt='" + caption + "' border='0'/></a>" + "<div id='TB_caption'>" + caption + "<div id='TB_secondLine'>" + TB_imageCount + TB_PrevHTML + TB_NextHTML + "</div></div><div id='TB_closeWindow'><a href='#' id='TB_closeWindowButton' title='Close'>" + JLI_cerrar + "&nbsp;&nbsp;<img src='/esp/gbp/bin/img/particulares_desconectar.gif' alt='" + JLI_cerrar + "' border='0'/></a>&nbsp;&nbsp;</div>");
				$("#TB_closeWindowButton").click(tb_remove);
				if (!(TB_PrevHTML === "")) {
					function goPrev(){
						if ($(document).unbind("click", goPrev)) 
							{$(document).unbind("click", goPrev);
						}
						$("#TB_window").remove();
						$("body").append("<div id='TB_window'></div>");
						tb_show(TB_PrevCaption, TB_PrevURL, imageGroup);
						return false;
					}
					$("#TB_prev").click(goPrev);
				}
				if (!(TB_NextHTML === "")) {
					function goNext(){
						$("#TB_window").remove();
						$("body").append("<div id='TB_window'></div>");
						tb_show(TB_NextCaption, TB_NextURL, imageGroup);
						return false;
					}
					$("#TB_next").click(goNext);
				}
				document.onkeydown = function(e){
					if (e == null) {keycode = event.keyCode;}// ie
					else {keycode = e.which;}// mozilla
					if (keycode == 27) {tb_remove();}// close
					else 
						if (keycode == 190) { // display previous image
							if (!(TB_NextHTML == "")) {
								document.onkeydown = "";
								goNext();
							}
						}
						else 
							if (keycode == 188) { // display next image
								if (!(TB_PrevHTML == "")) {
									document.onkeydown = "";
									goPrev();
								}
							}
				};
				tb_position();
				$("#TB_load").remove();
				$("#TB_ImageOff").click(tb_remove);
				$("#TB_window").css({display: "block"}); //for safari using css instead of show
				$("#TB_window").css({overflow:"auto"});
			};
			imgPreloader.src = url;
		}
		else {//code to show html
			var queryString = url.replace(/^[^\?]+\??/, '');
			var params = tb_parseQuery(queryString);
			TB_WIDTH = (params['width'] * 1) + 30 || 630; //defaults to 630 if no paramaters were added to URL
			TB_HEIGHT = (params['height'] * 1) + 40 || 440; //defaults to 440 if no paramaters were added to URL

			if ( screen.width < 1024 )   
			 	TB_WIDTH = Math.abs((screen.width * 92) / 100);			 	 
	
			if ( screen.height < 768 )	
		 		TB_HEIGHT = Math.abs((screen.height * 40) / 100);
			
			ajaxContentW = TB_WIDTH - 30;
			ajaxContentH = TB_HEIGHT - 45;
			if (url.indexOf('TB_iframe') != -1) 
			{// either iframe or ajax window
				urlNoQuery = url.split('TB_');
				$("#TB_iframeContent").remove();
				if (params['modal'] != "true") 
				{//iframe no modal
					$("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>" + caption + "</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' title='" + JLI_cerrar + "'>" + JLI_cerrar + "</a>&nbsp;&nbsp;</div></div><iframe frameborder='0' hspace='0' src='" + urlNoQuery[0] + "' id='TB_iframeContent' name='TB_iframeContent" + Math.round(Math.random() * 1000) + "' onload='tb_showIframe()' style='width:" + (ajaxContentW + 29) + "px;height:" + (ajaxContentH + 17) + "px;' > </iframe>");
				}
				else 
				{//iframe modal
					$("#TB_overlay").unbind();
					$("#TB_window").append("<iframe frameborder='0' hspace='0' src='" + urlNoQuery[0] + "' id='TB_iframeContent' name='TB_iframeContent" + Math.round(Math.random() * 1000) + "' onload='tb_showIframe()' style='width:" + (ajaxContentW + 29) + "px;height:" + (ajaxContentH + 17) + "px;'> </iframe>");
				}
			}
			else 
			{// not an iframe, ajax
				if ($("#TB_window").css("display") != "block") {
					if (params['modal'] != "true") {//ajax no modal
						$("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>" + caption + "</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' title='"+JLI_cerrar+"'>" + JLI_cerrar + "</a>&nbsp;&nbsp;</div></div><div id='TB_ajaxContent' style='width:" + ajaxContentW + "px;height:" + ajaxContentH + "px'></div>");
					}
					else {//ajax modal
						$("#TB_overlay").unbind();
						$("#TB_window").append("<div id='TB_ajaxContent' class='TB_modal' style='width:" + ajaxContentW + "px;height:" + ajaxContentH + "px;'></div>");
					}
				}
				else {//this means the window is already up, we are just loading new content via ajax
					$("#TB_ajaxContent")[0].style.width = ajaxContentW + "px";
					$("#TB_ajaxContent")[0].style.height = ajaxContentH + "px";
					$("#TB_ajaxContent")[0].scrollTop = 0;
					$("#TB_ajaxWindowTitle").html(caption);
				}
			}
			$("#TB_closeWindowButton").click(tb_remove);
			if (url.indexOf('TB_inline') != -1) {
				$("#TB_ajaxContent").append($('#' + params['inlineId']).children());
				$("#TB_window").unload(function(){
					$('#' + params['inlineId']).append($("#TB_ajaxContent").children()); // move elements back when you're finished
				});
				tb_position();
				$("#TB_load").remove();
				$("#TB_window").css({display: "block"});
			}
			else 
				if (url.indexOf('TB_iframe') != -1) {
					tb_position();
					if ($.browser.safari) {//safari needs help because it will not fire iframe onload
						$("#TB_load").remove();
						$("#TB_window").css({display: "block"});
					}
				}
				else {
					$("#TB_ajaxContent").load(url += "&random=" + (new Date().getTime()), function(){//to do a post change this load method
						tb_position();
						$("#TB_load").remove();
						tb_init("#TB_ajaxContent a.thickbox");
						$("#TB_window").css({display: "block"});
					});
				}
		}
		if (!params['modal']) {
			document.onkeyup = function(e){
				if (e == null) {keycode = event.keyCode;}// ie
				else {keycode = e.which;}// mozilla
				if (keycode == 27) {tb_remove();}// close
			};
		}
		//$("auto").css("overflow","auto");
		$(".TB_iframeContent").css({overflow:"auto"});
	} 
	
	catch (e) {}//nothing here
}
function tb_showIframe(){
	$("#TB_load").remove();
	$("#TB_window").css({display:"block"});
}
function tb_remove() {

 	$("#TB_imageOff").unbind("click");
	$("#TB_closeWindowButton").unbind("click");
	$("#TB_window").fadeOut("fast",function(){$('#TB_window,#TB_overlay,#TB_HideSelect').trigger("unload").unbind().remove();});
	$("#TB_load").remove();
	if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
		$("body","html").css({height: "auto", width: "auto"});
		$("html").css("overflow","");
	}
	document.onkeydown = "";
	document.onkeyup = "";

	return false;
}
function tb_position() {
$("#TB_window").css({marginLeft: '-' + parseInt((TB_WIDTH / 2),10) + 'px', width: TB_WIDTH + 'px'});
	if ( !(jQuery.browser.msie && jQuery.browser.version < 7)) 
		 $("#TB_window").css({marginTop: '-' + parseInt((TB_HEIGHT / 2),10) + 'px'});
	else if ((jQuery.browser.msie)&&(_ie78))
		$("#TB_window").css('top','30px');
}
function tb_parseQuery ( query ) {
   var Params = {};
   if ( ! query ) {return Params;}// return empty object
   var Pairs = query.split(/[;&]/);
   for ( var i = 0; i < Pairs.length; i++ ) {
      var KeyVal = Pairs[i].split('=');
      if ( ! KeyVal || KeyVal.length != 2 ) {continue;}
      var key = unescape( KeyVal[0] );
      var val = unescape( KeyVal[1] );
      val = val.replace(/\+/g, ' ');
      Params[key] = val;
   }
   return Params;
}
function tb_getPageSize(){
	var de = document.documentElement;
	var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
	var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
	arrayPageSize = [w,h];
	return arrayPageSize;
}
function tb_detectMacXFF() {
  var userAgent = navigator.userAgent.toLowerCase();
  if (userAgent.indexOf('mac') != -1 && userAgent.indexOf('firefox')!=-1) return true;
}
function tb_fake( url ){
	var css_bg = {
		'background-color': '#FFF',
		'background-image': 'none',
		'border': '0 none',
		'height': '100%',
		'left': '1px',
		'margin': '-1px',
		'overflow': 'hidden',
		'padding': '0',
		'position': 'absolute',
		'top': '1px',
		'width': '100%',
		'z-index': '1001'
	};
	var css_window = {
		'border': '0 none',
		'height': '100%',
		'margin': '0',
		'overflow': 'auto',
		'padding': '0',
		'width': '100%'
	};
	var target_name		= String( ( new Date() ).valueOf() );
	var target_opened	= false;
	var target_url		= '/Bpemotor/';
	try{
		//	recover url params
		var queryString = url.replace(/^[^\?]+\??/, '');
		var params = tb_parseQuery( queryString );
		//	clear previous fake windows
		$('form[name=^"launcher_"], iframe[name=^"window_"], div[name=^"bg_"]').remove();
		//	set fake background
		$('body').append( $('<div>').css( css_bg ).attr('id', 'bg_'+target_name) );
		//	detect browser
		if( typeof $.browser.msie == 'undefined' ){
			//	Ohter - No IExplorer
			//	create launcher for fake window
			var launcher = $('<form>').attr('id', 'launcher_'+target_name).attr('target', 'window_'+target_name).attr('action', target_url).attr('method', 'post');
			for(var key in params){
				launcher.append( $('<input type="hidden">').attr('name', key).val(params[key]) );
			}
			launcher.appendTo( document.body );
			//	create fake window
			var layer_window	= $('<iframe>').css( css_window ).attr('id', 'window_'+target_name).attr('name', 'window_'+target_name).attr('frameborder', '0').attr('hspace', '0');
			layer_window.load(function(){
				if( !target_opened ){
					$('body form#launcher_'+target_name, window.top.document).submit();
					target_opened = true;
				}
			});
			$('body div#bg_'+target_name).append( layer_window );
		}else{
			// IExplorer
			//	build url
			for(var key in params){
				target_url += (/\/\?/i).test( target_url )?'&':'?';
				target_url += key+'='+encodeURIComponent(params[key]);
			}
			//	create fake window
			$('body div#bg_'+target_name).append( '<iframe id="window_'+target_name+'" name="window_'+target_name+'" frameborder="0" hspace="0" src="'+target_url+'">' );
			$('body iframe#window_'+target_name).css( css_window );
		}
	}catch( Err ){
		//	nothing here
	}
}

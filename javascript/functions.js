var DOC_ROOT = "../";
var DOC_ROOT_TRUE = "../";
var DOC_ROOT_SECTION = "../../";

Event.observe(window, 'load', function() {
	if($('login_0'))
		Event.observe($('login_0'), "click", LoginCheck);

	if($('addNotice'))
		Event.observe($('addNotice'), "click", AddNoticePopup);
});

function LoginCheck()
{
	new Ajax.Request(WEB_ROOT+'/ajax/login.php',
	{
  	parameters: $('loginForm').serialize(true),
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			console.log(response);
			var splitResponse = response.split("|");
			if(splitResponse[0] == "fail")
			{
				$('divStatus').innerHTML = splitResponse[1];
				$('centeredDiv').show();
				grayOut(true);
			}
			else
			{
				Redirect('/sistema');
			}
		},
    onFailure: function(){ alert('Something went wrong...') }
  });

}

function ToogleStatusDiv()
{
	$('centeredDiv').toggle();
	grayOut(false);
}

function ToogleStatusDivOnPopup()
{
	$('centeredDivOnPopup').toggle();
}

function AddNoticePopup(id)
{
 	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/homepage.php',
	{
		method:'post',
		parameters: {type: "addNotice"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ AddNoticePopup(0); });
			Event.observe($('saveNotice'), "click", AddNotice);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function HistorialPopup(id)
{
 	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/homepage.php',
	{
		method:'post',
		parameters: {type: "addHistorialPendiente", noticeId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ HistorialPopup(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}


function AddPendientePopup(id)
{
 	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/homepage.php',
	{
		method:'post',
		parameters: {type: "addPendiente"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ AddPendientePopup(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddComentarioPendiente()
{
	new Ajax.Request(WEB_ROOT+'/ajax/homepage.php',
	{
		method:'post',
		parameters: $('addPendienteForm').serialize(true),
		onLoading: function(){
			$("btnSaveNot").hide();
			$("load").show();
		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");

			$("load").hide();

			if(splitResponse[0] == "ok")
			{
				ShowStatus(splitResponse[1]);
				$("contenido").innerHTML = splitResponse[2];
			}
			else
			{
				alert("Ocurrio un error al eliminar el registro");
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}
function AddPendiente()
{
	new Ajax.Request(WEB_ROOT+'/ajax/homepage.php',
	{
		method:'post',
		parameters: $('addPendienteForm').serialize(true),
		onLoading: function(){
			$("btnSaveNot").hide();
			$("load").show();
		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");

			$("load").hide();

			if(splitResponse[0] == "fail")
			{
				$("btnSaveNot").show();
				ShowStatusPopUp(splitResponse[1])
			}
			else
			{
				$("noticeId").value = splitResponse[1];
				$('addPendienteForm').submit();
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function AddNotice()
{
	new Ajax.Request(WEB_ROOT+'/ajax/homepage.php',
	{
		method:'post',
		parameters: $('addNoticeForm').serialize(true),
		onLoading: function(){
			$("btnSaveNot").hide();
			$("load").show();
		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");

			$("load").hide();

			if(splitResponse[0] == "fail")
			{
				$("btnSaveNot").show();
				ShowStatusPopUp(splitResponse[1])
			}
			else
			{
				$("noticeId").value = splitResponse[1];
				$('addNoticeForm').submit();
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function DeleteNotice(id)
{
	var resp = confirm("Esta seguro de eliminar este registro?");

	if(!resp)
		return;

	new Ajax.Request(WEB_ROOT+'/ajax/homepage.php',
	{
		method:'post',
		parameters: "type=deleteNotice&noticeId="+id,
		onLoading: function(){
		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			console.log(response);
			if(splitResponse[0] == "ok")
			{
				ShowStatus(splitResponse[1]);
				$("contenido").innerHTML = splitResponse[2];
			}
			else
			{
				alert("Ocurrio un error al eliminar el registro");
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function ClosePendiente(id)
{
	var resp = confirm("Esta seguro de cerrar este pendiente?");

	if(!resp)
		return;

	new Ajax.Request(WEB_ROOT+'/ajax/homepage.php',
	{
		method:'post',
		parameters: "type=closePendiente&noticeId="+id,
		onLoading: function(){
		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			console.log(response);
			if(splitResponse[0] == "ok")
			{
				ShowStatus(splitResponse[1]);
				$("contenido").innerHTML = splitResponse[2];
			}
			else
			{
				alert("Ocurrio un error al eliminar el registro");
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function Redirect(page)
{
	window.location = WEB_ROOT+page;
}

function Logout() {
	new Ajax.Request(WEB_ROOT+'/ajax/logout.php',
	{
		method:'post',
    onSuccess: function(transport){
      Redirect('');
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}
function Logout() {
	new Ajax.Request(WEB_ROOT+'/ajax/logout.php',
	{
		method:'post',
    onSuccess: function(transport){
      Redirect('');
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}
/*
Event.observe(window, 'load', function() {
	Event.observe($('cambiarRfcButton'), "click", CambiarRfcActivo);
	Event.observe($('logoutDiv'), "click", Logout);
});
*/
function CambiarRfcActivo()
{
	new Ajax.Request(WEB_ROOT+'/ajax/sistema.php',
	{
  	parameters: {rfcId: $('rfcId').value, type: "cambiarRfcActivo"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			window.location.reload();
		},
    onFailure: function(){ alert('Something went wrong...') }
  });

}

function printExcel(id, type)
{
	new Ajax.Request(WEB_ROOT+'/ajax/print.php',
	{
  		parameters: {contenido: $('contenido').innerHTML, type:type},
		method:'post',
		onLoading: function(){
				$('loadPrint').innerHTML = "Sea paciente mientras carga el archivo...";
		},
    	onSuccess: function(transport){
      		var response = transport.responseText || "no response text";

			$('loadPrint').innerHTML = "";
			window.location = response;
		},
    	onFailure: function(){ alert('Something went wrong...') }
  });
}

function ToggleSpecifiedDiv(id)
{
	var myId;

	console.log(id);
	$$('#contenido tr.class-'+id).each(function(e){
		myId = e.identify();
		console.log(myId);
		e.toggle();

		if(e.visible() === false)
		{
			$$('#contenido tr.'+id).each(function(f){
					myId = f.identify();
					var del = f.hasClassName('class-'+id);
					if(del === false)
					{
						$(myId).hide();
					}
			});
		}
	});
}
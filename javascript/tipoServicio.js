Event.observe(window, 'load', function() {
	Event.observe($('addTipoServicio'), "click", AddTipoServicioDiv);

	AddEditTipoServicioListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteTipoServicioPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditTipoServicioPopup(id);
		}

		del = el.hasClassName('spanTextToReport');
		if(del == true)
		{
			OpenConfigTextToReport(id);
		}
	}
	$('contenido').observe("click", AddEditTipoServicioListeners);
});

function EditTipoServicioPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/tipoServicio.php',
	{
		method:'post',
		parameters: {type: "editTipoServicio", tipoServicioId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditTipoServicioPopup(0); });
			Event.observe($('editTipoServicio'), "click", EditTipoServicio);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditTipoServicio()
{
	new Ajax.Request(WEB_ROOT+'/ajax/tipoServicio.php',
	{
		method:'post',
		parameters: $('editTipoServicioForm').serialize(true),
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "fail")
			{
				ShowStatusPopUp(splitResponse[1])
			}
			else
			{
				ShowStatusPopUp(splitResponse[1])
				$('contenido').innerHTML = splitResponse[2];
				AddTipoServicioDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteTipoServicioPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/tipoServicio.php',
	{
		method:'post',
		parameters: {type: "deleteTipoServicio", tipoServicioId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddTipoServicioDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddTipoServicioDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/tipoServicio.php',
	{
		method:'post',
		parameters: {type: "addTipoServicio"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addTipoServicioButton'), "click", AddTipoServicio);
			Event.observe($('fviewclose'), "click", function(){ AddTipoServicioDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddTipoServicio()
{
	new Ajax.Request(WEB_ROOT+'/ajax/tipoServicio.php',
	{
		method:'post',
		parameters: $('addTipoServicioForm').serialize(true),
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "fail")
			{
				ShowStatusPopUp(splitResponse[1])
			}
			else
			{
				ShowStatusPopUp(splitResponse[1])
				$('contenido').innerHTML = splitResponse[2];
				AddTipoServicioDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function OpenConfigTextToReport(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/tipoServicio.php',
		{
			method:'post',
			parameters: {type: "openConfigTextToReport", id:id},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				FViewOffSet(response);
				Event.observe($('closePopUpDiv'), "click", close_popup);
				jQ('#btnText').on("click", SaveTextReport)
			},
			onFailure: function(){ alert('Something went wrong...') }
		});
}
function SaveTextReport() {
	var form = jQ(this).parents('form:first');
	var fd =  new FormData(form[0]);
	jQ.ajax({
		url:WEB_ROOT+'/ajax/tipoServicio.php',
		method:'post',
		data:fd,
		processData: false,
		contentType: false,
		type: 'POST',
		beforeSend: function(){
			jQ("#loading-img").show();
			jQ('#btnText').hide();
		},
		success: function(response){
			var splitResp = response.split("[#]");
			if(splitResp[0]=='ok'){
				close_popup();
				ShowStatusPopUp(splitResp[1]);
			}
			else{
				jQ("#loading-img").hide();
				jQ('#btnText').show();
				ShowStatusPopUp(splitResp[1]);
			}
		}
	});
}
jQ(document).on('change','#inheritanceId',function(){
	var id  =  jQ(this).find(":selected").val();
	jQ.ajax({
		url:WEB_ROOT+'/ajax/tipoServicio.php',
		method:'post',
		data:{ id, type:'inheritanceFrom'},
		success:function (response) {
			jQ('#stepTask').html(response);
			TogglePermisos();
		}

	});
});
function TogglePermisos(){
	jQ('.deepList').on('click',function(){
		if(jQ("ul#"+this.id).is(':visible')){
			jQ("#"+this.id).html('[+]-');
			jQ("ul#"+this.id).removeClass('siShow');
		}
		else
		{
			jQ('#'+this.id).html('[-]-');
			jQ("ul#"+this.id).addClass('siShow');
		}

	});
}
jQ(document).on('click','div#stepTask input[type="checkbox"]',function(){
    jQ(this).next().find('input[type=checkbox]').prop('checked',this.checked);
    jQ(this).parents('ul').prev('input[type=checkbox]').prop('checked',function(){
        return jQ(this).next().find(':checked').length;
    });
});


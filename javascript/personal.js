Event.observe(window, 'load', function() {
	Dropzone.autoDiscover=false;
	Event.observe($('addPersonal'), "click", AddPersonalDiv);

	AddEditPersonalListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeletePersonalPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditPersonalPopup(id);
		}
        del = el.hasClassName('spanShowFile');
        if(del == true)
        {
            ShowFilePopup(id);
        }
	}

	$('contenido').observe("click", AddEditPersonalListeners);

});

function EditPersonalPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/personal.php',
	{
		method:'post',
		parameters: {type: "editPersonal", personalId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditPersonalPopup(0); });
			Event.observe($('editPersonal'), "click", EditPersonal);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditPersonal()
{
	new Ajax.Request(WEB_ROOT+'/ajax/personal.php',
	{
		method:'post',
		parameters: $('editPersonalForm').serialize(true),
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
				AddPersonalDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeletePersonalPopup(id)
{
	var message = "Realmente deseas eliminar este contador?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/personal.php',
	{
		method:'post',
		parameters: {type: "deletePersonal", personalId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddPersonalDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddPersonalDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/personal.php',
	{
		method:'post',
		parameters: {type: "addPersonal"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('btnAddPersonal'), "click", AddPersonal);
			Event.observe($('fviewclose'), "click", function(){ AddPersonalDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddPersonal()
{
	new Ajax.Request(WEB_ROOT+'/ajax/personal.php',
	{
		method:'post',
		parameters: $('addPersonalForm').serialize(true),
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
				AddPersonalDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

	function ToggleReporta()
	{
		if($('tipoPersonal').value == 'Auxiliar')
		{
			if($('jefeContadorDiv'))
				$('jefeContadorDiv').show();
			if($('jefeSupervisorDiv'))
				$('jefeSupervisorDiv').show();
			if($('jefeGerenteDiv'))
				$('jefeGerenteDiv').show();
			if($('jefeSocioDiv'))
				$('jefeSocioDiv').show();
		}

		if($('tipoPersonal').value == 'Contador')
		{
			if($('jefeContadorDiv'))
				$('jefeContadorDiv').hide();
			if($('jefeSupervisorDiv'))
				$('jefeSupervisorDiv').show();
			if($('jefeGerenteDiv'))
				$('jefeGerenteDiv').show();
			if($('jefeSocioDiv'))
				$('jefeSocioDiv').show();
		}

		if($('tipoPersonal').value == 'Supervisor')
		{
			if($('jefeContadorDiv'))
				$('jefeContadorDiv').hide();
			if($('jefeSupervisorDiv'))
				$('jefeSupervisorDiv').hide();
			if($('jefeGerenteDiv'))
				$('jefeGerenteDiv').show();
			if($('jefeSocioDiv'))
				$('jefeSocioDiv').show();
		}

		if($('tipoPersonal').value == 'Gerente')
		{
			if($('jefeContadorDiv'))
				$('jefeContadorDiv').hide();
			if($('jefeSupervisorDiv'))
				$('jefeSupervisorDiv').hide();
			if($('jefeGerenteDiv'))
				$('jefeGerenteDiv').hide();
			if($('jefeSocioDiv'))
				$('jefeSocioDiv').show();
		}

		if($('tipoPersonal').value == 'Socio')
		{
			if($('jefeContadorDiv'))
				$('jefeContadorDiv').hide();
			if($('jefeSupervisorDiv'))
				$('jefeSupervisorDiv').hide();
			if($('jefeGerenteDiv'))
				$('jefeGerenteDiv').hide();
			if($('jefeSocioDiv'))
				$('jefeSocioDiv').hide();
		}
	}
function ShowFilePopup(id)
{
    grayOut(true);
    $('fview').show();
    if(id == 0)
    {
        $('fview').hide();
        grayOut(false);
        return;
    }
    new Ajax.Request(WEB_ROOT+'/ajax/personal.php',
        {
            method:'post',
            parameters: {type: "showFile", personalId:id},
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                FViewOffSet(response);
                Event.observe($('closePopUpDiv'), "click", function(){ EditPersonalPopup(0); });
                LoadBoxDropzone();
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}
function LoadBoxDropzone(){
    jQ('#file-up form.dropzone').each(function(){
        //ocultar el contenedor
        var sp=(this.id).split('_');
        var existFile = jQ('#exist_file'+sp[1]+sp[2]).val();
        if(existFile)
            var defaultMessage = 'Ya existe un archivo, para ver dar click en el icono de abajo  <img src="'+WEB_ROOT+'/images/downCloud24.png">, de lo contrario arraste o click  en esta zona para actualizar';
        else
            var defaultMessage = 'Arraste o click en esta zona para subir archivo';
        jQ(this).dropzone({
            dictDefaultMessage:defaultMessage,
            url: WEB_ROOT + '/ajax/expediente.php',
            paramName:'file_'+sp[1]+sp[2],
            addRemoveLinks: true,
            dictInvalidFileType:'Archivo no valido',
            dictRemoveFile:'Eliminar',
            maxFileSize:2,
            autoProcessQueue: true,
            autoQueue:true,
            maxFiles:1,
            uploadMultiple:false,
            acceptedFiles:'application/pdf,image/jpeg,image/png',
            thumbnailWidth:'112px',
            thumbnailHeight:'100px',
            init: function() {
                var my = this;
                this.on('sending',function(file,xhr,formData){
                    formData.append('idp',sp[1]);
                    formData.append('ide',sp[2]);
                });
                this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
                this.on("complete", function(file) {
                    this.removeFile(file);
                });
                this.on('success',function(file,response){
                    var splitResp = response.split('[#]');
                    if(splitResp[0]=='ok') {
                        jQ('#content-files').html(splitResp[1]);
                        LoadBoxDropzone();
                    }
                    else
                        alert(splitResp[1]);
                });
            }
        });
    });
}



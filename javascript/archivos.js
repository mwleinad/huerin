function EditArchivoPopup(id,depId)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/archivos.php',
	{
		method:'post',
		parameters: {type: "editArchivo", archivoId:id,depId:depId},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditArchivoPopup(0); });
			createCombineDropzone();
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}
function SaveUpdateArchivo() {
	jQ.ajax({
		type:'POST',
		url:WEB_ROOT+"/ajax/archivos.php",
		data:jQ('#frmArchivoDep').serialize(),
		success:function(response){
            var respSplit = response.split("[#]");
            if(respSplit[0]=='ok'){
                ShowStatusPopUp(respSplit[1]);
                jQ("#contenido").html(respSplit[2]);
                close_popup();
            }else
            {
                ShowStatusPopUp(respSplit[1]);
            }
		},
		error:function(error){
            ShowErrorOnPopup(errorMessage,1);
		}
	});
}
function createCombineDropzone(){
  var myDropzone  = new Dropzone("#zoneClick",
	  {
	  	url:WEB_ROOT+'/ajax/archivos.php',
        dictDefaultMessage:'Arraste o click en esta zona para subir archivo',
        paramName:'path',
        autoProcessQueue: false,
        uploadMultiple: false,
        parallelUploads: 1,
        maxFiles: 1,
		init: function() {
            var currentForm =  jQ(this.element).parents('form:first');
            var drop = this;
            document.querySelector("input[id='btnArchivoDep'").addEventListener("click",function (e) {
                e.preventDefault();
                e.stopPropagation();
                if(drop.getQueuedFiles().length>0)
                	drop.processQueue();
                else
                    SaveUpdateArchivo();

            })
			// Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
			// of the sending event because uploadMultiple is set to true.
			this.on("sendingmultiple", function(file,xhr,formData) {
				// Gets triggered when the form is actually being sent.
				// Hide the success button or the complete form.
			});
			this.on('sending',function(file,xhr,formData){
				[...currentForm[0].elements].forEach((input) => {
					formData.append(input.name,input.value);
				});
			});
			this.on("successmultiple", function(files, response) {
				// Gets triggered when the files have successfully been sent.
				// Redirect user or notify of success.
			});
			this.on("errormultiple", function(files, response) {
				// Gets triggered when there was an error sending the files.
				// Maybe show form again, and notify user of error
			});
			this.on("maxfilesexceeded", function(file) {
				this.removeAllFiles();
				this.addFile(file);
			});
			this.on("complete", function(file) {
				this.removeFile(file);
			});
			this.on('success',function(file,response){
                var respSplit = response.split("[#]");
                if(respSplit[0]=='ok'){
                    ShowStatusPopUp(respSplit[1]);
                    jQ("#contenido").html(respSplit[2]);
                    close_popup();
                }else
                {
                    ShowStatusPopUp(respSplit[1]);
                }
			});
			this.on("error", function(file, errorMessage) {
				ShowErrorOnPopup(errorMessage,1);
			});
		}
	  });
}

function DeleteArchivoPopup(id,depa)
{
	var message = "¿Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/archivos.php',
	{
		method:'post',
		parameters: {type: "deleteArchivo", archivoId: id, depa: depa},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			console.log(response);
			var splitResponse = response.split("[#]");
            ShowStatusPopUp(splitResponse[1]);
			jQ('#contenido').html(splitResponse[2]);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function NuevoArchivo(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/archivos.php',
	{
		method:'post',
		parameters: {type: "addArchivo", departamentoId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ NuevoArchivo(0); });
            createCombineDropzone();
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddArchivo()
{
	new Ajax.Request(WEB_ROOT+'/ajax/archivo.php',
	{
		method:'post',
		parameters: $('addArchivoForm').serialize(true),
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
				$('contentArchivos').innerHTML = splitResponse[2];
				AddArchivoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}


jQ(document).ready(function () {
	jQ(document).on('click', '.spanControlService', function () {
		var type = jQ(this).data('type');
		var id =  jQ(this).data('id');
		jQ.ajax({
			url: WEB_ROOT + '/ajax/tipoServicio.php',
			type: 'POST',
			data: { type: type, tipoServicioId:id},
			dataType: 'json',
			success: function (response) {
				grayOut(true);
				$('fview').show();
				FViewOffSet(response.template);
				if (jQ("#secondaryMultiple").length) {
					jQ("select[multiple]").multiselect({
						columns: 1,
						search: true,
						maxHeight:60,
						selectGroup: true,
						selectAll:true,
						texts: {
							placeholder: 'Selecciona los servicios secundarios',
							search         : 'Buscar',         // search input placeholder text
							selectedOptions: ' Seleccionado',      // selected suffix text
							selectAll      : 'Seleccionar todos',     // select all text
							unselectAll    : 'Quitar todos',   // unselect all text
							noneSelected   : 'Ningun elemento seleccionado'   // None selected text
						}
					});
					jQ("select[multiple]").multiselect('loadOptions', response.secondary_services);
				}
			},
			error: function (error) {
				alert(error)
			}
		})
	})

	jQ(document).on('click', '.spanSaveService', function () {
		var form = jQ(this).parents('form:first');
		var fd =  new FormData(form[0]);
		jQ.ajax({
			url: WEB_ROOT + '/ajax/tipoServicio.php',
			type: 'POST',
			data: fd,
			processData: false,
			contentType: false,
			success: function (response) {
				var splitResponse = response.split("[#]")
				ShowStatusPopUp(splitResponse[1])
				if(splitResponse[0] === "ok") {
					close_popup()
					jQ('#contenido').html(splitResponse[2]);
				}
			},
			error: function (error) {
				alert(error)
			}
		})
	})

	jQ(document).on('click', '.spanDelete', function () {
		var id = jQ(this).data('id');
		var type = jQ(this).data('type');
		jQ.ajax({
			url: WEB_ROOT + '/ajax/tipoServicio.php',
			type: 'POST',
			data: { type:type, tipoServicioId:id},
			success: function (response) {
				var splitResponse = response.split("[#]")
				ShowStatusPopUp(splitResponse[1])
				if(splitResponse[0] === "ok") {
					$('#contenido').html(splitResponse[2]);
				}
			},
			error: function (error) {
				alert(error)
			}
		})
	})
})

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

jQ(document).on('change', '#isPrimary', function () {
  jQ('.field_secondary').toggle(parseInt(this.value) === 1)
});


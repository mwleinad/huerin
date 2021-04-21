var DOC_ROOT = "../";
var ops = {
	placeholder: "Seleccionar un sector.",
	minimumResultsForSearch: -1,
	formatSearching: 'Buscando opciones',
};
Event.observe(window, 'load', function() {

	if($('login_0'))
		Event.observe($('login_0'), "click", LoginCheck);

	if($('addNotice'))
		Event.observe($('addNotice'), "click", AddNoticePopup);
});

var Select2Cascade = ( function(window, jQ) {
	function Select2Cascade(parent, child, url, select2Options) {
		var afterActions = [];
		var options = select2Options || {};

		// Register functions to be called after cascading data loading done
		this.then = function(callback) {
			afterActions.push(callback);
			return this;
		};
		parent.select2(select2Options).on("change", function (e) {
			child.prop("disabled", true);
			jQ.post(url, {type: child.prop('name'), id: e.val }, function (items) {
				var newOptions = '<option value="">-- Select --</option>';
				jQ.each(items, function (key, value) {
					newOptions += '<option value="'+ value.id+'">'+ value.name +'</option>';
				})
				child.select2('destroy').html(newOptions).prop("disabled", false)
					.select2(options);

				afterActions.forEach(function (callback) {
					callback(parent, child, items);
				});
			}, 'json');
		});
	}
	return Select2Cascade;
})( window, jQ);
jQ(document).ready(function () {
	setInterval(check_session, 60000);
	if(jQ('.select2').length > 0) {
		jQ('.select2').select2(ops);
		new Select2Cascade(jQ('#sector'), jQ('#subsector'), WEB_ROOT+"/ajax/load_items_select.php", ops);
		new Select2Cascade(jQ('#subsector'), jQ('#actividad_comercial'), WEB_ROOT+"/ajax/load_items_select.php", ops);
		if(jQ('#responsableGerente').length > 0)
			new Select2Cascade(jQ('#responsableGerente'), jQ('#responsableSupervisor'), WEB_ROOT+"/ajax/load_items_select.php", ops);
	}

	if(jQ('#use_alternative_rz_for_invoice').length > 0) {
		jQ('#use_alternative_rz_for_invoice').on('change', function () {
			if (this.value === '1') {
				jQ('#alternative_rz_id').select2({
					placeholder: 'Seleccione una razon..',
					minimumResultsForSearch: -1,
					formatSearching: 'Buscando opciones',
					ajax: {
						type: 'post',
						url: WEB_ROOT + "/ajax/load_items_select.php",
						data: function () {
							return {
								type: 'contract',
								id: jQ('#customerId').val(),
								contractId: jQ('#contractId').val(),
							}
						},
						processResults: function (data) {
							return { results: data }
						}
					},
					initSelection: function (element, callback) {
						var id = jQ(element).val();
						if (id !== '') {
							jQ.post(WEB_ROOT + "/ajax/load_items_select.php", {
								type: 'defaultContract',
								id: id
							}, function (response) {
								callback(response);
							}, 'json');
							if(id === '0')
								jQ('#div_other_data').show()
						}
					}
				}).on('change', function(e) {
					if(e.val === '0') {
						jQ('#div_other_data').show()
						jQ('#div_separate_invoice').hide();
					} else {
						jQ('#div_other_data').hide();
						jQ('#div_separate_invoice').show();
					}
				});
			} else {
				jQ('#alternative_rz_id').select2('destroy');
				jQ('#div_other_data').hide();
			}
		});
		if(jQ('#use_alternative_rz_for_invoice').val() === '1')
			jQ('#use_alternative_rz_for_invoice').trigger('change');
	}
});
function LoginCheck()
{
	new Ajax.Request(WEB_ROOT+'/ajax/login.php',
	{
  	parameters: $('loginForm').serialize(true),
		method:'post',
    onSuccess: function(transport){
      		var response = transport.responseText || "no response text";
			var splitResponse = response.split("|");
			if(splitResponse[0] == "fail") {
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
    grayOut(false);
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
			var splitResp = response.split("[#]");
			if(splitResp[0]=='ok'){
                FViewOffSet(splitResp[1]);
                Event.observe($('closePopUpDiv'), "click", function(){ AddNoticePopup(0); });
                Event.observe($('saveNotice'), "click", AddNotice);
			}else{
				Redirect("/"+splitResp[1]);
			}

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
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok') {
                FViewOffSet(splitResp[1]);
                Event.observe($('closePopUpDiv'), "click", function () {
                    HistorialPopup(0);
                });
            }else{
                Redirect("/"+splitResp[1]);
			}
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
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok') {
                FViewOffSet(splitResp[1]);
                Event.observe($('closePopUpDiv'), "click", function () {
                    AddPendientePopup(0);
                });
            }else{
                Redirect("/"+splitResp[1]);
			}
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
	var fd =  new FormData(document.getElementById('addNoticeForm'));
    jQ.ajax({
        url: WEB_ROOT+'/ajax/homepage.php',
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ('#load').show();
            jQ('#saveNotice').hide();
        },
        success: function(response){
            var splitResp = response.split("[#]");
            console.log(response);
            if(splitResp[0]=='ok')
            {
                jQ('#load').hide();
                ShowStatusPopUp(splitResp[1]);
                jQ('#contenidoAviso').html(splitResp[2]);
                jQ('#contenidoPendiente').html(splitResp[3]);
                jQ('#saveNotice').show();
                AddNoticePopup(0);

            }else{
                jQ('#load').hide();
                jQ('#saveNotice').show();
                ShowStatusPopUp(splitResp[1]);
            }

        },

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
				$("contenidoAviso").innerHTML = splitResponse[2];
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
function printExcel(id, type)
{
	new Ajax.Request(WEB_ROOT+'/ajax/print.php',
	{
  		parameters: {contenido: $('contenido').innerHTML, type:type},
		method:'post',
		onLoading: function(){
				$('loadPrint').innerHTML = "Sea paciente mientras carga el archivo...";
		},
    	onSuccess: function(transport) {
  			console.log(transport);
      		var response = transport.responseText || "no response text";
      		var splitResponse = response.split("[#]");
			$('loadPrint').innerHTML = "";
			window.location = splitResponse[1];
		},
    	onFailure: function(){ alert('Something went wrong...') }
  });
}
function printExcelJq(id, type)
{
	var con = jQ('#contenido').html();
	console.log(con)
	jQ.ajax({
		url:WEB_ROOT+"/ajax/print.php",
		method:"POST",
		data: { contenido: jQ('#contenido').html(), type:type},
		beforeSend: function() {
			jQ('#loadPrint').html("Sea paciente mientras carga el archivo...");
		},
		success:function(response)
		{
			console.log(response);
			var splitResponse = response.split("[#]");
			$('loadPrint').innerHTML = "";
			//window.location = splitResponse[1];
		},
		error: function (error) {
			console.log(error)
		}
	})
}

function ToggleSpecifiedDiv(id)
{
	var myId;
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
function ToggleDiv(id)
{
	$(id).toggle();
	if(jQ('#jump'+id).length>0)
        $('jump'+id).toggle();

}
function SetDateCalendar(input){
    var dateNow = jQ("#"+input.id).val();
    var flag = true;
    jQ("#"+input.id).datepicker({
        format:'yyyy-mm-dd',
        language:'es',
        autoclose:true,
        todayBtn: true,
        todayBtn: "linked"
    }).focus();
}
function Calendario(input){
    var dateNow = jQ("#"+input.id).val();
    var flag = true;
    jQuery("#"+input.id).datepicker({
        format:'yyyy-mm-dd',
        language:'es',
        autoclose:true,
        todayBtn: true,
        todayBtn: "linked"
    }).on('changeDate',function(e){
        if(flag){
            if(e.currentTarget.value!=dateNow)
                UpdateDateWorkflow(input);
            else
            {
                console.log('no cambio');
            }
            flag = false;
        }

    }).focus();
}
function CalendarioSimple(input){
    jQuery("#"+input.id).datepicker({
        format:'dd-mm-yyyy',
        language:'es',
        autoclose:true,
        todayBtn: true,
        todayBtn: "linked"
    }).focus();
}
jQ(document).on('change','form#addNoticeForm li>input[type="checkbox"]',function(){

	if(jQ(this).is(':checked'))
	{
		jQ('form#addNoticeForm input[type="checkbox"]#'+this.id).each(function(e){
			var self = this;
			self.checked=true;
            //comprobar por cada hijo si se tiene que activar el padre
            var clss = self.getAttribute('class');
            var actives = 0;
            jQ('.'+clss).each(function(){
                var selfChild= this;
                if(jQ(selfChild).is(':checked'))
                    actives++;
            });
            if(actives<=0)
            {
                var idSplit = clss.split('-');
                jQ('#father-'+idSplit[1]).prop('checked',false);
            }else{
                var idSplit = clss.split('-');
                jQ('#father-'+idSplit[1]).prop('checked',true);
            }
		});
	}
	else{
        jQ('form#addNoticeForm input[type="checkbox"]#'+this.id).each(function(){
        	var self =  this;
            self.checked=false;
            //comprobar por cada hijo si se tiene que activar el padre
            var clss = self.getAttribute('class')
            var actives = 0;
            jQ('.'+clss).each(function(){
            	var selfChild= this;
                if(jQ(selfChild).is(':checked'))
                    actives++;
            });
            if(actives==0)
            {
                var idSplit = clss.split('-');
                jQ('#father-'+idSplit[1]).prop('checked',false);
            }else{
                var idSplit = clss.split('-');
                jQ('#father-'+idSplit[1]).prop('checked',true);
            }
        });
	}

});
//si selecciona un padre se selecciona todo los hijos
jQ(document).on('change','form#addNoticeForm td>input[type="checkbox"]',function(){
    var id = this.id;
    var idSplit = id.split('-');
	if(jQ(this).is(':checked')){
        jQ('form#addNoticeForm input[type="checkbox"].child-'+idSplit[1]).each(function(e){
            var self = this;
            self.checked=true;
        });
	}else{
        jQ('form#addNoticeForm input[type="checkbox"].child-'+idSplit[1]).each(function(e){
            var self = this;
            self.checked=false;
        });
	}
});
jQ(document).on('change','form#addNoticeForm input[type="checkbox"]#allSelected',function(){
    if(jQ(this).is(':checked')){
        jQ('form#addNoticeForm table tr.area input[type="checkbox"]').each(function(e){
            var self = this;
            self.checked=true;
        });
	}else{
        jQ('form#addNoticeForm table tr.area input[type="checkbox"]').each(function(e){
            var self = this;
            self.checked=false;
        });
	}
});
var close_popup = () => {
    $('fview').innerHTML='';
    $('fview').hide();
    grayOut(false);
};

jQ(document).on('click','.showPayment',function (e) {
	e.preventDefault();
   var id =  this.id;
   var clase =  this.className;
   console.log('.'+clase+'-'+id);
   jQ('.'+clase+'-'+id).toggle();
});
function check_session()
{
	jQ.ajax({
		url:WEB_ROOT+"/ajax/check_session.php",
		method:"POST",
		success:function(data)
		{
			if(data == 'fail')
			{
				window.location.href = WEB_ROOT+"/login";
			}
		}
	})
}
jQ.fn.convertFormToJson = function() {
	var _ = {};
	jQ.map(this.serializeArray(), function(n) {
		const keys = n.name.match(/[a-zA-Z0-9_]+|(?=\[\])/g);
		if (keys.length > 1) {
			let tmp = _;
			pop = keys.pop();
			for (let i = 0; i < keys.length, j = keys[i]; i++) {
				tmp[j] = (!tmp[j] ? (pop === '') ? [] : {} : tmp[j]), tmp = tmp[j];
			}
			if (pop === '') tmp = (!Array.isArray(tmp) ? [] : tmp), tmp.push(n.value);
			else tmp[pop] = n.value;
		} else _[keys.pop()] = n.value;
	});
	return _;
}

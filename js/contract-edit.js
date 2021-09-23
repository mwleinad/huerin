Event.observe(window, 'load', function() {
	AddEditDocumentoListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteDocumentoPopup(id);
			return;
		}
	}
	$('contentDocumentos').observe("click", AddEditDocumentoListeners);

	AddEditRequerimientoListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteRequerimientoPopup(id);
			return;
		}
	}
	$('contentRequerimientos').observe("click", AddEditRequerimientoListeners);

	AddEditArchivoListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();

		if(del == true)
		{
			DeleteArchivoPopup(id);
			return;
		}
    	del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditArchivoFechaPopup(id);
      return;
		}
	}
	$('contentArchivos').observe("click", AddEditArchivoListeners);

	AddEditImpuestoListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();

		if(del == true)
		{
			DeleteImpuestoPopup(id);
			return;
		}
	}

	//$('contentImpuestos').observe("click", AddEditImpuestoListeners);

	AddEditObligacionListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteObligacionPopup(id);
			return;
		}
	}
	//$('contentObligaciones').observe("click", AddEditObligacionListeners);

});

function EditArchivoFechaPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/archivo.php',
	{
		method:'post',
		parameters: {type: "editArchivoFecha", archivoId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('editArchivoFecha'), "click", EditArchivo);
			Event.observe($('closePopUpDiv'), "click", function(){ EditArchivoFechaPopup(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}
function EditArchivo()
{
	new Ajax.Request(WEB_ROOT+'/ajax/archivo.php',
	{
		method:'post',
		parameters: $('editArchivoFechaForm').serialize(true),
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "fail")
			{
				ShowStatusPopUp(splitResponse[1])
			}
			else
			{
				$('contentArchivos').innerHTML = splitResponse[2];
				ShowStatusPopUp(splitResponse[1]);
				AddArchivoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}


function DeleteObligacionPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/obligacion.php',
	{
		method:'post',
		parameters: {type: "deleteObligacionContract", obligacionId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contentObligaciones').innerHTML = splitResponse[2];
				AddArchivoDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteImpuestoPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/impuesto.php',
	{
		method:'post',
		parameters: {type: "deleteImpuestoContract", impuestoId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contentImpuestos').innerHTML = splitResponse[2];
				AddArchivoDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteArchivoPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/archivo.php',
	{
		method:'post',
		parameters: {type: "deleteArchivo", archivoId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contentArchivos').innerHTML = splitResponse[2];
				AddArchivoDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddArchivoDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/archivo.php',
	{
		method:'post',
		parameters: {type: "addArchivo"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addArchivo'), "click", AddArchivo);
			Event.observe($('fviewclose'), "click", function(){ AddArchivoDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteDocumentoPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/documento.php',
	{
		method:'post',
		parameters: {type: "deleteDocumento", documentoId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contentDocumentos').innerHTML = splitResponse[2];
				AddDocumentoDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddDocumentoDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/documento.php',
	{
		method:'post',
		parameters: {type: "addDocumento"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addDocumento'), "click", AddDocumento);
			Event.observe($('fviewclose'), "click", function(){ AddDocumentoDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteRequerimientoPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/requerimiento.php',
	{
		method:'post',
		parameters: {type: "deleteRequerimiento", requerimientoId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contentRequerimientos').innerHTML = splitResponse[2];
				AddRequerimientoDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddRequerimientoDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/requerimiento.php',
	{
		method:'post',
		parameters: {type: "addRequerimiento"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addRequerimiento'), "click", AddRequerimiento);
			Event.observe($('fviewclose'), "click", function(){ AddRequerimientoDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}


function ChangeTipo()
{
	if($('type').value == "Persona Moral")
	{
		$('tipoDeSociedad').show();
		$('regimenesMorales').show();
		$('regimenesFisicos').hide();

		$('box-table-a').show();
		$('idse1').show();
		$('idse2').show();
		$('idse3').show();
	}
	else
	{
		$('tipoDeSociedad').hide();
		$('regimenesMorales').hide();
		$('regimenesFisicos').show();
		$('box-table-a').hide();
		$('idse1').hide();
		$('idse2').hide();
		$('idse3').hide();
	}
}

function VerifyForm(){

	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
	{
		method:'post',
		parameters: $('frmContract').serialize(true),
		onLoading: function(){
			$("divLoading").style.display = "block";
		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");

			$("divLoading").style.display = "none";

			if(splitResponse[0] == "fail")
			{
				ShowStatus(splitResponse[1]);
			}
			else
			{
				$('frmContract').submit();
		//		window.location.href = WEB_ROOT + "contract";
			}

		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}


function LoadSubcontracts(){

	var idContCat = $("contCatId").value;

	if(idContCat == 1){
		$("infoArrendamiento").style.display = "block";
		$("infoArrendamiento2").style.display = "block";
		$("infoCompraVenta").style.display = "none";
		$("infoCompraVenta2").style.display = "none";
		$("titParte1").innerHTML = "Arrendatario:";
		$("titParte2").innerHTML = "Arrendador:";
		$("txtMonto").innerHTML = "Monto de la renta";
	}else{
		$("infoArrendamiento").style.display = "none";
		$("infoArrendamiento2").style.display = "none";
		$("infoCompraVenta").style.display = "block";
		$("infoCompraVenta2").style.display = "block";
		$("titParte1").innerHTML = "Comprador:";
		$("titParte2").innerHTML = "Vendedor:";
		$("txtMonto").innerHTML = "Monto de la compraventa";
	}

	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
	{
		method:'post',
		parameters: {action:"loadSubcontracts", contCatId:idContCat},
		onLoading: function(){

		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");

			if(splitResponse[0] == "ok")
			{
				showRow("infoGral",6,true);
				$('listSubcontratos').innerHTML = splitResponse[1];
				$('listPartes').innerHTML = splitResponse[2];
			}

		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function LoadDocGral(){

	var idContSubcat = $("contSubcatId").value;

	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
	{
		method:'post',
		parameters: {action:"loadDocGral", contSubcatId:idContSubcat},
		onLoading: function(){

		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");

			if(splitResponse[0] == "ok")
			{
				$('listStatus').innerHTML = splitResponse[1];

				if(idContSubcat == 4)
					$("tblCond").style.display = "none";
				else if(idContSubcat == 3)
					$("tblCond").style.display = "block";

			}

		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function runJS(html)
{

    var search = html;
    var script;

    while( script = search.match(/(<script[^>]+javascript[^>]+>\s*(<!--)?)/i))
    {
      search = search.substr(search.indexOf(RegExp.$1) + RegExp.$1.length);

      if (!(endscript = search.match(/((-->)?\s*<\/script>)/))) break;

      block = search.substr(0, search.indexOf(RegExp.$1));
      search = search.substring(block.length + RegExp.$1.length);

      var oScript = document.createElement('script');
      oScript.text = block;
      document.getElementsByTagName("head").item(0).appendChild(oScript);
    }
}

function showRow(idTable,num,ver) {

  dis= ver ? '' : 'none';
  tab=document.getElementById(idTable);
  tab.getElementsByTagName('tr')[num].style.display=dis;

}

function loadCities(){

	var idState = $("stateId").value;

	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
	{
		method:'post',
		parameters: {action:"loadCities", stateId:idState},
		onLoading: function(){

		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");

			if(splitResponse[0] == "ok")
			{
				$('enumCity').innerHTML = splitResponse[1];
			}

		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function loadCitiesC(){

	var idState = $("stateIdC").value;

	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
	{
		method:'post',
		parameters: {action:"loadCitiesC", stateId:idState},
		onLoading: function(){

		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");

			if(splitResponse[0] == "ok")
			{
				$('enumCityC').innerHTML = splitResponse[1];
			}

		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function checkStatus(){

	var status = $("status").value;

	if(status == "firmado")
		$("divFechaFirma").style.display = "block";
	else
		$("divFechaFirma").style.display = "none";

}

function checkCartaCump(){

	var status = $("cartaCump").value;

	if(status == 1)
		$("divFechaCartaCump").style.display = "block";
	else
		$("divFechaCartaCump").style.display = "none";

}

function toggleDocBasic(id){

	if($("apDB_"+id).checked){
		Form.Element.enable("fechaRecDB_"+id);
		$("trigRDB_"+id).show();
		Form.Element.enable("descDB_"+id);
	}else{
		Form.Element.disable("fechaRecDB_"+id);
		$("trigRDB_"+id).hide();
		Form.Element.disable("descDB_"+id);
	}

}

num=0;
function crear(valor) {

  num++;
  fi = document.getElementById('fiel'); // 1
  contenedor = document.createElement('div'); // 2
  contenedor.id = 'div'+num; // 3
  fi.appendChild(contenedor); // 4

  ele = document.createElement('input'); // 5
  ele.type = 'text'; // 6
  ele.className = "smallInput medium";
  //ele.name = 'fil'+num; // 6
  ele.name = 'nom[]'; // 6
  ele.value = valor;
  contenedor.appendChild(ele); // 7

  ele = document.createElement('input'); // 5
  ele.type = 'button'; // 6
  ele.value = 'Borrar'; // 8
  ele.name = 'div'+num; // 8
  ele.onclick = function () {borrar(this.name)} // 9
  contenedor.appendChild(ele); // 7

}

function borrar(obj) {
  fi = document.getElementById('fiel'); // 1
  fi.removeChild(document.getElementById(obj)); // 10

}

function toggleSection(section, status){

	if(status == 1){

		$(section).style.display = "block";
		$(section+"H").style.display = "block";
		$(section+"S").style.display = "none";

		if($(section+"AR") != undefined)
			$(section+"AR").style.display = "block";

		if($(section+"CV") != undefined)
			$(section+"CV").style.display = "block";

	}else{

		$(section).style.display = "none";
		$(section+"H").style.display = "none";
		$(section+"S").style.display = "block";

		if($(section+"AR") != undefined)
			$(section+"AR").style.display = "none";

		if($(section+"CV") != undefined)
			$(section+"CV").style.display = "none";

	}
}

function addProrrogaDiv(id){

	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
	{
		method:'post',
		parameters: {action: "addProrrogaDiv", docGralId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('fviewclose'), "click", function(){ addProrrogaDiv(0); });
			Event.observe($('btnAddProrroga'), "click", function() { AddProrroga(id); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddProrroga(docGralId){

	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
	{
		method:'post',
		parameters: $('addProrrogaForm').serialize(true),
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";

			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "ok")
			{
				ShowStatusPopUp(splitResponse[1]);
				$('proContent').innerHTML = splitResponse[2];
				$("list_" + docGralId).innerHTML = splitResponse[3];
			}
			else
			{
				ShowStatusPopUp(splitResponse[1]);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteProrroga(id, idDocGral)
{
	var message = "Realmente deseas eliminar esta fecha?";
	if(!confirm(message))
	{
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
	{
		method:'post',
		parameters: {action: "deleteProrroga", k:id, docGralId:idDocGral},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");

			ShowStatusPopUp(splitResponse[1]);
			$('proContent').innerHTML = splitResponse[2];
			$("list_" + idDocGral).innerHTML = splitResponse[3];

		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function addDocsDiv(id){

	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
	{
		method:'post',
		parameters: {action: "addDocsDiv", docBasicId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('fviewclose'), "click", function(){ addDocsDiv(0); });
			Event.observe($('btnAddDocs'), "click",function() { AddDocs(id); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddDocs(id){

	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
	{
		method:'post',
		parameters: $('addDocsForm').serialize(true),
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";

			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "ok")
			{
				ShowStatusPopUp(splitResponse[1]);
				$('proContent').innerHTML = splitResponse[2];
				$('lstF_'+id).innerHTML = splitResponse[3];
				$('lstD_'+id).innerHTML = splitResponse[4];
			}
			else
			{
				ShowStatusPopUp(splitResponse[1]);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteDocs(id, idDocBasic)
{
	var message = "Realmente deseas eliminar este documento?";
	if(!confirm(message))
	{
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
	{
		method:'post',
		parameters: {action: "deleteDocs", k:id, docBasicId:idDocBasic},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");

			ShowStatusPopUp(splitResponse[1]);
			$('proContent').innerHTML = splitResponse[2];
			$('lstF_'+idDocBasic).innerHTML = splitResponse[3];
			$('lstD_'+idDocBasic).innerHTML = splitResponse[4];

		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function CB_ExternalFunctionCBClose(){

	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
	{
		method:'post',
		parameters: {action: "getDocsList"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");

			idDocBasic = splitResponse[0];

			$('lstF_'+idDocBasic).innerHTML = splitResponse[1];
			$('lstD_'+idDocBasic).innerHTML = splitResponse[2];
			$('lstA_'+idDocBasic).innerHTML = splitResponse[3];

		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function checkObligacion(obj, docGralId)
{
	if(obj.checked == false){
		if(confirm("Esta seguro de eliminar esta obligacion?")){
			$("fechaDG_" + docGralId).value = "";
			$("fechaRecDG_" + docGralId).value = "";
		}else{
			obj.checked = true;
		}
	}
}

jQ(document).on('change','.changeSelectedPermiso', function () {
	var id_split = jQ(this).val().split(',')
	jQ.ajax({
		 type: 'POST',
		 url: WEB_ROOT + '/ajax/load_items_select.php',
		 data: { type: 'loadSelectResponsable', id: id_split[1] },
		 dataType:' json',
		 success: function (response) {
			 response.forEach((res) => { jQ('#permiso_select_' + res.departament_id).val(res.departament_id + ',' + res.personal_id)})
		 }
		})
})

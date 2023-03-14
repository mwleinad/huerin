function ChangeTipo()
{
	loadUsoCfdi();
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
	loadUsoCfdi();
	var idContCat = $("contCatId")?.value;

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
				showRow("infoGral",9,true);
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

function showDateFirma(){

	var status = $("firma").value;

	if(status == "firmado")
		$("divFechaFirma").style.display = "block";
	else
		$("divFechaFirma").style.display = "none";

}

function showDateCobrado(){

	var status = $("cobrado").value;

	if(status == 1)
		$("divFechaCobrado").style.display = "block";
	else
		$("divFechaCobrado").style.display = "none";

}

function showDateInmEnt(){

	var status = $("inmuebleEntregado").value;

	if(status == 1)
		$("divFechaInmEnt").style.display = "block";
	else
		$("divFechaInmEnt").style.display = "none";

}

function showComments(){

	var status = $("comentarios").value;

	if(status == 1)
		$("divComments").style.display = "block";
	else
		$("divComments").style.display = "none";

}

function showCommentsC(){

	var status = $("comentariosC").value;

	if(status == 1)
		$("divCommentsC").style.display = "block";
	else
		$("divCommentsC").style.display = "none";

}

function showDateCertEnv(){

	var status = $("certEnviado").value;

	if(status == 1)
		$("divFechaCertEnv").style.display = "block";
	else
		$("divFechaCertEnv").style.display = "none";

}

function showDateCertEnvC(){

	var status = $("certEnviadoC").value;

	if(status == 1)
		$("divFechaCertEnvC").style.display = "block";
	else
		$("divFechaCertEnvC").style.display = "none";

}

function showPersona(){

	var status = $("persona").value;

	if(status == "f"){
		$("divPerFisica").style.display = "block";
		$("divPerMoral").style.display = "none";
	}else{
		$("divPerMoral").style.display = "block";
		$("divPerFisica").style.display = "none";
	}
}

function showPredial(){

	var status = $("predial").value;

	if(status == 1)
		$("divPredial").style.display = "block";
	else
		$("divPredial").style.display = "none";

}

function showAgua(){

	var status = $("agua").value;

	if(status == 1)
		$("divAgua").style.display = "block";
	else
		$("divAgua").style.display = "none";

}

function showFusionar(){

	var status = $("subdivInm").value;

	if(status == 1)
		$("divFusionar").style.display = "block";
	else
		$("divFusionar").style.display = "none";

}

function showFracc(){

	var status = $("fraccionamiento").value;

	if(status == 1)
		$("divFracc").style.display = "block";
	else
		$("divFracc").style.display = "none";

}

function showProyEsc(){

	var status = $("proyEscritura").value;

	if(status == 1 || status == 2)
		$("divProyEsc").style.display = "block";
	else
		$("divProyEsc").style.display = "none";

}

function showCalcIsr(){

	var status = $("calculoIsr").value;

	if(status == 3)
		$("divCalcIsr").style.display = "block";
	else
		$("divCalcIsr").style.display = "none";

}

function showCalcIva(){

	var status = $("calculoIva").value;

	if(status == 3)
		$("divCalcIva").style.display = "block";
	else
		$("divCalcIva").style.display = "none";

}

function showCheques(){

	var status = $("cheques").value;

	if(status == 2)
		$("divCheques").style.display = "block";
	else
		$("divCheques").style.display = "none";

}

function showCompVta(){

	var status = $("fechaCompVta").value;

	if(status == "firmado")
		$("divCompVta").style.display = "block";
	else
		$("divCompVta").style.display = "none";

}

function showImpWal(){

	var status = $("pagoImpWal").value;

	if(status == 1)
		$("divImpWal").style.display = "block";
	else
		$("divImpWal").style.display = "none";

}

function showImpNot(){

	var status = $("compPagoImpNot").value;

	if(status == 1)
		$("divImpNot").style.display = "block";
	else
		$("divImpNot").style.display = "none";

}

function showHonorarios(){

	var status = $("pagoHonorarios").value;

	if(status == 1)
		$("divHonorarios").style.display = "block";
	else
		$("divHonorarios").style.display = "none";

}

function showPagoPteWal(){

	var status = $("pagoPteWal").value;

	if(status == 1)
		$("divPagoPteWal").style.display = "block";
	else
		$("divPagoPteWal").style.display = "none";

}

function showEscRpp(){

	var status = $("escRpp").value;

	if(status == 1)
		$("divEscRpp").style.display = "block";
	else
		$("divEscRpp").style.display = "none";

}

function showCobradoC(){

	var status = $("cobradoC").value;

	if(status == 1)
		$("divCobradoC").style.display = "block";
	else
		$("divCobradoC").style.display = "none";

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
function crear(obj) {

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
			Event.observe($('btnAddDocs'), "click", function() { AddDocs(id); });
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

function loadUsoCfdi(){
	document.getElementById('filaUsoCfdi').style.display = '';
	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
		{
			method:'post',
			parameters: {action: "loadUsoCfdi",
				'regimen': document.getElementById('regimenId').value,
				'persona': document.getElementById('type').value},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				var splitResponse = response.split("[#]");

				idDocBasic = splitResponse[0];

				$('selectUsoCFDI').innerHTML = splitResponse[1];
			},
			onFailure: function(){ alert('Something went wrong...') }
		});

}
function changeTipoPersonaAlterno() {
	document.getElementById('alternativeRegimen').value = '';
	loadRegimenes();
	loadUsoCfdiAlternativo();
}
function loadRegimenes(){
	const persona = document.getElementById('alternativeType').value;
	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
		{
			method:'post',
			parameters: {
				action: "loadRegimen",
				persona,
				contractId: document.getElementById('contractId').value,
				alterno:1,
			},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				var splitResponse = response.split("[#]");

				idDocBasic = splitResponse[0];

				$('select-regimen-alterno').innerHTML = splitResponse[1];
			},
			onFailure: function(){ alert('Something went wrong...') }
		});
}
function loadUsoCfdiAlternativo(){
	const persona = document.getElementById('alternativeType').value;
	const regimen = document.getElementById('alternativeRegimen').value;
	new Ajax.Request(WEB_ROOT+'/ajax/contract-new.php',
		{
			method:'post',
			parameters: {
				action: "loadUsoCfdi",
				regimen,
				persona,
				contractId: document.getElementById('contractId').value,
				alterno:1,
			},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				var splitResponse = response.split("[#]");

				idDocBasic = splitResponse[0];

				$('select-uso-cfdi-alterno').innerHTML = splitResponse[1];
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


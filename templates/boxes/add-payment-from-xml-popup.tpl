<div class="popupheader" style="z-index:70">
	<div id="fviewmenu" style="z-index:70">
		<div id="fviewclose"><span style="color:#CCC" id="closePopUpDiv">Close<img src="{$WEB_ROOT}/images/b_disn.png" border="0" alt="close" /></span>
		</div>
	</div>
	<div id="ftitl">
		<div class="flabel">{$title}</div>
		<div id="vtitl">
			<span title="Titulo">{$title}
				<br />Serie y Folio: {$factura.folioComplete}
				<br />Saldo: $ <span id="mySaldoSpan">{$factura.saldo|number_format:2}</span>
			</span>
		</div>
	</div>
	<div id="draganddrop" style="position:absolute;top:45px;left:640px">
		<img src="{$WEB_ROOT}/images/draganddrop.png" border="0" alt="mueve" />
	</div>
</div>
<div class="wrapper">
	{include file="{$DOC_ROOT}/templates/forms/add-payment-from-xml.tpl"}
</div>

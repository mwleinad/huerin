<div class="popupheader" style="z-index:70">
	<div id="fviewmenu" style="z-index:70">
		<div id="fviewclose"><span style="color:#CCC" id="closePopUpDiv" onclick="close_popup()">Close<img src="{$WEB_ROOT}/images/b_disn.png" border="0" alt="close" /></span>
		</div>
	</div>
	<div id="ftitl">
		<div class="flabel">{$data.title}</div>
		<div id="vtitl"><span title="Titulo">{$data.title}</span></div>
	</div>
	<div id="draganddrop" style="position:absolute;top:45px;left:640px">
		<img src="{$WEB_ROOT}/images/draganddrop.png" border="0" alt="mueve" />
	</div>
</div>
<div class="wrapper">
	{include file="{$DOC_ROOT}/templates/forms/{$data.form}.tpl"}
</div>

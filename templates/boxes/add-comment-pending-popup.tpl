<div class="popupheader" style="z-index:70">
	<div id="fviewmenu" style="z-index:70">
		<div id="fviewclose"><span style="color:#CCC" id="closePopUpDiv" onclick="close_popup()">Close<img src="{$WEB_ROOT}/images/b_disn.png" border="0" alt="close" /></span>
		</div>
	</div>
	<div id="ftitl">
		<div class="flabel">Comentarios</div>
		<div id="vtitl"><span title="Titulo">Comentarios</span></div>
	</div>
	<div id="draganddrop" style="position:absolute;top:45px;left:640px">
		<img src="{$WEB_ROOT}/images/draganddrop.png" border="0" alt="mueve" />
	</div>
</div>
<div class="wrapper">
	{include file="{$DOC_ROOT}/templates/forms/form-add-comment-pending.tpl"}
	<div class="clearfix"></div>
	<div id="commentsPending">
		{include file="{$DOC_ROOT}/templates/lists/comments-pending.tpl"}
	</div>

</div>

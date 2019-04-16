<div id="divForm">
	<form id="{$data.nameForm}" name="{$data.nameForm}" method="post" onsubmit="return false">
		<input type="hidden" id="type" name="type" value="saveCommentPending"/>
		<input type="hidden" id="changeId" name="changeId" value="{$post.id}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Comentario:</div>
				<div style="width:70%;display:inline-block">
					<textarea name="comment" id="comment" class="largeInput medium"></textarea>
				</div>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<div style="display:inline-block;text-align: center;">
					<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
					<a class="button_grey" id="btnComment" name="btnComment"><span>Guardar</span></a>
				</div>
			</div>
		</fieldset>
	</form>
</div>

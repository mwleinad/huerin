<div id="divForm">
	<form id="frmGetSalario" name="frmGetSalario" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		<input type="hidden" id="type" name="type" value="getSalario"/>
		<fieldset>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:10%;float:left">* Empleado</div>
				<div style="width:70%;float: left;">
					<select name="personalId" id="personalId" class="largeInput">
					{foreach from=$empleados key=key item=item}
						<option value="{$item.personalId}">{$item.name}</option>
					{/foreach}
					</select>
				</div>
				<hr>
			</div>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:10%;float:left">* Folio</div>
				<div style="width:70%;float: left;">
					<input type="checkbox" name="deep" id="deep"  class="xsmallIn"/>
				</div>
				<hr />
			</div>
			<div class="formLine" style="text-align:center">
				<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
				<input type="submit" id="btnGetSalario" value="Obtener" class="btn" />
			</div>
		</fieldset>
	</form>
</div>

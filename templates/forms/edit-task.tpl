<div id="divForm">
	<form id="editTaskForm" name="editTaskForm" method="post">
	<input type="hidden" id="type" name="type" value="saveEditTask"/>
	<input type="hidden" id="taskId" name="taskId" value="{$post.taskId}"/>
	<input type="hidden" id="servicioId" name="servicioId" value="{$servicioId}"/>
		<fieldset>

			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre:</div>
         		<div style="width:30%;float:left"><input  name="nombreTask" id="nombreTask" type="text" value="{$post.nombreTask}" size="27" class="largeInput medium"/> </div>
				<hr />
        </div>		
            
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">*Dia de Vencimiento:</div>
         		<div style="width:30%;float:left"><input  name="diaVencimiento" id="diaVencimiento" type="text" value="{$post.diaVencimiento}" size="27" class="largeInput medium"/> </div>
				<hr />
			</div>

            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">*Dias de Prorroga:</div>
         		<div style="width:30%;float:left"><input  name="prorroga" id="prorroga" type="text" value="{$post.prorroga}" size="27" class="largeInput medium"/> </div>
				<hr />
			</div>

            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">*Control Uno (Obligatorio):</div>
        		 <div style="width:30%;float:left"><input  name="control" id="control" type="text" value="{$post.control}" size="27" class="largeInput medium"/> </div>
				<hr />
			</div>
			<div class="formLine" style="width:100%;height: auto;vertical-align: middle;display: table;" >
				<div style="display: table-cell;width: 30%;vertical-align: middle;">Extension de archivo permitido(puede marcar varios)</div>
				<div style="display: table-cell;width: 70%;vertical-align: middle;">
					<table>
						<tr><td><input type="checkbox" id="check_all" name="check_all" {if $all_checked}checked{/if}></td><td>Todos</td></tr>
						{assign var='ncol' value=4}
						{foreach from=$extensiones key=kexp item=exp}
							{if $ncol eq 4}
								<tr><td><input type="checkbox" name="extensiones[]" id="extensiones" value="{$exp.extension}" {if $exp.permitido}checked{/if} ></td><td>{$exp.name}</td>
								{assign var='ncol' value=$ncol-1}
							{else}
								<td><input type="checkbox" name="extensiones[]"  id="extensiones" value="{$exp.extension}" {if $exp.permitido}checked{/if} ></td> <td>{$exp.name}</td>
								{assign var='ncol' value=$ncol-1}
								{if $ncol eq 0}
									{assign var='ncol' value=4}
									</tr>
								{/if}
							{/if}
						{/foreach}
					</table>
				</div>

				<hr />
			</div>

			<div style="clear:both"></div>
			<div class="actionPopup">
				<span class="msjRequired">* Campos requeridos </span><br>
				<div class="actionsChild">
					<img  src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
				</div>
				<div class="actionsChild">
					<div style="display: inline-block"> <a class="button" id="btnEditTask" name="btnEditTask" title="Actualizar"><span>Actualizar</span></a></div>
				</div>
			</div>
		</fieldset>
	</form>
</div>

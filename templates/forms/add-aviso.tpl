<div id="divForm">
	<form id="addNoticeForm" name="addNoticeForm" method="post" onsubmit="return false;">
  
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left"> * Aviso:</div>
                 <textarea  id="descripcion" name="descripcion" class="smallInput" rows="8" cols="50"></textarea>
             <hr />
			</div>
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Prioridad:</div>
                 <select id="prioridad" name="prioridad" class="smallInput">
					 <option value="normal">Normal</option>
                 <option value="importante">Importante</option>
                 </select>
             <hr />
			</div>
            
			<div class="formLine" style="width:100%; text-align:left">
			  <div style="width:30%;float:left">Archivo:</div>
        		<input type="file" name="path" id="path" value="" size="50"  class="smallInput"/>
             <hr />
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="display: inline-block">* Visualizar para las siguientes areas: </div>
				<br><br>
				<div style="display: inline-block;">
					<table>
                        {assign var='ncol' value=3}
                        {foreach from=$roles key=key item=item}
                            {if $ncol eq 3}
								<tr>
								<td style="vertical-align: top">
									<input type="checkbox" name="dep-{$item.departamentoId}[]" id="father-{$item.departamentoId}"  value="{$item.departamentoId}" checked>
								</td>
								<td style="vertical-align: top">{$item.departamento}
								    <ul>
                                        {foreach from=$item.roles item=rol key=krol}
											<li style="list-style: none"><input type="checkbox" name="roles-{$item.departamentoId}[]" id="child_{$rol.name|substr:0:5}" class="child-{$item.departamentoId}" value="{$rol.rolId}" checked>{$rol.name}</li>
                                        {/foreach}
									</ul>
							    </td>

                                {assign var='ncol' value=$ncol-1}
                            {else}
								<td style="vertical-align: top">
									<input type="checkbox" name="dep-{$item.departamentoId}[]" id="father-{$item.departamentoId}"  value="{$item.departamentoId}" checked>
								</td>
								<td style="vertical-align: top">{$item.departamento}
									<ul>
                                        {foreach from=$item.roles item=rol key=krol}
											<li style="list-style: none"><input type="checkbox" name="roles-{$item.departamentoId}[]" id="child_{$rol.name|substr:0:5}" class="child-{$item.departamentoId}"  value="{$rol.rolId}" checked>{$rol.name}</li>
                                        {/foreach}
									</ul>
								</td>
                                {assign var='ncol' value=$ncol-1}
                                {if $ncol eq 0}
                                    {assign var='ncol' value=3}
									</tr>
                                {/if}
                            {/if}
                        {/foreach}
						{if $User.tipoPers eq 'Socio'||$User.tipoPers eq 'Admin' ||$User.tipoPers eq 'Coordinador'}
						<tr>
							<td style="vertical-align: top">
								<input type="checkbox" name="sendCustomer" value="">
							</td>
							<td style="vertical-align: top">
								Enviar a cliente
							</td>
						</tr>
						{/if}
					</table>
				</div>
				<hr />
			</div>
			<div style="clear:both"></div>
			 * Campo requerido	
      			<div align="center" id="load" style="display:none; padding-bottom:5px">
	    			<img src="{$WEB_ROOT}/images/loading.gif" width="16" height="16" />
         			<br />Cargando...
      			</div>	
     		<div class="formLine" style="text-align:center; margin-left:300px" id="btnSaveNot">            
            <a class="button_grey" id="saveNotice"><span>Agregar</span></a>                       
     		</div>
        <input type="hidden" id="type" name="type" value="saveAddNotice"/>
        <input type="hidden" id="userId" name="userId" value="{$userId}"/>
        <input type="hidden" id="noticeId" name="noticeId" value="{$noticeId}"/>
         <input type="hidden" id="usuario" name="usuario" value="{$usuario}"/>  
		</fieldset>
	</form>
</div>

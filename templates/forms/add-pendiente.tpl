<div id="divForm">
	<form id="addPendienteForm" name="addPendienteForm" method="post" action="{$WEB_ROOT}/homepage" enctype="multipart/form-data" action="">
  
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left"> * Descripcion:</div>
                 <textarea  id="descripcion" name="descripcion" class="smallInput" rows="8" cols="50"></textarea>
             <hr />
			</div>
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Prioridad:</div>
                 <select id="prioridad" name="prioridad" class="smallInput">
                 <option value="">Seleccione...</option>
                 <option value="importante">Importante</option>
                 <option value="normal">Normal</option>
                 </select>
             <hr />
			</div>

            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Responsable:</div>
                 <select id="responsable" name="responsable" class="smallInput">
                 <option value="">Seleccione...</option>
									{foreach from=$usuarios item=item}
                 <option value="{$item.personalId}">{$item.name}</option>
                 {/foreach}
                 </select>
             <hr />
			</div>
            
			<div style="clear:both"></div>
			 * Campo requerido	
      			<div align="center" id="load" style="display:none; padding-bottom:5px">
	    			<img src="{$WEB_ROOT}/images/loading.gif" width="16" height="16" />
         			<br />Cargando...
      			</div>	
     		<div class="formLine" style="text-align:center; margin-left:300px" id="btnSaveNot">            
            <a class="button_grey" onclick="AddPendiente()"><span>Agregar</span></a>                       
     		</div>
        <input type="hidden" id="type" name="type" value="saveAddPendiente"/>
        <input type="hidden" id="userId" name="userId" value="{$userId}"/>
        <input type="hidden" id="noticeId" name="noticeId" value="{$noticeId}"/>
         <input type="hidden" id="usuario" name="usuario" value="{$usuario}"/>  
		</fieldset>
	</form>
</div>

<div id="divForm">
	<form id="editTipoServicioForm" name="editTipoServicioForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Nombre del Servicio:</div>
                <input name="nombreServicio" id="nombreServicio" type="text" value="{$post.nombreServicio}" class="smallInput" size="50"/>
                <hr />
			</div>

 			<div class="formLine" style="width:100%; text-align:left">
              <div style="width:30%;float:left">Periodicidad:</div>
              <select name="periodicidad" id="periodicidad" class="smallInput medium">
                <option value="Mensual" {if $post.periodicidad == "Mensual"} selected="selected"{/if}>Mensual</option>
                <option value="Bimestral" {if $post.periodicidad == "Bimestral"} selected="selected"{/if}>Bimestral</option>
                <option value="Trimestral" {if $post.periodicidad == "Trimestral"} selected="selected"{/if}>Trimestral</option>
                <option value="Semestral" {if $post.periodicidad == "Semestral"} selected="selected"{/if}>Semestral</option>
                <option value="Anual" {if $post.periodicidad == "Anual"} selected="selected"{/if}>Anual</option>
                <option value="Eventual" {if $post.periodicidad == "Eventual"} selected="selected"{/if}>Eventual</option>
              </select>
              <hr />       
            </div>   
               
            <div class="formLine" style="width:100%; text-align:left"  id="departamentoDiv">
                 <div style="width:30%;float:left">Departamento:</div>
                 <select name="departamentoId" id="departamentoId"  class="smallInput medium">
                    <option value="0">Seleccione...</option>
                    {foreach from=$departamentos item=departamento}
                    <option value="{$departamento.departamentoId}" {if $departamento.departamentoId == $post.departamentoId} selected="selected"{/if}>{$departamento.departamento}</option>
                  {/foreach}
                 </select>
                 <hr />       
              </div>
             
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Costo: <br /><i>** solo informativo **</i></div>
                <input name="costoVisual" id="costoVisual" type="text" value="{$post.costoVisual}" class="smallInput" size="50"/>
                <hr />     
			</div>
            
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Mostrar Costo Informativo:</i></div>
                <input name="mostrarCostoVisual" id="mostrarCostoVisual" type="checkbox" value="1" class="smallInput" {if $post.mostrarCostoVisual == "1"}checked{/if} />

			</div>
              
			<div style="clear:both"></div>
			<hr />
			            
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="editTipoServicio"><span>Actualizar</span></a>           
            </div>
      
			<input type="hidden" id="type" name="type" value="saveEditTipoServicio"/>
			<input type="hidden" id="tipoServicioId" name="tipoServicioId" value="{$post.tipoServicioId}"/>
		</fieldset>
	</form>
</div>

<div id="divForm">
	<form id="addTipoServicioForm" name="addTipoServicioForm" method="post">
			<input type="hidden" id="type" name="type" value="saveAddTipoServicio"/>
			<input type="hidden" id="tipoServicioId" name="tipoServicioId" value="{$post.tipoServicioId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre del Servicio:</div>
                <input name="nombreServicio" id="nombreServicio" type="text" value="{$post.nombreServicio}" class="smallInput" size="50"/>
                <hr />     
			</div>

            <div class="formLine" style="width:100%; text-align:left">
              <div style="width:30%;float:left">Periodicidad:</div>
              <select name="periodicidad" id="periodicidad" class="smallInput medium">
                <option value="Mensual">Mensual</option>
                <option value="Bimestral">Bimensual</option>
                <option value="Trimestral">Trimestral</option>
                <option value="Semestral">Semestral</option>
                <option value="Anual">Anual</option>
                <option value="Eventual">Eventual</option>
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
                <input name="costoVisual" id="costoVisual" type="text" value="" class="smallInput" size="50"/>
                <hr />     
			</div>
            
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Mostrar Costo Informativo:</i></div>
                <input name="mostrarCostoVisual" id="mostrarCostoVisual" type="checkbox" value="1" class="smallIn2" />
                <hr>
			</div>
            <div class="formLine" style="width:100%; text-align:left" >
                <div style="width:30%;float:left">* Clave SAT:</div>
                <input name="claveSat" id="claveSat" type="text" value="" class="smallIn2" />
            </div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="addTipoServicioButton"><span>Agregar</span></a>           
            </div>
            
		</fieldset>
	</form>
</div>

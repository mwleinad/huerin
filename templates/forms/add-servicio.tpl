<div id="divForm">
	<form id="addServicioForm" name="addServicioForm" method="post">
	<input type="hidden" id="type" name="type" value="saveAddServicio"/>
	<input type="hidden" id="contractId" name="contractId" value="{$contractId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Servicio:</div>
               <select name="tipoServicioId" id="tipoServicioId" class="smallInput medium" onchange="UpdateCosto()">
               <option value="0">Seleccione...</option>
               {foreach from=$tiposDeServicio item=item}
               <option {if $item.tipoServicioId == $post.tipoServicioId} selected="selected"{/if}value="{$item.tipoServicioId}">{$item.nombreServicio}</option>
               {/foreach}
               </select>
				<hr />
            </div>		

			<div class="formLine" style="width:100%; text-align:left">
					<div style="width:30%;float:left">* Fecha Inicio Operaciones:</div>
					<input style="width:20%!important;float:left" class="smallInput medium" name="inicioOperaciones" id="inicioOperaciones"  onclick="CalendarioSimple(this)" type="text" value="{$post.inicioOperacionesMysql}"/>
					<hr />
			</div>
            
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Inicio Factura:</div>
        	    <input style="width:20%!important;float:left" class="smallInput medium" onclick="CalendarioSimple(this)" name="inicioFactura" id="inicioFactura" type="text" value="{$post.inicioFacturaMysql}" size="27"/>
                <hr />
			</div>

            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Costo:</div>
                <input class="smallInput medium" name="costo" id="costo" type="text" value="{$post.costo}" size="50"/>
                <hr />
			</div> 
			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="addServiceButton"><span>Agregar</span></a>           
            </div>			
		</fieldset>
	</form>
</div>

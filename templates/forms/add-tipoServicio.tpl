<div id="divForm">
	<form id="addTipoServicioForm" name="addTipoServicioForm" method="post">
			<input type="hidden" id="type" name="type" value="saveAddTipoServicio"/>
			<input type="hidden" id="tipoServicioId" name="tipoServicioId" value="{$post.tipoServicioId}"/>
		<fieldset>
            <div class="container_16">
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:30%;float:left"> * Nombre del servicio</div>
                        <div style="width:70%;float: left;">
                            <input name="nombreServicio" id="nombreServicio" type="text" value="{$post.nombreServicio}" class="largeInput"/>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:30%;float:left">Periodicidad:</div>
                        <div style="width:70%;float: left;">
                            <select name="periodicidad" id="periodicidad" class="largeInput">
                                <option value="Mensual">Mensual</option>
                                <option value="Bimestral">Bimensual</option>
                                <option value="Trimestral">Trimestral</option>
                                <option value="Cuatrimestral">Cuatrimestral</option>
                                <option value="Semestral">Semestral</option>
                                <option value="Anual">Anual</option>
                                <option value="Eventual">Eventual</option>
                            </select>
                        </div>
                    </div>
                    <hr />
                </div>
                <div class="grid_16">
                    <div class="formLine" style="width: 100%; display: inline-block;">
                        <div style="width:30%;float:left">Departamento:</div>
                        <div style="width:70%;float: left;">
                            <select name="departamentoId" id="departamentoId"  class="largeInput">
                                <option value="0">Seleccione...</option>
                                {foreach from=$departamentos item=departamento}
                                    <option value="{$departamento.departamentoId}" {if $departamento.departamentoId == $post.departamentoId} selected="selected"{/if}>{$departamento.departamento}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <hr />
                </div>
            </div>
            <div class="grid_16">
                <div class="formLine" style=" width:100%;display: inline-block;">
                    <div style="width:30%;float:left"> * Costo (<b>Solo informativo</b>)</div>
                    <div style="width:20%;float: left;">
                        <input name="costoVisual" id="costoVisual" type="text" value="" class="largeInput"/>
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16">
                <div class="formLine" style=" width:100%;display: inline-block;">
                    <div style="width:30%;float:left"> * Mostrar Costo Informativo:</div>
                    <div style="width:20%;float: left;">
                        <input name="mostrarCostoVisual" id="mostrarCostoVisual" type="checkbox" value="1" class="largeInput" />
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16">
                <div class="formLine" style=" width:100%;display: inline-block;">
                    <div style="width:30%;float:left"> * Clave SAT:</div>
                    <div style="width:20%;float: left;">
                        <input name="claveSat" id="claveSat" type="text" value="" class="largeInput" />
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16">
                <div class="formLine" style=" width:100%;display: inline-block;">
                    <div style="width:30%;float:left"> Heredar pasos y tareas del siguiente servicio:</div>
                    <div style="width:70%;float: left;">
                        <select name="inheritanceId" id="inheritanceId" class="largeInput">
                            <option value="">---Seleccionar---</option>
                            {foreach from=$servicios item=item key=key}
                                <option value="{$item.tipoServicioId}">{$item.nombreServicio}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16" id="stepTask"></div>
			<div style="clear:both"></div>
			<div class="formLine" style="text-align:center; margin-left:300px">
                <a class="button_grey" id="addTipoServicioButton"><span>Agregar</span></a>
            </div>
		</fieldset>
	</form>
</div>

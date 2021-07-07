<div id="divForm">
	<form id="frmServicio" name="frmServicio" method="post">
        <input type="hidden" name="type" id="type" value="{if $post}saveEditTipoServicio{else}saveAddTipoServicio{/if}" />
        <input type="hidden" name="tipoServicioId" id="tipoServicioId" value="{$post.tipoServicioId}" />
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
                                <option value="Mensual" {if $post.periodicidad == "Mensual"} selected="selected"{/if}>Mensual</option>
                                <option value="Bimestral" {if $post.periodicidad == "Bimestral"} selected="selected"{/if}>Bimestral</option>
                                <option value="Trimestral" {if $post.periodicidad == "Trimestral"} selected="selected"{/if}>Trimestral</option>
                                <option value="Cuatrimestral" {if $post.periodicidad == "Cuatrimestral"} selected="selected"{/if}>Cuatrimestral</option>
                                <option value="Semestral" {if $post.periodicidad == "Semestral"} selected="selected"{/if}>Semestral</option>
                                <option value="Anual" {if $post.periodicidad == "Anual"} selected="selected"{/if}>Anual</option>
                                <option value="Eventual" {if $post.periodicidad == "Eventual"} selected="selected"{/if}>Eventual</option>
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
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:30%;float:left"> Costo (<b>Solo informativo</b>)</div>
                        <div style="width:20%;float: left;">
                            <input name="costoVisual" id="costoVisual" type="text" value="{$post.costoVisual}" class="largeInput"/>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:30%;float:left"> Mostrar Costo Informativo:</div>
                        <div style="width:20%;float: left;">
                            <input name="mostrarCostoVisual" id="mostrarCostoVisual" type="checkbox" value="1" class="largeInput" {if $post.mostrarCostoVisual == "1"}checked{/if}/>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:30%;float:left"> Generar factura una sola vez:</div>
                        <div style="width:20%;float: left;">
                            <input name="uniqueInvoice" id="uniqueInvoice" type="checkbox" class="largeInput" {if $post.uniqueInvoice}checked{/if}/>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:30%;float:left"> * Clave SAT:</div>
                        <div style="width:20%;float: left;">
                            <input name="claveSat" id="claveSat" type="text" value="{$post.claveSat}" class="largeInput" />
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:30%;float:left"> Archivo layout para cotizaciones:</div>
                        <div style="width:70%;float: left;">
                            <input type="file" id="template" name="template" class="largeInput" />
                            {if $post.template neq ""}
                                <span>Ya se encuentra un archivo registrado</span>
                                <a href="{$post.template}" title="Descargar archivo" >
                                    Archivo actual <img  src="{$WEB_ROOT}/images/icons/iconDown.png"/>
                                </a>
                                <span>Adjunte nuevo archivo si desea actualizar.</span>
                                <span style="color:orange">Unicamente se permite archivos con extension .DOCX </span>
                            {/if}
                        </div>
                    </div>
                    <hr>
                </div>
                {if $post.tasks|count <= 0}
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
                {/if}
                <div class="grid_16" id="stepTask"></div>
            </div>
		</fieldset>
        <div class="actionPopup">
            <span class="msjRequired">* Campos requeridos </span><br>
            <div class="actionsChild">
                <img  src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading"/>
            </div>
            <div class="actionsChild">
                <a  href="javascript:;" class="button_grey spanSaveService">
                    <span>{if $post}Actualizar{else}Guardar{/if}</span>
                </a>
            </div>
        </div>
	</form>
</div>

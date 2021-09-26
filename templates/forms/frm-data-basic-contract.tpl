<div id="idForm">
    <fieldset>
        <div class="container_16">
            <div class="grid_16">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left"> * Tipo de persona</div>
                        <div style="width:100%;float: left;">
                            <select name="tax_purpose" id="tax_purpose" class="largeInput">
                                <option value="">Seleccionar...</option>
                                <option value="fisica" {if $post.tax_purpose eq 'fisica' || $post.contract.type eq 'Persona Fisica'}
                                                        selected{/if}>Persona Fisica</option>
                                <option value="moral"  {if $post.tax_purpose eq 'moral' || $post.contract.type eq 'Persona Moral'}
                                                        selected{/if}>Personal Moral</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="grid_18">
                    <div class="grid_8">
                        <div class="formLine" style="width:100%;  display: inline-block;">
                            <div style="width:100%;float:left"> * Facturador</div>
                            <div style="width:100%;float: left;">
                                <select name="facturador" id="facturador" class="largeInput">
                                    < <option value="">Seleccionar...</option>
                                    <option value="Efectivo" {if $post.contract.facturador == "Efectivo"} selected="selected" {/if} >Efectivo</option>
                                    {foreach from=$emisores  item=item key=key}
                                        <option value="{$item.claveFacturador}" {if $item.claveFacturador == $post.contract.facturador} selected="selected" {/if} >{$item.razonSocial}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <hr />
            </div>
            <div class="grid_16">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left"> * Nombre o Razon social</div>
                        <div style="width:100%;float: left;">
                            <input type="text" class="largeInput" name="name" id="name" value="{if $post.contract}{$post.contract.name}{else}{$post.name}{/if}"
                             {if $post.contract}readonly{/if}
                            />
                        </div>
                    </div>
                </div>
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left"> * RFC</div>
                        <div style="width:100%;float: left;">
                            <input type="text" class="largeInput" name="rfc" id="rfc" value="{if $post.contract}{$post.contract.rfc}{else}{$post.taxpayer_id}{/if}"
                                   {if $post.contract}readonly{/if} />
                        </div>
                    </div>
                </div>
                <hr />
            </div>
            <div class="grid_16">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left"> * Regimen fiscal</div>
                        <div style="width:100%;float: left;">
                            <input type="hidden" class="largeInput" name="regimen_id" id="regimen_id"
                                   value="{if $post.contract}{$post.contract.regimenId}{else}{$post.regimen_id}{/if}"/>
                        </div>
                    </div>
                </div>
                <div class="grid_8">
                    <div class="formLine field_moral" style="width:100%;  display:{if $post.tax_purpose eq 'moral' || $post.contract.type eq 'Persona Moral'}
                            inline-block{else}none{/if}">
                        <div style="width:100%;float:left"> Tipo de sociedad</div>
                        <div style="width:100%;float: left;">
                            <select class="largeInput" name="sociedadId" id="sociedadId">
                                <option value="">--- Seleccionar --</option>
                                {foreach from=$sociedades item=item}
                                    <option value="{$item.sociedadId}" {if $post.contract.sociedadId == $item.sociedadId} selected="selected" {/if}>{$item.nombreSociedad}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <hr />
            </div>
            <div class="grid_16">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">Nombre representante legal</div>
                        <div style="width:100%;float:left;">
                            <input type="text" class="largeInput" name="legal_representative" id="legal_representative"
                                   value="{if $post.contract}{$post.contract.nameRepresentanteLegal}
                                          {else}{$post.legal_representative}{/if}"
                                   {if $post.contract}readonly{/if}/>
                        </div>
                    </div>
                </div>
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">* Metodo de pago</div>
                        <div style="width:100%;float:left;">
                            <select name="metodoDePago" id="metodoDePago" class="largeInput">
                                <option value="" {if $post.contract.metodoDePago eq '12'}selected{/if}>--- Seleccionar ---</option>
                                {foreach from=$metodoPagos item=item}
                                    <option value="{$item.c_FormaPago}"
                                            {if $post.contract.metodoDePago eq $item.c_FormaPago}selected{/if}>{$item.descripcion}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16">
                <div class="grid_16">
                    <label style="font-weight:normal">Actividad Comercial</label>
                </div>
                <div class="grid_6">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">Sector</div>
                        <div style="width:100%;float:left;">
                            <select class="largeInput select2" name="sector" id="sector">
                                <option value="">Seleccionar..</option>
                                {foreach from=$sectores  item=sector key=key}
                                    <option value="{$sector.id}"
                                            {if $post.contract.sector_id eq $sector.id || $post.sector_id eq $sector.id}selected{/if}>{$sector.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="grid_5">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left"> Subsector</div>
                        <div style="width:100%;float:left;">
                            <select class="largeInput select2" name="subsector" id="subsector">
                                <option value="">Seleccionar..</option>
                                {foreach from=$subsectores  item=subsector key=key}
                                    <option value="{$subsector.id}"
                                            {if $post.contract.subsector_id eq $subsector.id}selected{/if}>{$subsector.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="grid_5">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">Actividad comercial</div>
                        <div style="width:100%;float:left;">
                            <select class="largeInput select2" name="actividad_comercial" id="actividad_comercial">
                                <option value="">Seleccionar..</option>
                                {foreach from=$actividades_comerciales  item=actividad key=key}
                                    <option value="{$actividad.id}"
                                            {if $post.contract.ac_id eq $actividad.id}selected{/if}>{$actividad.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <hr />
            </div>
            <div class="grid_16">
                <div class="grid_16"><h6>Direccion fiscal</h6></div>
                <div class="grid_4">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">* Calle</div>
                        <div style="width:100%;float:left;">
                            <input type="text" class="largeInput" name="address" id="address"
                                   value="{if $post.contract}{$post.contract.address}{/if}"/>
                        </div>
                    </div>
                </div>
                <div class="grid_4">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">No Exterior</div>
                        <div style="width:100%;float:left;">
                            <input type="text" class="largeInput" name="noExtAddress" id="noExtAddress"
                                   value="{if $post.contract}{$post.contract.noExtAddress}{/if}"/>
                        </div>
                    </div>
                </div>
                <div class="grid_4">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">No Interior</div>
                        <div style="width:100%;float:left;">
                            <input type="text" class="largeInput" name="noIntAddress" id="noIntAddress"
                                   value="{if $post.contract}{$post.contract.noIntAddress}{/if}"/>
                        </div>
                    </div>
                </div>
                <div class="grid_4">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">* Colonia</div>
                        <div style="width:100%;float:left;">
                            <input type="text" class="largeInput" name="coloniaAddress" id="coloniaAddress"
                                   value="{if $post.contract}{$post.contract.coloniaAddress}{/if}"/>
                        </div>
                    </div>
                </div>
                <div class="grid_4">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">* Municipio</div>
                        <div style="width:100%;float:left;">
                            <input type="text" class="largeInput" name="municipioAddress" id="municipioAddress"
                                   value="{if $post.contract}{$post.contract.municipioAddress}{/if}"/>
                        </div>
                    </div>
                </div>
                <div class="grid_4">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">* Estado</div>
                        <div style="width:100%;float:left;">
                            <input type="text" class="largeInput" name="estadoAddress" id="estadoAddress"
                                   value="{if $post.contract}{$post.contract.estadoAddress}{/if}"/>
                        </div>
                    </div>
                </div>
                <div class="grid_4">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">* Pais</div>
                        <div style="width:100%;float:left;">
                            <input type="text" class="largeInput" name="paisAddress" id="paisAddress"
                                   value="{if $post.contract}{$post.contract.paisAddress}{/if}"/>
                        </div>
                    </div>
                </div>
                <div class="grid_4">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">* Código Postal</div>
                        <div style="width:100%;float:left;">
                            <input type="text" class="largeInput" name="cpAddress" id="cpAddress"
                                   value="{if $post.contract}{$post.contract.cpAddress}{/if}"/>
                        </div>
                    </div>
                </div>
                <hr/>
            </div>
            <div class="grid_16">
                <div class="grid_16">
                    <label style="font-weight:normal">Dirección Comercial(papeleria)</label>
                </div>
                <div class="grid_16">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left;">
                           <textarea class="largeInput" name="direccionComercial" id="direccionComercial">{$post.contract.direccionComercial}</textarea>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16">
               <div class="grid_16"><h6>Responsables de departamento</h6></div>
                {foreach from=$departamentos item=departament}
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div class="grid_8">Responsable {$departament.departamento}:</div>
                        <div class="grid_8">
                            <select name="permisos[]" id="permiso_select_{$departament.departamentoId}"
                                    class="largeInput select_permiso {if $departament.departamentoId eq 1}changeSelectedPermiso{/if}">
                                <option value="">Seleccionar......</option>
                                {foreach from=$departament_responsable[$departament.departamentoId] item=itemDep}
                                    <option value="{$departament.departamentoId},{$itemDep.id}"
                                            {if $post.contract.current_responsable[$departament.departamentoId] eq $itemDep.id}selected{/if}>{$itemDep.name|strtoupper}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <hr>
                {/foreach}
            </div>
        </div>
    </fieldset>
</div>


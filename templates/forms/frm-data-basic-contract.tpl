<div id="idForm">
    <fieldset>
        <div class="container_16">
            <div class="grid_16">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left"> * Tipo de persona</div>
                        <div style="width:100%;float: left;">
                            <select type="text" name="tax_purpose" id="tax_purpose" class="largeInput">
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
                                <select name="facturador" id="facturador" class="largeInput medium">
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
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left"> * Clasificacion</div>
                        <div style="width:100%;float: left;">
                            <select name="qualification" id="qualification" class="largeInput">
                                <option value="">Seleccione....</option>
                                <option value="AAA" {if $post.contract.qualification eq 'AAA'}selected{/if}>Buena</option>
                                <option value="AA"  {if $post.contract.qualification eq 'AA'}selected{/if}>Regular</option>
                                <option value="A"   {if $post.contract.qualification eq 'A'}selected{/if}>Mala</option>
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
                <hr />
            </div>
            <div class="grid_16">
                <h6>Direccion fiscal</h6>
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
                        <div style="width:100%;float:left">* Codigo Postal</div>
                        <div style="width:100%;float:left;">
                            <input type="text" class="largeInput" name="cpAddress" id="cpAddress"
                                   value="{if $post.contract}{$post.contract.cpAddress}{/if}"/>
                        </div>
                    </div>
                </div>
                <hr/>
            </div>
            <div class="grid_16">
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
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">* Dirección comercial </div>
                        <div style="width:100%;float:left;">
                            <input type="text" class="largeInput" name="direccionComercial" id="direccionComercial"
                                   value="{if $post.contract}{$post.contract.direccionComercial}{/if}"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid_16">
                <div class="grid_8">
                </div>
            </div>
        </div>
    </fieldset>
</div>


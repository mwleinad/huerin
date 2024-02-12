<div id="divForm">
    <form id="{$data.nameForm}" name="{$data.nameForm}" method="post" autocomplete="off">
        <input type="hidden" name="valur" id="valur" value="{$valur}"/>
        <input type="hidden" name="tipo" id="tipo" value="{$tipo}"/>
        <fieldset>
            <div class="grid_16">
                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:30%;float:left">* Nombre del Directivo:</div>
                    <input class="largeInput medium" name="nameContact" id="nameContact" type="text"
                           value="{$post.nameContact}" size="50"/>
                    <hr/>
                </div>
                <div class="formLine field_list_partner" style="width:100%; text-align:left;">
                    <div style="width:30%;float:left">Clasificación cliente</div>
                    <select class="largeInput medium" name="tipo_clasificacion_cliente_id" id="tipo_clasificacion_cliente_id">
                        <option value="">---Seleccionar---</option>
                        {foreach from=$clasificaciones item=item key=key}
                            <option value="{$item.id}" {if $post.tipo_clasificacion_cliente_id eq $item.id}selected{/if}>{$item.nombre}</option>
                        {/foreach}
                    </select>
                    <hr />
                </div>

                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:30%;float:left">Tel&eacute;fono Contacto Directivo:</div>
                    <input class="largeInput medium" name="phone" id="phone" type="text" value="{$post.phone}" size="50"/>
                    <hr/>
                </div>
                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:30%;float:left">Email Contacto Directivo:</div>
                    <input class="largeInput medium" name="email" id="email" type="text" value="{$post.email}" size="50"/>
                    <hr/>
                </div>

                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:30%;float:left">Password:</div>
                    <input class="largeInput medium" name="password" id="password" type="text" value="{$post.password}"
                           size="50"/>
                    <hr/>
                </div>
                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:30%;float:left">* Cliente referido:</div>
                    <select class="largeInput medium field_referred" name="is_referred" id="is_referred">
                        <option value="0" {if $post.is_referred eq '0'}selected{/if}>No</option>
                        <option value="1" {if $post.is_referred eq '1'}selected{/if}>Si</option>
                    </select>
                    <hr />
                </div>
                <div class="formLine field_type_referred" style="width:100%; text-align:left;
                        display: {if $post.is_referred eq '1'}block{else}none{/if};" >
                    <div style="width:30%;float:left">* Referido por</div>
                    <select class="largeInput medium" name="type_referred" id="type_referred">
                        <option value="">Seleccione una opcion...</option>
                        <option value="partner" {if $post.type_referred eq 'partner'}selected{/if}>Asociados</option>
                        <option value="otro" {if $post.type_referred eq 'otro'}selected{/if}>Otros</option>
                    </select>
                    <hr />
                </div>
                <div class="formLine field_list_partner" style="width:100%; text-align:left;
                        display: {if $post.is_referred eq '1' && $post.type_referred eq 'partner'}block{else}none{/if};">
                    <div style="width:30%;float:left"> * Asociados comerciales</div>
                    <select class="largeInput medium" name="partner_id" id="partner_id">
                        <option value="">---Seleccionar---</option>
                        {foreach from=$partners item=item key=key}
                            <option value="{$item.id}" {if $post.partner_id eq $item.id}selected{/if}>{$item.name}</option>
                        {/foreach}
                    </select>
                    <hr />
                </div>
                <div class="formLine field_other_referred" style="width:100%; text-align:left;
                        display: {if $post.is_referred eq '1' && $post.type_referred eq 'otro'}block{else}none{/if};">
                    <div style="width:30%;float:left">* Referente</div>
                    <textarea class="largeInput medium" name="name_referrer" id="name_referrer">{$post.name_referrer}</textarea>
                    <hr />
                </div>

                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:30%;float:left">Observaciones:</div>
                    <textarea class="largeInput medium" name="observacion" id="observacion"
                              size="50">{$post.observacion}</textarea>
                    <hr/>
                </div>
                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:30%;float:left">* Fecha de Alta:</div>
                    <input style="width:20%!important;" class="largeInput"  name="fechaAlta"
                           id="fechaAlta" type="text" value="{if $post}{$post.fechaMysql}{else}{$smarty.now|date_format:'%d-%m-%Y'}{/if}" readonly/>
                    <hr/>
                </div>

                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:30%;float:left">* NO generar factura 13:</div>
                    <div style="width:30%;float:left"><input name="noFactura13" id="noFactura13" type="checkbox" value="Si" {if $post.noFactura13 == "Si"} checked="checked"{/if}/>
                    </div>
                    <hr/>
                </div>
            </div>
            <div style="clear:both"></div>
            * Campos requeridos
            {include file= "{$DOC_ROOT}/templates/boxes/loader.tpl"}
            <div class="formLine" style="text-align:center; margin-left:300px">
                <a class="button_grey" id="{if $post}editCustomer{else}btnAddCustomer{/if}"><span>{if $post}Actualizar{else}Guardar{/if}</span></a>
            </div>
            <input type="hidden" id="type" name="type" value="{if $post}saveEditCustomer{else}saveAddCustomer{/if}"/>
            <input type="hidden" id="customerId" name="customerId" value="{$post.customerId}"/>
        </fieldset>
    </form>
</div>

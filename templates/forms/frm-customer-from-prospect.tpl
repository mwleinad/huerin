<div id="divForm">
    <fieldset>
    <div class="container_16">
        <div class="grid_16">
            <div class="grid_8">
                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:100%;float:left">* Nombre del Directivo:</div>
                    <input class="largeInput " name="nameContact" id="nameContact" type="text"
                        value="{if $prospect.customer}{$prospect.customer.nameContact}
                        {else}{$prospect.name}{/if}"
                        {if $prospect.customer}readonly{/if}/>
                </div>
            </div>
            <div class="grid_8">
                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:100%;float:left">Tel&eacute;fono Contacto Directivo:</div>
                    <input class="largeInput" name="phone" id="phone" type="text"
                           value="{if $prospect.customer}{$prospect.customer.phone}
                       {else}{$prospect.phone}{/if}"
                       {if $prospect.customer}readonly{/if}/>
                </div>
            </div>
            <hr>
        </div>
        <div class="grid_16">
            <div class="grid_8">
                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:100%;float:left">Email Contacto Directivo:</div>
                    <input class="largeInput" name="email" id="email" type="text"
                           value="{if $prospect.customer}{$prospect.customer.email}
                       {else}{$prospect.email}{/if}" {if $prospect.customer}readonly{/if}/>
                </div>
            </div>
            <div class="grid_8">
                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:100%;float:left">* Cliente referido:</div>
                    <select class="largeInput field_referred" name="is_referred" id="is_referred">
                        <option value="0" {if $prospect.is_referred eq '0' || $prospect.customer.is_referred eq '0'}selected{/if}>No</option>
                        <option value="1" {if $prospect.is_referred eq '1' || $prospect.customer.is_referred eq '1'}selected{/if}>Si</option>
                    </select>
                </div>
            </div>
            <hr>
        </div>
        <div class="grid_16">
            <div class="grid_8">
                <div class="formLine field_type_referred" style="width:100%; text-align:left;
                        display: {if $prospect.is_referred || $prospect.customer.is_referred eq '1'}block{else}none{/if};" >
                    <div style="width:100%;float:left">* Referido por</div>
                    <select class="largeInput" name="type_referred" id="type_referred">
                        <option value="">Seleccione una opcion...</option>
                        <option value="partner" {if ($prospect.type_referred eq 'partner' || $prospect.customer.type_referred eq 'partner')}
                        selected{/if}>Asociados</option>
                        <option value="otro" {if ($prospect.type_referred eq 'otro' || $prospect.customer.type_referred eq 'otro')}
                        selected{/if}>Otros</option>
                    </select>
                </div>
            </div>
            <div class="grid_8" >
                <div class="formLine field_list_partner" style="width:100%; text-align:left;
                        display: {if ($prospect.is_referred eq '1' && $prospect.type_referred eq 'partner')
                || ($prospect.customer.is_referred eq '1' && $prospect.customer.type_referred eq 'partner')}
                        block{else}none{/if};">
                    <div style="width:100%;float:left"> * Asociados comerciales</div>
                    <select class="largeInput" name="partner_id" id="partner_id">
                        <option value="">---Seleccionar---</option>
                        {foreach from=$partners item=item key=key}
                            <option value="{$item.id}" {if $prospect.partner_id eq $item.id ||
                            $prospect.customer.partner_id eq $item.id}selected{/if}>
                                {$item.name}
                            </option>
                        {/foreach}
                    </select>
                </div>
                <div class="formLine field_other_referred" style="width:100%; text-align:left;
                        display: {if ($prospect.is_referred eq '1' && $prospect.type_referred eq 'otro')
                        || $prospect.customer.is_referred eq '1' && $prospect.customer.type_referred eq 'otro'}block{else}none{/if};">
                    <div style="width:100%;float:left">* Referente</div>
                    <input class="largeInput" name="name_referrer" id="name_referrer"
                     value="{if $prospect.customer}{$prospect.customer.name_referrer}{else}{$prospect.name_referrer}{/if}"
                    />
                </div>
            </div>
          <hr id="separate_is_referred"  style="display:{if !$prospect.is_referred && !$prospect.customer.is_referred}none{/if}" />
        </div>
        <div class="grid_16">
            <div class="grid_8">
                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:80%;float:left">* NO generar factura 13</div>
                    <div style="width:20%;float:left">
                        <input name="noFactura13" id="noFactura13" type="checkbox" value="Si"
                                {if $post.customer.noFactura13 == "Si"} checked="checked"{/if}/>
                    </div>
                </div>
            </div>
            {if $prospect.customer}
            <div class="grid_8">
                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:100%;float:left">Fecha de Alta:</div>
                    <input style="width:100%!important;" class="largeInput"  name="fechaAlta"
                           id="fechaAlta" type="text" value="{$prospect.customer.fechaAlta|date_format:'%d-%m-%Y'}" readonly/>
                </div>
            </div>
            {/if}
            <hr />
        </div>
    </div>
    </fieldset>
</div>

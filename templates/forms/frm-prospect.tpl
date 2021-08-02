<div id="divForm">
    <form id="formProspect" name="formProspect" method="post" autocomplete="off">
        <input type="hidden" id="type" name="type" value="{if $post}updateProspect{else}saveProspect{/if}"/>
        <input type="hidden" id="customer_exists" name="customer_exists" value="{$post.customer_id}"" />
        {if $post}<input type="hidden" name="id" value="{$post.id}" />{/if}
        <fieldset>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">* Nombre:</div>
                <div class="custom-autocomplete">
                    <input class="largeInput medium" name="name" id="name" type="text" value="{$post.name}" size="50"/>
                </div>

                <hr />
            </div>

            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Tel&eacute;fono:</div>
                <input class="largeInput medium" name="phone" id="phone" type="text" value="{$post.phone}" size="50"/>            	<hr />
            </div>

            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Email:</div>
                <input class="largeInput medium" name="email" id="email" type="text" value="{$post.email}" size="50"/>
                <hr />
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
            <div style="clear:both"></div>
            * Campos requeridos
            {include file= "{$DOC_ROOT}/templates/boxes/loader.tpl"}
            <div class="formLine" style="text-align:center; margin-left:300px">
                <a class="button_grey spanSaveProspect"><span>{if $post}Actualizar{else}Agregar{/if}</span></a>
            </div>
        </fieldset>
    </form>
</div>

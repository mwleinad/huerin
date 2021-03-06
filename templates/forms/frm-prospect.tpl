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
                <div style="width:30%;float:left">Observaciones:</div>
                <textarea class="largeInput medium" name="observation" id="observation">{$post.comment}</textarea>
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

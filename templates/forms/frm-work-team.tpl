<div id="divForm">
    <form id="{$data.nameForm}" name="{$data.nameForm}" method="post" autocomplete="off">
        <div class="container_16">
            <div class="grid_16">
                <div class="formLine" style="width:100%; text-align:left">
                    <div style="width:30%;float:left">* Nombre del grupo:</div>
                    <input class="largeInput medium" name="name" id="name" type="text"
                           value="{$post.name}"/>
                </div>
                <hr/>
            </div>
            <div class="grid_16">
                    <div class="grid_8"><h3>Departamento</h3></div>
                    <div class="grid_8"><h3>Responsable</h3></div>
                    {foreach from=$departaments item=item key=key}
                        <div class="grid_8">
                            <input type="text" readonly name="departament" id="departament" value="{$item.departamento}"
                                   class="largeInput"/>
                        </div>
                        <div class="grid_8">
                            <select name="res_{$item.departamentoId}" id="res_{$item.departamentoId}" class="largeInput medium">
                                {foreach from=$responsables[$item.departamentoId] key=key item=item2}
                                    <option value="{$item2.id}"
                                            {if $item2.id eq $post.current_responsable[$item.departamentoId]}
                                            selected{/if}>{$item2.name}</option>
                                {/foreach}
                            </select>
                        </div>
                        <hr>
                    {/foreach}
            </div>
        </div>
        * Campos requeridos
        {include file= "{$DOC_ROOT}/templates/boxes/loader.tpl"}
        <div class="formLine" style="text-align:center; margin-left:300px">
            <a class="button_grey"
               id="btnWorkTeam"><span>{if $post}Actualizar{else}Guardar{/if}</span></a>
        </div>
        <input type="hidden" id="type" name="type" value="{if $post}updateWorkTeam{else}saveWorkTeam{/if}"/>
        <input type="hidden" id="id" name="id" value="{$post.id}"/>
    </form>
</div>

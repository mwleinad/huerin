<div id="divForm">
<form name="frmResponsableResource" id="frmResponsableResource" method="post" action="" onsubmit="return false;">
    <input type="hidden" name="type" value="saveUpkeepResource" />
    <input type="hidden" name="office_resource_id" value="{$resource.office_resource_id}" />
    {if $post}
        <input type="hidden" name="upk_id" value="{$post.upkeep_resource_office_id}" />
    {/if}
    <fieldset>
        <div class="container_16">
        <div class="grid_16">
            <div class="formLine" style=" width:100%;display: inline-block;">
                <div style="width:30%;float:left"> * Realizado por</div>
                <div style="width:70%;float: left;">
                    <input name="upkeep_responsable" id="upkeep_responsable" class="largeInput" value="{$post.upkeep_responsable}">
                </div>
            </div>
            <hr>
        </div>
        <div class="grid_16">
            <div class="formLine" style="width:100%;display: inline-block;">
                <div  style="width:30%;float:left">* Fecha de mantenimiento</div>
                <div  style="width:38%;float:left">
                    <input name="upkeep_date"  id="upkeep_date"  onclick="CalendarioSimple(this)" type="text" class="largeInput "
                           value="{if $post.upkeep_date}{$post.upkeep_date|date_format:'%d-%m-%Y'}{/if}"
                           autocomplete="off"/>
                </div>
            </div>
            <hr/>
        </div>
        <div class="grid_16">
            <div class="formLine" style="width:100%;  display: inline-block;">
                <div style="width:30%;float:left">* Mantenimiento realizado</div>
                <div style="width:70%;float: left;">
                    <textarea name="upkeep_description" id="upkeep_description" class="largeInput" rows="5">{$post.upkeep_description}</textarea>
                </div>
            </div>
            <hr>
        </div>
        <div style="clear:both"></div>
        <div class="grid_16">
            <span style="float:left">* Campos Obligatorios</span>
        </div>
        <div class="grid_16" style="text-align: center">
            <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
        </div>
        <div class="grid_16" style="text-align: center">
            <div class="formLine"  style="display: inline-block">
                <a href="javascript:;"  id="btnUpkeep" class="button_grey"><span>{if $post}Actualizar{else}Guardar{/if}</span></a>
            </div>
        </div>
        </div>
    </fieldset>
</form>
</div>

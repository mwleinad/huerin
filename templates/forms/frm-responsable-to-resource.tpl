<div id="divForm">
<form name="frmResponsableResource" id="frmResponsableResource" method="post" action="" onsubmit="return false;">
    <input type="hidden" name="type" value="saveResponsableResource" />
    <input type="hidden" name="office_resource_id" value="{$resource.office_resource_id}" />
    {if $post}
        <input type="hidden" name="rs_id" value="{$post.responsable_resource_id}" />
    {/if}
    <fieldset>
        <div class="container_16">
        <div class="grid_16">
            <div class="formLine" style=" width:100%;display: inline-block;">
                <div style="width:30%;float:left"> * Nombre responsable</div>
                <div style="width:70%;float: left;">
                    <select name="personalId" id="personalId" class="largeInput">
                        <option value="">Seleccionar...</option>
                        {foreach from=$empleados item=item key=key}
                            <option value ="{$item.personalId}" {if $post.personalId eq $item.personalId}selected{/if}>{$item.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <hr>
        </div>
        <div class="grid_16">
            <div class="formLine" style="width:100%;display: inline-block;">
                <div  style="width:30%;float:left">* Fecha de entrega a responsable</div>
                <div  style="width:38%;float:left">
                    <input name="fecha_entrega"  id="fecha_entrega"  onclick="CalendarioSimple(this)" type="text" class="largeInput "
                           value="{if $post.fecha_entrega_responsable}{$post.fecha_entrega_responsable|date_format:'%d-%m-%Y'}{/if}"
                           autocomplete="off"/>
                </div>
            </div>
            <hr/>
        </div>
        <div class="grid_16">
            <div class="formLine" style="width:100%;  display: inline-block;">
                <div style="width:30%;float:left">* Tipo responsable</div>
                <div style="width:40%;float: left;">
                    <select name="tipo_responsable" id="tipo_responsable" class="largeInput">
                        <option value="" {if $post.tipo_responsable eq ""}selected{/if}>Seleccionar..</option>
                        <option value="Principal" {if $post.tipo_responsable eq "Principal"}selected{/if}>Principal</option>
                        <option value="Secundario" {if $post.tipo_responsable eq "Secundario"}selected{/if} >Secundario</option>
                    </select>
                </div>
            </div>
            <hr>
        </div>
        <div class="grid_16">
            <div class="formLine" style="width:100%;  display: inline-block;">
                <div style="width:30%;float:left"> Adjuntar responsiva(PDF Max 2MB)</div>
                <div style="width:68%;float:left" >
                    <input name="responsiva"  id="responsiva"   type="file" class="largeInput"/>
                 {if is_file("{$DOC_ROOT}{$post.responsiva_root}")}
                     <span style="color: #FFA500">Existe un archivo responsiva. si desea actualizarlo adjunte una nueva.
                         <a href="{$WEB_ROOT}{$post.responsiva_root}" title="Ver responsiva" target="_blank" class="spanAll">
                            <img src="{$WEB_ROOT}/images/icons/pdf.png">
                         </a>
                     </span>
                 {/if}
                </div>
            </div>
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
                <a href="javascript:;"  id="btnResponsable" class="button_grey"><span>{if $post}Actualizar{else}Guardar{/if}</span></a>
            </div>
        </div>
        </div>
    </fieldset>
</form>
</div>

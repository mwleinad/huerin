<div id="divForm">
    <form id="frmExpediente" name="frmExpediente" method="post" onsubmit="return false">
        <input type="hidden" id="type" name="type" value="{if $info}updateExpediente{else}saveExpediente{/if}"/>
        <input type="hidden" id="id" name="id" value="{$info.expedienteId}"/>
        <fieldset>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">* Nombre:</div>
                <div style="width:70%;display:inline-block"><input name="nombre" id="nombre" type="text"
                                                                   value="{$info.name}" class="largeInput medium"/>
                </div>
            </div>
            <hr>
            <div class="formLine" style="width:100%;height: auto;vertical-align: middle;display: table;">
                <div style="display: table-cell;width: 30%;vertical-align: middle;">Extension de archivo permitido(puede
                    marcar varios)
                </div>
                <div style="display: table-cell;width: 70%;vertical-align: middle;">
                    <table>
                        <tr>
                            <td><input type="checkbox" id="check_all" name="check_all"></td>
                            <td>Todos</td>
                        </tr>
                        {assign var='ncol' value=4}
                        {foreach from=$extensiones key=kexp item=exp}
                            {if $ncol eq 4}
                                <tr>
                                <td><input type="checkbox" name="extensiones[]" id="extensiones"
                                           value="{$exp.extension}" {if $exp.permitido}checked{/if}></td>
                                <td>{$exp.name}</td>
                                {assign var='ncol' value=$ncol-1}
                            {else}
                                <td><input type="checkbox" name="extensiones[]" id="extensiones"
                                           value="{$exp.extension}" {if $exp.permitido}checked{/if}></td>
                                <td>{$exp.name}</td>
                                {assign var='ncol' value=$ncol-1}
                                {if $ncol eq 0}
                                    {assign var='ncol' value=4}
                                    </tr>
                                {/if}
                            {/if}
                        {/foreach}
                    </table>
                </div>
            </div>
            <div style="clear:both"></div>
            <hr/>
            <div class="formLine" style="text-align:center">
                <div style="display:inline-block;text-align: center;">
                    <img src="{$WEB_ROOT}/images/loading.gif" style="display:none" id="loading-img"/>
                    <a class="button_grey" id="btnExpediente"
                       name="btnExpediente"><span>{if $info}Actualizar{else}Guardar{/if}</span></a>
                </div>
            </div>
        </fieldset>
    </form>
</div>

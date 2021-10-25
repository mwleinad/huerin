<div id="divForm">
    <form id="{$data.nameForm}" name="{$data.nameForm}"  method="post">
        <input type="hidden" id="type" name="type" value="{if $post}saveEditTask{else}saveAddTask{/if}"/>
        <input type="hidden" id="taskId" name="taskId" value="{$post.taskId}"/>
        <input type="hidden" id="servicioId" name="servicioId" value="{$servicioId}"/>
        <input type="hidden" id="stepId" name="stepId" value="{$stepId}"/>
        <fieldset>

            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">* Nombre:</div>
                <div style="width:30%;float:left"><input name="nombreTask" id="nombreTask" type="text"
                                                         value="{$post.nombreTask}" size="27"
                                                         class="largeInput medium"/></div>
                <hr/>
            </div>

            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">* Orden:</div>
                <div style="width:30%;float:left"><input name="order" id="order" type="text"
                                                         value="{$post.taskPosition}" size="27"
                                                         class="largeInput medium"/></div>
                <hr/>
            </div>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:20%;float:left">* Vigencia del</div>
                <div style=" width:65%;display: table;border-collapse: separate;position: relative">
                    <input type="text" id="effectiveDate" name="effectiveDate" class="largeInput"
                           onclick="CalendarioSimple(this)" style="float: left;display: table-cell!important;"
                           value="{if $post.effectiveDate neq '0000-00-00' && $post.effectiveDate  neq null}{$post.effectiveDate|date_format:'%d-%m-%Y'}{/if}"
                           placeholder="Fecha inicio vigencia"/>
                    <span class="input-addon">al</span>
                    <input type="text" id="finalEffectiveDate" name="finalEffectiveDate" class="largeInput"
                           onclick="CalendarioSimple(this)" style="float: left;display: table-cell!important;
                           value="
                           value="{if $post.finalEffectiveDate neq '0000-00-00' && $post.finalEffectiveDate  neq null}{$post.finalEffectiveDate|date_format:'%d-%m-%Y'}{/if}"
                           placeholder="Fecha fin de vigencia"
                    />
                </div>
                <hr/>
            </div>

            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">*Dia de Vencimiento:</div>
                <div style="width:30%;float:left"><input name="diaVencimiento" id="diaVencimiento" type="text"
                                                         value="{$post.diaVencimiento}" size="27"
                                                         class="largeInput medium"/></div>
                <hr/>
            </div>

            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">*Dias de Prorroga:</div>
                <div style="width:30%;float:left">
                    <input name="prorroga" id="prorroga" type="text"
                                                            value="{$post.prorroga}" size="27" class="largeInput medium" />
                </div>
                <hr/>
            </div>

            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">*Control Bueno (Obligatorio):</div>
                <div style="width:70%;float:left">
                    <textarea name="control" cols="10" id="control" class="largeInput medium">{$post.control}</textarea>
                </div>
                <hr/>
            </div>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Control Regular:</div>
                <div style="width:70%;float:left">
                    <textarea name="control2" cols="10" id="control2" class="largeInput medium">{$post.control2}</textarea>
                </div>
                <hr/>
            </div>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Control Malo:</div>
                <div style="width:70%;float:left">
                    <textarea name="control3" cols="10" id="control3" class="largeInput medium">{$post.control3}</textarea>
                </div>
                <hr/>
            </div>
            <div class="formLine" style="width:100%;height: auto;vertical-align: middle;display: table;">
                <div style="display: table-cell;width: 30%;vertical-align: middle;">Extension de archivo permitido(puede
                    marcar varios)
                </div>
                <div style="display: table-cell;width: 70%;vertical-align: middle;">
                    <table>
                        <tr>
                            <td><input type="checkbox" id="check_all" name="check_all" {if $all_checked}checked{/if}>
                            </td>
                            <td>Todos</td>
                        </tr>
                        {assign var='ncol' value=4}
                        {foreach from=$extensiones key=kexp item=exp}
                            {if $ncol eq 4}
                                <tr>
                                <td><input type="checkbox" name="extensiones[]" id="extensiones"
                                           value="{$exp.extension}" {if $exp.permitido}checked{/if} ></td>
                                <td>{$exp.name}</td>
                                {assign var='ncol' value=$ncol-1}
                            {else}
                                <td><input type="checkbox" name="extensiones[]" id="extensiones"
                                           value="{$exp.extension}" {if $exp.permitido}checked{/if} ></td>
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

                <hr/>
            </div>

            <div style="clear:both"></div>
            <div class="actionPopup">
                <span class="msjRequired">* Campos requeridos </span><br>
                <div class="actionsChild">
                    <img src="{$WEB_ROOT}/images/loading.gif" style="display:none" id="loading-img"/>
                </div>
                <div class="actionsChild">
                    <div style="display: inline-block"><a class="button_grey" id="{if $post}btnEditTask{else}btnAddTask{/if}">
                            <span>{if $post}Actualizar{else}Agregar{/if}</span>
                        </a></div>
                </div>
            </div>
        </fieldset>
    </form>
</div>

<div id="divForm">
    <form id="{$data.nameForm}" name="{$data.nameForm}" method="post">
        <input type="hidden" id="type" name="type" value="{if $post}saveEditStep{else}saveAddStep{/if}"/>
        <input type="hidden" id="stepId" name="stepId" value="{$post.stepId}"/>
        <input type="hidden" id="servicioId" name="servicioId" value="{$servicioId}"/>
        <fieldset>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:20%;float:left">* Nombre:</div>
                <div style="width:80%;float:left">
                    <input name="nombreStep" id="nombreStep" type="text"
                           value="{$post.nombreStep}" size="27" class="largeInput medium"/>
                </div>
                <hr/>
            </div>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:20%;float:left">* Descripcion:</div>
                <div style="width:80%;float:left">
                    <input name="descripcion" id="descripcion" type="text"
                           value="{$post.descripcion}" size="27" class="largeInput medium"/>
                </div>
                <hr/>
            </div>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:20%;float:left">* Orden:</div>
                <div style="width:30%;float:left">
                    <input name="order" id="order" type="text"
                           value="{$post.position}" class="largeInput medium"/>
                </div>
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
            <div style="clear:both"></div>
            * Campos requeridos
            {include file= "{$DOC_ROOT}/templates/boxes/loader.tpl"}
            <div class="formLine" style="text-align:center; margin-left:300px">
                <a class="button_grey" id="{if $post}btnEditStep{else}btnAddStep{/if}">
                    <span>{if $post}Actualizar{else}Agregar{/if}</span>
                </a>
            </div>
        </fieldset>
    </form>
</div>

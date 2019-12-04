<div id="divForm">
    <form id="editRegimenForm" name="editRegimenForm" method="post" autocomplete="off">
        <fieldset>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">* Nombre del Regimen:</div>
                <input name="regimenName" id="regimenName" type="text" value="{$post.nombreRegimen}"  class="largeInput"/>
            </div>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Tipo de Persona:</div>
                <select id="tipoDePersona" name="tipoDePersona" class="largeInput">
                    <option value="Persona Fisica" {if $post.tipoDePersona == "Persona Fisica"} selected="selected"{/if}>Persona Fisica</option>
                    <option value="Persona Moral" {if $post.tipoDePersona == "Persona Moral"} selected="selected"{/if}>Persona Moral</option>
                </select>
            </div>
            <div style="clear:both"></div>
            <hr/>
			{include file= "{$DOC_ROOT}/templates/boxes/loader.tpl"}
			<div class="formLine" style="text-align:center; margin-left:300px">
				<a class="button_grey" id="editRegimen" name="editRegimen" ><span>Actualizar</span></a>
			</div>
            <input type="hidden" id="type" name="type" value="saveEditRegimen"/>
            <input type="hidden" id="regimenId" name="regimenId" value="{$post.regimenId}"/>
        </fieldset>
    </form>
</div>

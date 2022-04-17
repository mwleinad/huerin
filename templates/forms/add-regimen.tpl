<div id="divForm">
    <form id="addRegimenForm" name="addRegimenForm" method="post" autocomplete="off">
        <input type="hidden" id="type" name="type" value="saveAddRegimen"/>
        <fieldset>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">* Clave:</div>
                <input  class="largeInput medium2" name="regimenId" id="regimenId" type="text" value="{$post.regimenId}" size="10"/>
            </div>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">* Nombre del Regimen:</div>
                <input  class="largeInput" name="regimenName" id="regimenName" type="text" value="{$post.regimenName}" size="50"/>
            </div>

            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Tipo de Persona:</div>
                <select id="tipoDePersona" name="tipoDePersona" class="largeInput">
                    <option value="1">Persona Moral</option>
                    <option value="2">Persona Fisica</option>
                    <option value="3">Ambos</option>
                </select>
            </div>
            <div style="clear:both"></div>
            <hr/>
			{include file= "{$DOC_ROOT}/templates/boxes/loader.tpl"}
			<div class="formLine" style="text-align:center; margin-left:300px">
				<a class="button_grey" id="addRegimenButton" name="addRegimenButton" ><span>Agregar</span></a>
			</div>
        </fieldset>
    </form>
</div>

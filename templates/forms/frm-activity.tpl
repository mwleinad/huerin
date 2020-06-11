<div id="divForm">
	<form id="frmActivity" name="frmActivity" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		{if $post}
			<input name="id" id="id" type="hidden" value="{$post.id}" size="50"/>
		{/if}
        <input type="hidden" id="type" name="type" value="save"/>
		<fieldset>
            <div class="container_16">
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:30%;float:left"> * Sector</div>
                        <div style="width:70%;float: left;">
                            <select class="smallInput select2" name="sector" id="sector">
                                <option value="">Seleccionar..</option>
                                {foreach from=$sectores  item=sector key=key}
                                    <option value="{$sector.id}" {if $post.sector_id eq $sector.id}selected{/if}>{$sector.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:30%;float:left"> * Subsector</div>
                        <div style="width:70%;float: left;">
                            <select class="smallInput select2" name="subsector" id="subsector">
                                <option value="">Seleccionar..</option>
                                {foreach from=$subsectores  item=subsector key=key}
                                    <option value="{$subsector.id}" {if $post.subsector_id eq $subsector.id}selected{/if}>{$subsector.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left">* Actividad</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="name" id="name" value="{$post.name}" class="largeInput">
                        </div>
                    </div>
                </div>
            </div>
		</fieldset>
        <div class="formLine" style="text-align:center">
            <span style="float:left">* Campos Obligatorios</span>
            <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
            <input type="submit" id="btnControl" name="btnControl" class="buttonForm" value="{if $post}Actualizar{else}Guardar{/if}" />
        </div>
	</form>
</div>

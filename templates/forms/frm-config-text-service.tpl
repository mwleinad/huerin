<div id="divForm">
	<form id="frmResource" name="frmResource" method="post" onsubmit="return false;" action="#"  autocomplete="off">
        <input name="service_id" id="servicie_id" type="hidden" value="{$post.service_id}" size="50"/>
        {if $post.id}<input name="id" id="id" type="hidden" value="{$post.id}" size="50"/>{/if}
		<input type="hidden" id="type" name="type" value="saveTextReport"/>

		<fieldset>
            <div class="container_16">
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> * Descripcion detallada de actividades</div>
                        <div style="width:100%;float: left;">
                            <textarea name="large_description" id="large_description" class="largeInput">{$post.large_description}</textarea>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> * Descripcion corta de actividades</div>
                        <div style="width:100%;float: left;">
                            <textarea name="short_description" id="short_description"  class="largeInput">{$post.short_description}</textarea>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left">Expectativa</div>
                        <div style="width:100%;float: left;">
                                <textarea name="expectation" id="expectation" class="largeInput">{$post.expectation}</textarea>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left">* Informacion requerida</div>
                        <div style="width:100%;float: left;">
                            <textarea name="request_information" id="request_information" class="largeInput">{$post.request_information}</textarea>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left">* Programacion de trabajo</div>
                        <div style="width:100%;float: left;">
                            <textarea name="work_schedule" id="work_schedule" class="largeInput">{$post.work_schedule}</textarea>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left">* Informes a presentar</div>
                        <div style="width:100%;float: left;">
                            <textarea name="reports" id="reports" class="largeInput">{$post.reports}</textarea>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <span style="float:left">* Campos Obligatorios</span>
                </div>
                <div class="grid_16" style="text-align: center">
                    <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
                </div>
                <div class="grid_16" style="text-align: center">
                    <div class="formLine"  style="display: inline-block">
                        <a href="javascript:;"  id="btnText" class="button_grey"><span>{if $post.id}Actualizar{else}Guardar{/if}</span></a>
                    </div>
                </div>
            </div>
		</fieldset>
	</form>
</div>

<div id="divForm">
	<form id="frmCompany" name="frmCompany" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		<input type="hidden" id="prospect_id" name="prospect_id" value="{$prospect.id}" >
		<input type="hidden" id="type" name="type" value="{if $post}updateCompany{else}saveCompany{/if}"/>
		{if $post}<input name="id" id="id" type="hidden" value="{$post.id}" size="50"/>{/if}
		<fieldset>
            <div class="grid_16">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:40%;float:left"> * Nombre o razon social</div>
                        <div style="width:60%;float: left;">
							<input type="text" name="name" id="name" value="{if $post}{$post.name}{else}{/if}" class="largeInput "/>
                        </div>
                    </div>
                </div>
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:80%;float:left"> * ¿ Empresa de nueva creacion ?</div>
                        <div style="width:20%;float: left;">
							<input type="checkbox" name="is_new_company" id="is_new_company" {if $post.is_new_company}checked{/if}>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
			<div class="grid_16" id="data_conditioned">
				<div class="grid_8">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:40%;float:left"> Rfc</div>
						<div style="width:60%;float: left;">
							<input type="text" name="rfc" id="rfc" value="{$post.rfc}" class="largeInput "/>
						</div>
					</div>
				</div>
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:40%;float:left"> Fecha constitucion</div>
                        <div style="width:60%;float: left;">
                            <input type="text" name="constitution_date" id="constitution_date"
                                   value="{if $post && $post.constitution_date neq '0000-00-00'}{$post.constitution_date|date_format:'%d-%m-%Y'}{/if}" class="largeInput"
                                   onclick="CalendarioSimple(this)"
                            />
                        </div>
                    </div>
                </div>
				<hr>
			</div>
            <div class="grid_16">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:40%;float:left"> * Email</div>
                        <div style="width:60%;float: left;">
                            <input type="text" name="email" id="email" value="{$post.email}" class="largeInput "/>
                        </div>
                    </div>
                </div>
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:40%;float:left"> * Telefono</div>
                        <div style="width:60%;float: left;">
                            <input type="text" name="phone" id="phone" value="{$post.phone}" class="largeInput "/>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:40%;float:left"> Representante legal</div>
                        <div style="width:60%;float: left;">
                            <input type="text" name="legal_representative" id="legal_representative" value="{$post.legal_representative}" class="largeInput "/>
                        </div>
                    </div>
                </div>
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:40%;float:left"> Observacion</div>
                        <div style="width:60%;float: left;">
                            <input type="text" name="observation" id="observation" value="{$post.observation}" class="largeInput "/>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16">
                <div class="formLine" style="width:100%;  display: inline-block;">
                    <div style="width:30%;float:left"> Servicios</div>
                    <div style="width:70%;float: left;">
                        <select class="largeInput " multiple="multiple" name="services[]" id="customMultiple">
                        </select>
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
                    <a href="javascript:;"  id="btnResource" class="button_grey spanSaveCompany"><span>{if $post}Actualizar{else}Guardar{/if}</span></a>
                </div>
            </div>

		</fieldset>
	</form>
</div>

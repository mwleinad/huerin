<div id="divForm">
	<form id="frmCompany" name="frmCompany" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		<input type="hidden" id="prospect_id" name="prospect_id" value="{$prospect.id}" >
        <input type="hidden" id="contract_exists" name="contract_exists" value="{$post.contract_id}" >
		<input type="hidden" id="type" name="type" value="{if $post}updateCompany{else}saveCompany{/if}"/>
		{if $post}<input name="id" id="id" type="hidden" value="{$post.id}" size="50"/>{/if}
		<fieldset>
            <div class="grid_16">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:40%;float:left"> * Tipo de persona</div>
                        <div style="width:60%;float: left;">
                                <select type="text" name="tax_purpose" id="tax_purpose" class="largeInput">
                                    <option value="">Seleccionar...</option>
                                    <option value="fisica" {if $post.tax_purpose eq 'fisica'}selected{/if}>Persona Fisica</option>
                                    <option value="moral"  {if $post.tax_purpose eq 'moral'}selected{/if}>Personal Moral</option>
                                </select>
                        </div>
                    </div>
                </div>

                <div class="grid_8 field_is_new_company" style="display:{if $post.tax_purpose eq 'moral'}block{else}none{/if}">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:80%;float:left"> * ¿ Empresa de nueva creacion ?</div>
                        <div style="width:20%;float: left;">
							<input type="checkbox" name="is_new_company" id="is_new_company" {if $post.is_new_company}checked{/if}>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:40%;float:left"> * Nombre o razon social</div>
                        <div style="width:60%;float: left;">
                            <div class="custom-autocomplete">
                                <input type="text" name="name" id="name" value="{if $post}{$post.name}{else}{/if}"
                                       class="largeInput "/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid_8">
                    <div class="formLine field_regimen" style="width:100%;  display:{if $post}inline-block{else}none{/if};">
                        <div style="width:40%;float:left"> Regimen</div>
                        <div style="width:60%;float: left;">
                            <input type="hidden" class="largeInput" name="regimen_id" id="regimen_id" value="{$post.regimen_id}" />
                            <!--select class="largeInput"  name="regimen_id" id="regimen_id">
                                <option value="">Seleccionar..</option>
                                {foreach from=$regimenes item=item}
                                    <option value="{$item.tipoRegimenId}" {if $post.regimen_id eq $item.tipoRegimenId}selected{/if}>{$item.nombreRegimen}</option>
                                {/foreach}
                            </select-->
                        </div>
                    </div>
                </div>
                <hr>
            </div>
			<div class="grid_16" id="data_constitution" style="display:{if $post}block{else}none{/if};">
				<div class="grid_8">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:40%;float:left"> RFC</div>
						<div style="width:60%;float: left;">
							<input type="text" name="rfc" id="rfc" value="{$post.taxpayer_id}" class="largeInput "/>
						</div>
					</div>
				</div>
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:40%;float:left" id="label_date_constitution">
                            {if $post.tax_purpose eq 'moral'}
                                Fecha de constitucion
                            {elseif $post.tax_purpose eq 'fisica'}
                                Fecha alta en el SAT{/if}</div>
                        <div style="width:60%;float: left;">
                            <input type="text" name="date_constitution" id="date_constitution"
                                   value="{if $post && $post.date_constitution neq '0000-00-00'}{$post.date_constitution|date_format:'%d-%m-%Y'}{/if}" class="largeInput"
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
                        <div style="width:40%;float:left"> Representante legal</div>
                        <div style="width:60%;float: left;">
                            <input type="text" name="legal_representative" id="legal_representative" value="{$post.legal_representative}" class="largeInput "/>
                        </div>
                    </div>
                </div>
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:40%;float:left"> Actividad</div>
                        <div style="width:60%;float: left;">
                            <select class="largeInput"  name="activity_id" id="activity_id">
                                <option value="">Seleccionar..</option>
                                {foreach from=$actividades item=item}
                                    <option value="{$item.id}" {if $post.activity_id eq $item.id}selected{/if}>{$item.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16">
                <div class="formLine" style="width:100%;  display: inline-block;">
                    <div style="width:20%;float:left"> Servicios</div>
                    <div style="width:80%;float: left;">
                        <select class="largeInput" multiple="multiple" name="services[]" id="customMultiple">
                        </select>
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16">
                <div class="formLine" style="width:100%;  display: inline-block;">
                    <div style="width:30%;float:left"> Observacion</div>
                    <div style="width:100%;float: left;">
                        <textarea name="observation" id="observation" class="largeInput" rows="5">{$post.comment}</textarea>
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
                    <a href="javascript:;"  id="btnResource" class="button_grey spanSaveCompany"><span>{if $post}Actualizar{else}Guardar{/if}</span></a>
                </div>
            </div>

		</fieldset>
	</form>
</div>

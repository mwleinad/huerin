<div id="divForm">
	<form id="frmResource" name="frmResource" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		{if $post}
			<input name="office_resource_id" id="office_resource_id" type="hidden" value="{$post.office_resource_id}" size="50"/>
			<input type="hidden" id="type" name="type" value="updateResource"/>
		{else}
			<input type="hidden" id="type" name="type" value="saveResource"/>
		{/if}
		<fieldset>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:30%;float:left"> * Tipo de recurso</div>
				<div style="width:70%;float: left;">
					<select class="largeInput" name="tipo_recurso" id="tipo_recurso">
						<option value="dispositivo" {if $post.tipo_recurso eq "dispositivo"}selected{/if}>Dispositivo</option>
						<option value="equipo_computo" {if $post.tipo_recurso eq "equipo_computo"}selected{/if}>Equipo de computo</option>
						<option value="inmobiliaria" {if $post.tipo_recurso eq "inmobiliaria"}selected{/if}>Inmobiliaria</option>
					</select>
				</div>
				<hr>
			</div>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:30%;float:left"> * Nombre</div>
				<div style="width:70%;float: left;">
					<input type="text" name="nombre" id="nombre" value="{$post.nombre}" class="largeInput "/>
				</div>
				<hr>
			</div>
			<div class="grid_16">
				<div class="formLine" style="width:100%;  display: inline-block;">
					<div style="width:30%;float:left"> * Descripcion</div>
					<div style="width:70%;float: left;">
						<textarea name="descripcion" id="descripcion" class="largeInput ">{$post.descripcion}</textarea>
					</div>

				</div>
				<hr>
			</div>
            <div class="grid_16">
                <div class="formLine" style="width:100%;  display: inline-block;">
                    <div style="width:30%;float:left">* Fecha de compra</div>
                    <div style="width:70%;float: left;">
                        <input name="fecha_compra"  id="fecha_compra" onclick="CalendarioSimple(this)" value="{if $post.fecha_compra neq "0000-00-00"}{$post.fecha_compra|date_format:"%d-%m-%Y"}{/if}" type="text" class="largeInput "/>
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16 field_computo {if $post.tipo_recurso neq 'equipo_computo'}noShow{/if}">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:40%;float:left"> Cuenta con hub usb</div>
                        <div style="width:30%;float: left;">
                            <input name="hub_usb"  id="hub_usb" type="checkbox" class="largeInput " {if $post.con_hubusb}checked{/if}/>
                        </div>
                    </div>
                </div>
                <div class="grid_8">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:40%;float:left"> Cuenta con no break</div>
						<div style="width:30%;float: left;">
							<input name="no_break"  id="no_break" type="checkbox" class="largeInput" {if $post.con_nobreak}checked{/if}/>
						</div>
					</div>
                </div>
				<hr>
            </div>
			<div class="grid_16">
				<div class="grid_8">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> No. Serie</div>
						<div style="width:70%;float: left;">
							<input name="no_serie"  id="no_serie" type="text"  value="{$post.no_serie}" class="largeInput "/>
						</div>
					</div>
				</div>
				<div class="grid_8 field_computo {if $post.tipo_recurso neq 'equipo_computo'}noShow{/if}">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> No. licencia</div>
						<div style="width:70%;float: left;">
							<input name="no_licencia"  id="no_licencia" type="text" value="{$post.no_licencia}" class="largeInput "/>
						</div>
					</div>
				</div>
                <hr>
			</div>
            <div class="grid_16 field_computo {if $post.tipo_recurso neq 'equipo_computo'}noShow{/if}">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:30%;float:left"> Cod. activación</div>
                        <div style="width:70%;float: left;">
                            <input name="cod_activacion"  id="cod_activacion" type="text"  value="{$post.codigo_activacion}" class="largeInput "/>
                        </div>
                    </div>
                </div>
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:30%;float:left"> * Tipo de equipo</div>
                        <div style="width:70%;float: left;">
                            <select class="largeInput" id="tipo_equipo" name="tipo_equipo">
                                <option value="">Seleccionar..</option>
                                <option value="escritorio" {if $post.tipo_equipo eq "escritorio"}selected{/if}>Escritorio</option>
                                <option value="portatil" {if $post.tipo_equipo eq "portatil"}selected{/if}>Portatil</option>
                            </select>
                            
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16">
                <form id="frmResponsable" name="frmResponsable" method="post" onsubmit="return false;" action="#"  autocomplete="off">
                <div class="grid_16" style="margin-bottom: 12px">
                    <span style="color:#FFA500">Nota: Utilice el formulario de abajo para agregar responsables.<br></span>
                </div>
                <div class="grid_5">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">* Nombre reponsable</div>
                        <div style="width:100%;float: left;">
                            <input name="nombre_responsable"  id="nombre_responsable" type="text" class="largeInput "/>
                        </div>
                    </div>
                </div>
                <div class="grid_4">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">* Fecha de entrega a res.</div>
                        <div style="width:100%;float: left;">
                            <input name="fecha_entrega"  id="fecha_entrega"  onclick="CalendarioSimple(this)" type="text" class="largeInput "/>
                        </div>
                    </div>
                </div>
                <div class="grid_5">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left">* Tipo responsable</div>
                        <div style="width:100%;float: left;">
                            <select name="tipo_responsable" id="tipo_responsable" class="largeInput">
                                <option value="">Seleccionar..</option>
                                <option value="Principal">Principal</option>
                                <option value="Secundario">Secundario</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="grid_2">
                    <div class="formLine" style="text-align: center;vertical-align: middle">
                        <a href="javascript:;"  id="btnAddResponsable" class="button_grey"><span>Agregar</span></a>
                    </div>
                </div>
                </form>
            </div>
            <div class="grid_16" id="div_responsable_resource">
                {include file="{$DOC_ROOT}/templates/lists/responsables-resources.tpl"}
            </div>
            <div style="clear:both"></div>
			<hr />
            <div class="grid_16">
                <span style="float:left">* Campos Obligatorios</span>
            </div>
            <div class="grid_16" style="text-align: center">
                <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
            </div>
            <div class="grid_16" style="text-align: center">
                <div class="formLine"  style="display: inline-block">
                    <a href="javascript:void();"  id="btnResource" class="button_grey"><span>{if $post}Actualizar{else}Guardar{/if}</span></a>
                </div>
            </div>

		</fieldset>
	</form>
</div>

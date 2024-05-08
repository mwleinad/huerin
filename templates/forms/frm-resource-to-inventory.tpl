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
                        <option value="">Seleccionar...</option>
						<option value="dispositivo" {if $post.tipo_recurso eq "dispositivo"}selected{/if}>Accesorios</option>
						<option value="equipo_computo" {if $post.tipo_recurso eq "equipo_computo"}selected{/if}>Computadora</option>
						<option value="software" {if $post.tipo_recurso eq "software"}selected{/if}>Sistemas</option>
					</select>
				</div>
				<hr>
			</div>
			<div class="grid_16">
				<div class="formLine" style="width:100%;  display: inline-block;">
					<div style="width:30%;float:left"> * Ubicación</div>
					<div style="width:70%;float: left;">
						<input name="ubicacion" id="ubicacion" class="largeInput" value="{$post.ubicacion}" />
					</div>
				</div>
				<hr>
			</div>
			<div class="grid_16 shared_field equipo_computo
						{if !in_array($post.tipo_recurso, ['equipo_computo'])}
						noShow{/if}">
				<div class="formLine" style="width:100%;  display: inline-block;">
					<div style="width:30%;float:left"> * Procesador</div>
					<div style="width:70%;float: left;">
						<input name="procesador" id="procesador" class="largeInput" value="{$post.procesador}" />
					</div>
				</div>
				<hr>
			</div>
			<div class="grid_16 shared_field equipo_computo
						{if !in_array($post.tipo_recurso, ['equipo_computo'])}
						noShow{/if}">
				<div class="formLine" style="width:100%;  display: inline-block;">
					<div style="width:30%;float:left">Velocidad de Procesador</div>
					<div style="width:70%;float:left;">
						<input name="velocidad_procesador" id="velocidad_procesador" class="largeInput" value="{$post.velocidad_procesador}" />
					</div>
				</div>
				<hr>
			</div>
			<div class="grid_16 shared_field equipo_computo
						{if !in_array($post.tipo_recurso, ['equipo_computo'])}
						noShow{/if}">
				<div class="formLine" style="width:100%;  display: inline-block;">
					<div style="width:30%;float:left"> * Memoria Ram</div>
					<div style="width:70%;float: left;">
						<input name="memoria_ram" id="memoria_ram" class="largeInput" value="{$post.memoria_ram}" />
					</div>
				</div>
				<hr>
			</div>
			<div class="grid_16 shared_field equipo_computo
						{if !in_array($post.tipo_recurso, ['equipo_computo'])}
						noShow{/if}">
				<div class="formLine" style="width:100%;  display: inline-block;">
					<div style="width:30%;float:left">Tipo de Memoria Ram</div>
					<div style="width:70%;float: left;">
						<input name="tipo_memoria_ram" id="tipo_memoria_ram" class="largeInput" value="{$post.tipo_memoria_ram}" />
					</div>
				</div>
				<hr>
			</div>
			<div class="grid_16 shared_field equipo_computo
						{if !in_array($post.tipo_recurso, ['equipo_computo'])}
						noShow{/if}">
				<div class="formLine" style="width:100%;  display: inline-block;">
					<div style="width:30%;float:left"> * Capacidad Disco Duro</div>
					<div style="width:70%;float: left;">
						<input name="disco_duro" id="disco_duro" class="largeInput" value="{$post.disco_duro}" />
					</div>
				</div>
				<hr>
			</div>
			<div class="grid_16 shared_field equipo_computo
						{if !in_array($post.tipo_recurso, ['equipo_computo'])}
						noShow{/if}">
				<div class="formLine" style="width:100%;  display: inline-block;">
					<div style="width:30%;float:left">Tipo de Disco Duro</div>
					<div style="width:70%;float: left;">
						<input name="tipo_disco_duro" id="tipo_disco_duro" class="largeInput" value="{$post.tipo_disco_duro}" />
					</div>

				</div>
				<hr>
			</div>
			<div class="grid_16 shared_field equipo_computo inmobiliaria
						{if !in_array($post.tipo_recurso, ['equipo_computo', 'inmobiliaria'])}
						noShow{/if}"">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> No. fisico</div>
						<div style="width:70%;float: left;">
							<input name="no_inventario" id="no_inventario" class="largeInput" value="{$post.no_inventario}"
									{if $post.tipo_recurso eq 'dispositivo' && $post.no_inventario}readonly{/if} />
						</div>
					</div>
					<hr>
			</div>
			<div class="grid_16 shared_field equipo_computo
						{if !in_array($post.tipo_recurso, ['equipo_computo'])}
						noShow{/if}">
				<div class="grid_16">
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
			<div class="grid_16 shared_field dispositivo
									{if !in_array($post.tipo_recurso, ['dispositivo'])}
									noShow{/if}">
				<div class="grid_16">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> * Tipo de dispositivo</div>
						<div style="width:70%;float: left;">
							<select class="largeInput" id=tipo_dispositivo name="tipo_dispositivo">
								<option value="">Seleccionar..</option>
								<option value="hdmi" {if $post.tipo_dispositivo eq "hdmi"}selected{/if}>HDMI</option>
								<option value="mousepad" {if $post.tipo_dispositivo eq "mousepad"}selected{/if}>Mousepad</option>
								<option value="mouse" {if $post.tipo_dispositivo eq "mouse"}selected{/if}>Mouse</option>
								<option value="teclado" {if $post.tipo_dispositivo eq "teclado"}selected{/if}>Teclado</option>
								<option value="ventilador" {if $post.tipo_dispositivo eq "ventilador"}selected{/if}>Ventilador</option>
								<option value="monitor" {if $post.tipo_dispositivo eq "monitor"}selected{/if}>Monitor</option>
								<option value="vga" {if $post.tipo_dispositivo eq "cable_ventilador"}selected{/if}>Cable VGA</option>
							</select>
						</div>
					</div>
				</div>
				<hr>
			</div>
			<div class="grid_16 shared_field software
									{if !in_array($post.tipo_recurso, ['software'])}
									noShow{/if}">
				<div class="grid_16">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> * Tipo de software</div>
						<div style="width:70%;float: left;">
							<select class="largeInput" id=tipo_software name="tipo_software">
								<option value="">Seleccionar..</option>
								<option value="aspel_coi" {if $post.tipo_software eq "aspel_coi"}selected{/if}>Aspel COI</option>
								<option value="aspel_noi" {if $post.tipo_software eq "aspel_noi"}selected{/if}>Aspel NOI</option>
								<option value="aspel_facture" {if $post.tipo_software eq "aspel_facture"}selected{/if}>Aspel Facture</option>
								<option value="admin_xml" {if $post.tipo_software eq "admin_xml"}selected{/if}>Admin XML</option>
								<option value="licencia_windows" {if $post.tipo_software eq "licencia_windows"}selected{/if}>Licencia de windows</option>
								<option value="office" {if $post.tipo_software eq "office"}selected{/if}>Office</option>
							</select>
						</div>
					</div>
				</div>
				<hr>
			</div>
			<div class="grid_16">
				<div class="grid_8">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> Marca</div>
						<div style="width:70%;float: left;">
							<input name="marca"  id="marca" type="text"  value="{$post.marca}" class="largeInput "/>
						</div>
					</div>
				</div>
				<div class="grid_8">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> Modelo</div>
						<div style="width:70%;float: left;">
							<input name="modelo"  id="modelo" type="text" value="{$post.modelo}" class="largeInput "/>
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
				<div class="grid_8">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left">* Fecha de compra</div>
						<div style="width:70%;float: left;">
							<input name="fecha_compra"  id="fecha_compra" onclick="CalendarioSimple(this)" value="{if $post.fecha_compra neq "0000-00-00"}{$post.fecha_compra|date_format:"%d-%m-%Y"}{/if}" type="text" class="largeInput "/>
						</div>
					</div>
				</div>
				<hr>
			</div>
			<div class="grid_16">
				<div class="grid_8">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> Costo de compra</div>
						<div style="width:70%;float: left;">
							<input name="costo_compra"  id="costo_compra" type="text"  value="{$post.costo_compra}" class="largeInput "/>
						</div>
					</div>
				</div>
				<div class="grid_8">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> Costo de Recuperación</div>
						<div style="width:70%;float: left;">
							<input name="costo_recuperacion"  id="costo_recuperacion" value="{$post.costo_recuperacion}" type="text" class="largeInput "/>
						</div>
					</div>
				</div>
				<hr>
			</div>
			<div class="grid_16 shared_field equipo_computo software {if !in_array($post.tipo_recurso, ['equipo_computo', 'software'])}noShow{/if}">
				<div class="grid_8">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> No. licencia</div>
						<div style="width:70%;float: left;">
							<input name="no_licencia"  id="no_licencia" type="text" value="{$post.no_licencia}" class="largeInput "/>
						</div>
					</div>
				</div>
				<div class="grid_8">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> Cod. activación</div>
						<div style="width:70%;float: left;">
							<input name="cod_activacion"  id="cod_activacion" type="text"  value="{$post.codigo_activacion}" class="largeInput "/>
						</div>
					</div>
				</div>
				<hr>
			</div>
            <div class="grid_16 shared_field software {if !in_array($post.tipo_recurso, ['software'])}noShow{/if}">
                <div class="grid_8">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:30%;float:left"> Fecha de vencimiento</div>
                        <div style="width:70%;float: left;">
                            <input name="vencimiento" id="vencimiento" type="text"
                                   onclick="CalendarioSimple(this)" value="{if $post.vencimiento !== null && $post.vencimiento !== ''}{$post.vencimiento|date_format:"%d-%m-%Y"}{/if}"
                                   class="largeInput " placeholder="dia-mes-año"/>
							<span style="color: #FFA500FF">Para software sin vencimiento, dejar vacio.</span>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
			<div class="grid_16 shared_field equipo_computo {if !in_array($post.tipo_recurso, ['equipo_computo'])}noShow{/if}">
				<div class="grid_16">
					<div style="text-align: left; padding-bottom:5px">Vincular dispositivos con el equipo de computo.</div>
				</div>
				<div class="grid_12">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> Dispositivos disponibles</div>
						<div style="width:70%;float: left;">
							<select name="device_id" id="device_id" class="largeInput">
								<option value="">Seleccionar.......</option>
								{foreach from=$devices item=item key=key}
									<option value="{$item.office_resource_id}">{$item.tipo_dispositivo|upper} {$item.marca} {$item.modelo} {$item.no_serie}</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>
				<div class="grid_4">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<a  href="javascript:;" class="button_grey spanAddDevice" style="margin: 0px">
							<span>Agregar</span>
						</a>
					</div>
				</div>
				<div class="grid_16" id="list_device">
					{include file="{$DOC_ROOT}/templates/lists/computo_device.tpl" listDevices=$post.device_resource}
				</div>
				<hr>
			</div>
			<div class="grid_16 shared_field equipo_computo {if !in_array($post.tipo_recurso, ['equipo_computo'])}noShow{/if}">
				<div class="grid_16">
					<div style="text-align: left; padding-bottom:5px">Vincular softwares con el equipo de computo.</div>
				</div>
				<div class="grid_12">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:30%;float:left"> Softwares disponibles</div>
						<div style="width:70%;float: left;">
							<select name="software_id" id="software_id" class="largeInput">
								<option value="">Seleccionar.......</option>
								{foreach from=$softwares item=item key=key}
									<option value="{$item.office_resource_id}">{$item.tipo_recurso|upper} {$item.tipo_software|upper} {$item.marca|upper} {$item.modelo|upper} {$item.no_serie|upper} {$item.no_licencia|upper} {$item.codigo_activacion|upper}</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>
				<div class="grid_4">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<a  href="javascript:;" class="button_grey spanAddSoftware" style="margin: 0px">
							<span>Agregar</span>
						</a>
					</div>
				</div>
				<div class="grid_16" id="list_software">
					{include file="{$DOC_ROOT}/templates/lists/computo_software.tpl" listSoftware=$post.software_resource}
				</div>
				<hr>
			</div>
			<div class="grid_16 shared_field equipo_computo inmobiliaria
						{if !in_array($post.tipo_recurso, ['equipo_computo', 'inmobiliaria'])}
						noShow{/if}">
				<div class="formLine" style="width:100%;  display: inline-block;">
					<div style="width:30%;float:left"> Observaciones</div>
					<div style="width:70%;float: left;">
						<textarea name="descripcion" id="descripcion" class="largeInput" rows="10">{$post.descripcion}</textarea>
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
                    <a href="javascript:;"  id="btnResource" class="button_grey"><span>{if $post}Actualizar{else}Guardar{/if}</span></a>
                </div>
            </div>

		</fieldset>
	</form>
</div>

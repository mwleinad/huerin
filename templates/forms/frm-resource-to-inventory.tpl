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
						<option value="dispositivo" {if $post.tipo_recurso eq "dispositivo"}selected{/if}>Dispositivo</option>
						<option value="equipo_computo" {if $post.tipo_recurso eq "equipo_computo"}selected{/if}>Equipo de computo</option>
						<option value="software" {if $post.tipo_recurso eq "software"}selected{/if}>Software</option>
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
						<textarea name="descripcion" id="descripcion" class="largeInput" rows="10">{$post.descripcion}</textarea>
					</div>

				</div>
				<hr>
			</div>
			<div class="grid_16 shared_field equipo_computo
						{if !in_array($post.tipo_recurso, ['equipo_computo'])}
						noShow{/if}"">
				<div class="formLine" style="width:100%;  display: inline-block;">
					<div style="width:30%;float:left"> Procesador</div>
					<div style="width:70%;float: left;">
						<input name="procesador" id="procesador" class="largeInput" value="{$post.procesador}" />
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
			<div class="grid_16 shared_field equipo_computo {if !in_array($post.tipo_recurso, ['equipo_computo'])}noShow{/if}">
				<div class="grid_4">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:40%;float:left"> Mouse</div>
						<div style="width:30%;float: left;">
							<input name="con_mouse"  id="con_mouse" type="checkbox" class="largeInput " {if $post.con_mouse}checked{/if}/>
						</div>
					</div>
				</div>
				<div class="grid_4">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:40%;float:left"> Mousepad</div>
						<div style="width:30%;float: left;">
							<input name="con_mousepad"  id="con_mousepad" type="checkbox" class="largeInput " {if $post.con_mousepad}checked{/if}/>
						</div>
					</div>
				</div>
				<div class="grid_4">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:40%;float:left"> Teclado</div>
						<div style="width:30%;float: left;">
							<input name="con_teclado"  id="con_teclado" type="checkbox" class="largeInput" {if $post.con_teclado}checked{/if}/>
						</div>
					</div>
				</div>
				<div class="grid_4">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:40%;float:left"> Monitor</div>
						<div style="width:30%;float: left;">
							<input name="con_monitor"  id="con_monitor" type="checkbox" class="largeInput" {if $post.con_monitor}checked{/if}/>
						</div>
					</div>
				</div>
				<div class="grid_4">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:40%;float:left"> Ethernet</div>
						<div style="width:30%;float: left;">
							<input name="con_ethernet"  id="con_ethernet" type="checkbox" class="largeInput" {if $post.con_ethernet}checked{/if}/>
						</div>
					</div>
				</div>
				<div class="grid_4">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:40%;float:left"> Hdmi</div>
						<div style="width:30%;float: left;">
							<input name="con_hdmi"  id="con_hdmi" type="checkbox" class="largeInput" {if $post.con_hdmi}checked{/if}/>
						</div>
					</div>
				</div>
				<div class="grid_4">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:40%;float:left"> Cuenta con hub usb</div>
						<div style="width:30%;float: left;">
							<input name="hub_usb"  id="hub_usb" type="checkbox" class="largeInput " {if $post.con_hubusb}checked{/if}/>
						</div>
					</div>
				</div>
				<div class="grid_4">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:40%;float:left"> Cuenta con no break</div>
						<div style="width:30%;float: left;">
							<input name="no_break"  id="no_break" type="checkbox" class="largeInput" {if $post.con_nobreak}checked{/if}/>
						</div>
					</div>
				</div>
				<div class="grid_4">
					<div class="formLine" style="width:100%;  display: inline-block;">
						<div style="width:40%;float:left"> Ventilador</div>
						<div style="width:30%;float: left;">
							<input name="con_ventilador"  id="con_ventilador" type="checkbox" class="largeInput" {if $post.con_ventilador}checked{/if}/>
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

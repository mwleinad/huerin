<div id="divForm">
	<form id="{$data.nameForm}" name="{$data.nameForm}" method="post" onsubmit="return false">
		<input type="hidden" id="type" name="type" value="{$data.nameType}"/>
		{if $post}
			<input type="hidden" id="changeId" name="changeId" value="{$post.changeId}"/>
		{/if}
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Descripcion:</div>
				<div style="width:70%;display:inline-block">
					<textarea name="descripcion" id="descripcion" class="largeInput medium">{$post.descripcion}</textarea>
				</div>
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Modulo:</div>
				<div style="width:70%;display:inline-block">
					<select name="modulo" id="modulo" class="largeInput medium">
						<option value="">Seleccionar modulo</option>
						<option value="catalogos" {if $post.modulo eq "catalogos"}selected{/if}>Catalogos</option>
						<option value="clientes" {if $post.modulo eq "clientes"}selected{/if}>Clientes</option>
						<option value="servicios" {if $post.modulo eq "servicios"}selected{/if}>Servicios</option>
						<option value="cxc" {if $post.modulo eq "cxc"}selected{/if}>CxC</option>
						<option value="facturacion" {if $post.modulo eq "facturacion"}selected{/if}>Facturacion</option>
						<option value="departamentos" {if $post.modulo eq "departamentos"}selected{/if}>Departamentos</option>
						<option value="reportes" {if $post.modulo eq "reportes"}selected{/if}>Reportes</option>
						<option value="cafeteria" {if $post.modulo eq "cafeteria"}selected{/if}>Cafeteria</option>
						<option value="configuracion" {if $post.modulo eq "configuracion"}selected{/if}>Configuracion</option>
					</select>
				</div>
			</div>
			{if $post}
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Fecha de solicitud:</div>
				<div style="width:70%;display:inline-block">
					<input  name="fsolicitud" id="fsolicitud" class="largeInput medium" onclick="CalendarioSimple(this)" value="{if $post.fechaSolicitud!="0000-00-00"}{$post.fechaSolicitud|date_format:"%d-%m-%Y"}{/if}"/>
				</div>
			</div>
			{/if}
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Fecha de entrega:</div>
				<div style="width:70%;display:inline-block">
					<input  name="fentrega" id="fentrega" class="largeInput medium" onclick="CalendarioSimple(this)" value="{if $post.fechaEntrega!="0000-00-00"}{$post.fechaEntrega|date_format:"%d-%m-%Y"}{/if}" />
				</div>
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Fecha de revision:</div>
				<div style="width:70%;display:inline-block">
					<input  name="frevision" id="frevision" class="largeInput medium" onclick="CalendarioSimple(this)" value="{if $post.fechaRevision!="0000-00-00"}{$post.fechaRevision|date_format:"%d-%m-%Y"}{/if}" />
				</div>
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Adjuntar archivo</div>
				<div style="width:70%;display:inline-block">
					<input type="file" name="adjunto" id="adjunto" class="largeInput medium" />
					{if $post.fileExist}
						<br><span>Ya existe un archivo, si desea actualizar adjunte un nuevo archivo.</span>
					{/if}
				</div>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<div style="display:inline-block;text-align: center;">
					<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
					<a class="button_grey" id="btnPending" name="btnPending"><span>{$data.nameBtn}</span></a>
				</div>
			</div>
		</fieldset>
	</form>
</div>

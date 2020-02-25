<tr id="1">
	<td align="center" class="id">{$item.razonSocial}</td>
	<td align="center">{$item.rfc}</td>
	<td align="center">{$item.calle} {$item.noExt} {$item.noInt} {$item.ciudad} {$item.estado} {$item.cp} {$item.pais}</td>
	<td align="center">{if $item.dataCertificate.noCertificado}{$item.dataCertificate.noCertificado}{else}<span style="color: red">Pendiente por subir certificado del sat</span>{/if}</td>
	<td align="center">{$item.dataCertificate.expireDate}</td>
	<td align="center">
		<img src="{$WEB_ROOT}/images/icons/zip-icon.png" width="16px" class="spanCertificate" id="{$item.rfcId}" title="Subir o actualizar certificado"/>
		<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.rfcId}" title="Eliminar"/>
		<img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.rfcId}" title="Editar"/>
		{if $item.dataCertificate.noCertificado}
			<a href="{$WEB_ROOT}/admin-folios/nuevos-folios/id/{$item.rfcId}" target="_blank">
				<img src="{$WEB_ROOT}/images/folios.png" width="16px" class="spanAll" title="Administrar folios"/>
			</a>
		{/if}
	</td>
</tr>


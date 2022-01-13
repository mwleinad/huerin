{if count($data.items)}
<table style="width: 100%; border-collapse: collapse" id="box-table-a">
	<thead>
		<th>Fecha</th>
		<th>Realizado por</th>
		<th>Modulo</th>
		<th></th>
	</thead>
<tbody>
	{foreach from=$data.items item=item key=key}
    	<tr>
			<td>{$item.fecha_registro|date_format:"%d-%m-%Y"}</td>
			<td>{$item.usuario_realizo}</td>
			<td>{$item.id_modulo}</td>
			<td>
				<a href="javascript:;" title="Enviar notificación a empresas" onclick="openEnviarRecotizacion({$item.id})">
					<img src="{$WEB_ROOT}/images/icons/send-email.png">
				</a>
			</td>
		</tr>
	{/foreach}
</tbody>
</table>
	<div class="pagination" style="text-align: right">
		{if count($data.pages)}
			{include file="{$DOC_ROOT}/templates/lists/pages_ajax.tpl" pages=$data.pages handler='cargarListaImportacion'}
		{/if}
	</div>

{else}
<div align="center">No existen registros.</div>
{/if}
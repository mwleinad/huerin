{if count($options)}
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	<thead>
		<tr>
			<th>Respuesta</th>
			<th>Precio</th>
			<th></th>
		</tr>
	</thead>
<tbody>
	{foreach from=$options item=item key=key}
		<tr>
			<td>{$item.text}</td>
			<td>$ {$item.price|number_format:2:'.':','}</td>
			<td>
				<a href="javascript:;" title="Editar" class="spanControlOption" data-type="editOption" data-key="{$key}">
					<img src="{$WEB_ROOT}/images/icons/edit.gif">
				</a>
				<a href="javascript:;" title="Eliminar" class="spanDeleteOption" data-type="deleteOption" data-key="{$key}">
					<img src="{$WEB_ROOT}/images/icons/delete.png">
				</a>
			</td>
		</tr>
	{/foreach}
</tbody>
</table>
{else}
<div align="center">No existen respuestas para esta pregunta.</div>
{/if}

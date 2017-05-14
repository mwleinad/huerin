{foreach from=$impuestos item=item key=key}
	<tr id="1">
		<td align="center">{$item.impuestoNombre}</td>
		<td class="act">
			<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.contractImpuestoId}"/>
		</td>
	</tr>
{/foreach}

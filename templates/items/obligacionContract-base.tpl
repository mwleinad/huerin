{foreach from=$obligaciones item=item key=key}
	<tr id="1">
		<td align="center">{$item.obligacionNombre}</td>
		<td class="act">
			<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.contractObligacionId}"/>
		</td>
	</tr>
{/foreach}

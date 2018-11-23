{foreach from=$menus.items item=item key=key}
	<tr id="1">
		<td align="center">{$item.menuId}</td>
		<td align="center">{$item.fecha}</td>
		<td align="center">
			<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDown" id="{$item.menuId}" title="Eliminar menu"/>
			<a class="spanEdit" title="Ver menu" href="{$WEB_ROOT}/vp_menu/menu/{$item.menuId}" target="_blank">
				<img src="{$WEB_ROOT}/images/icons/restaurant.png"/>
			</a>

		</td>
	</tr>
{foreachelse}
<tr><td colspan="3" style="text-align: center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}

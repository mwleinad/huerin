{foreach from=$results item=item key=key}
	<tr id="1">
		<td>{$item.nombre}</td>
		<td>
			<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="span-eliminar-clasificacion" data-id="{$item.id}" title="Eliminar"/>
            <img src="{$WEB_ROOT}/images/icons/edit.gif" class="span-control-clasificacion" data-id="{$item.id}"  data-type='1' title="Editar"/>
		</td>
	</tr>
{foreachelse}
<tr><td colspan="2"  style="text-align: center;">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}

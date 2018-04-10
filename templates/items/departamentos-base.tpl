{foreach from=$resDepartamentos item=item key=key}
	<tr id="1">
		<td align="center">{$item.departamento}</td>
		<td class="act" align="center">
{*
			<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.departamentoId}" title="Eliminar"/></span>
*}
			{if $item.departamentoId != "1" && in_array(54,$permissions) || $User.isRoot}
			 	<img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.departamentoId}" title="Editar"/></a>
			{/if}
		</td>
	</tr>
{/foreach}

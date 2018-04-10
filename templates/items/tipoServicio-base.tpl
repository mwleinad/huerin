{foreach from=$resTipoServicio.items item=item key=key}
	<tr id="1">
		<td align="center" width="30%">{$item.nombreServicio}</td>
		<td align="center" width="30%">{$item.periodicidad}</td>
		<td align="center" width="20%">{$item.totalPasos}</td>
		<td  align="center" width="20%">
		{if in_array(27,$permissions) || $User.isRoot}
	  		<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.tipoServicioId}"  title="Eliminar"/>
		{/if}
        {if in_array(26,$permissions) || $User.isRoot}
      		<img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.tipoServicioId}"  title="Editar"/>
		{/if}
        {if in_array(28,$permissions) || $User.isRoot}
      		<a href="{$WEB_ROOT}/service-steps/id/{$item.tipoServicioId}" onclick="return parent.GB_show('Pasos del Servicio', this.href,500,970) "><img src="{$WEB_ROOT}/images/icons/config.gif" title="Configurar Servicio"/></a>
		{/if}
		</td>
	</tr>
{/foreach}

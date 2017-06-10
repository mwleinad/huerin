<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<th align="center" width="60">Comentario</th>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">C. Asignado</th>
		<th align="center" width="60">Razon Social</th>
		<th align="center" width="50">Ene</th>
		<th align="center" width="50">Feb</th>
		<th align="center" width="50">Mar</th>
		<th align="center" width="50">Abr</th>
		<th align="center" width="50">May</th>
		<th align="center" width="50">Jun</th>
		<th align="center" width="50">Jul</th>
		<th align="center" width="50">Ago</th>
		<th align="center" width="50">Sep</th>
		<th align="center" width="50">Oct</th>
		<th align="center" width="50">Nov</th>
		<th align="center" width="50">Dic</th>
	</tr>
</thead>
<tbody>
<pre>
	</pre>
{foreach from=$cleanedArray item=item key=key}
		<tr>
			<td align="center" class="" title="{$item.nameContact}">
				<span id="comentario-{$item.servicioId}">{$item.comentario}</span>
				{if $User.roleId < 4}
				<img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.servicioId}" onclick="ModifyComment({$item.servicioId})"  title="Editar"/>
				<a href="{$WEB_ROOT}/download_all_tasks.php?id={$item.servicioId}" style="color:#FFF;font-weight:bold"><img src="{$WEB_ROOT}/images/b_disc.png" class="spanEdit" id="{$item.servicioId}" title="Descargar todos los archivos"/></a>
				{/if}
			</td>
    		<td align="center" class="" title="{$item.nameContact}">{$item.nameContact}</td>
    		<td align="center" class="" title="{$item.responsable}">{$item.responsable}</td>
    		<td align="center" class="" title="{$item.name}">{$item.name}</td>
        {foreach from=$item.instanciasServicio item=instanciaServicio}
                <td align="center"
                  class="{if $instanciaServicio.status neq 'inactiva'}
                        {if $instanciaServicio.class eq 'CompletoTardio'}
                          st{'Completo'} txtSt{'Completo'}
                        {else}
                          {if $instanciaServicio.class eq 'Iniciado'}
                            st{'PorCompletar'} txtSt{'PorCompletar'}
                          {else}
                            st{$instanciaServicio.class} txtSt{$instanciaServicio.class}
                          {/if}
                        {/if}
                      {/if}"
                  title="{$item.nombreServicio} {if $instanciaServicio.status neq 'inactiva'}{if $instanciaServicio.class eq 'CompletoTardio'}{'Completo'}{else}{if $instanciaServicio.class eq 'Iniciado'}{'PorCompletar'}{else}{$instanciaServicio.class}{/if}{/if}{/if}">
                <div style="cursor:pointer" onclick="GoToWorkflow('report-servicios', '{$instanciaServicio.instanciaServicioId}')">	
                {$item.nombreServicio|truncate:5:""}
                {if $instanciaServicio.status eq 'inactiva'}<span style="color:#DA9696">(Inactivo)</span>{/if}
                <a href="{$WEB_ROOT}/download_tasks.php?id={$instanciaServicio.instanciaServicioId}" style="color:#FFF;font-weight:bold">Archivos</a>
                </div>
                </td>
        {/foreach}

{foreachelse}
<tr>
	<td colspan="15" align="center">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}


</tbody>
</table>
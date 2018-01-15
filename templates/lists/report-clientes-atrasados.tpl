{assign var=meses value=['1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Noviembre']}
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<th colspan="12"></th>
	</tr>
	<tr>
		<th colspan="12" style="font-size:16px">Esto es un reporte de clientes que cuentan con un retraso en los servicios</th>
	</tr>
	<tr>
		<th colspan="12" style="font-size:16px">Si tiene alguna duda, favor de consultar con el administrador del sistema.</th>
	</tr>
	<tr>
		<th align="center" width="60">Comentario</th>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">C. Asignado</th>
		<th align="center" width="60">Razon Social</th>
	</tr>
</thead>
<tbody>
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
				<br><a style="color:#FFF;font-weight:bold">{($meses[$instanciaServicio.mes]|UPPER)|truncate:3:""}-{$instanciaServicio.anio}</a>
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
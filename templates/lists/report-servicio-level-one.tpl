<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<th align="center" width="10"></th>
		<th align="center" width="10">Comentario</th>
		<th align="center" width="60">Cliente</th>
	</tr>
</thead>
<tbody>
<pre>
	</pre>
{foreach from=$cleanedArray item=item key=key}
		<tr>
			<td><a href="javascript:;" title="Mas">[+]</a></td>
			<td align="center" class="" title="{$item.nameContact}">
				<span id="comentario-{$item.servicioId}">{$item.comentario}</span>
				{if $User.roleId < 4}
				<img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.servicioId}" onclick="ModifyComment({$item.servicioId})"  title="Editar"/>
				<a href="{$WEB_ROOT}/download_all_tasks.php?id={$item.servicioId}" style="color:#FFF;font-weight:bold"><img src="{$WEB_ROOT}/images/b_disc.png" class="spanEdit" id="{$item.servicioId}" title="Descargar todos los archivos"/></a>
				{/if}
			</td>
    		<td align="center" class="" title="{$item.nameContact}">{$item.nameContact}</td>
{foreachelse}
<tr>
	<td colspan="3" align="center">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}


</tbody>
</table>
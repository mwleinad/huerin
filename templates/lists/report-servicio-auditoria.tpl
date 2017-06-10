<script>
function ActivateFileOption(id)
{
	console.log(id);
	console.log($(id));
	$('file_'+id).click();
	$('enviar_'+id).enable();
}
</script>
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">C. Asignado</th>
		<th align="center" width="60">Razon Social</th>
		<th align="center" width="60">Servicio</th>
		<th align="center" width="50">{$mes}</th>
	</tr>
</thead>
<tbody>

{if $cleanedArray}

{foreach from=$cleanedArray item=item key=key}


		<tr>
    		<td align="center" class="" title="{$item.nameContact}">{$item.nameContact}</td>
    		<td align="center" class="" title="{$item.responsable}">{$item.responsable}</td>
    		<td align="center" class="" title="{$item.name}">{$item.name}</td>
    	<td align="center" class="" title="{$contract.responsable.name}">{$item.nombreServicio}</td>
    	{foreach from=$item.instanciasServicio item=instanciaServicio}

    		{for $foo=0 to $maxSteps-1}
				{if $instanciaServicio.steps.$foo.nombreStep!=""}
      	{foreach from=$instanciaServicio.steps.$foo.tasks key=keyTask item=task}
            <td width="100px" align="center" class="{if $instanciaServicio.steps.$foo.class eq 'CompletoTardio'}
      							st{'Completo'} txtSt{'Completo'}
      						{else}
      							{if $instanciaServicio.steps.$foo.class eq 'Iniciado'}
      								st{'PorCompletar'} txtSt{'PorCompletar'}
      							{else}
      								st{$instanciaServicio.steps.$foo.class} txtSt{$instanciaServicio.steps.$foo.class}
      							{/if}
      						{/if}"
					 title="{$servicio.nombreServicio}{if $instanciaServicio.steps.$foo.class eq 'CompletoTardio'}{'Completo'}{else}{if $instanciaServicio.steps.$foo.class eq 'Iniciado'}{'PorCompletar'}{else}{$instanciaServicio.steps.$foo.class}{/if}{/if}">
	            <a href="#" style="color:#fff; font-style:italic" onclick="GoToWorkflow('report-servicios', '{$instanciaServicio.instanciaServicioId}')">Paso {$foo+1} {$instanciaServicio.steps.$foo.nombreStep}</a><br />
            	{$task.nombreTask}<br />
            	{if $task.control}
              	<b style="cursor:pointer; text-decoration:underline" onclick="ActivateFileOption('{$instanciaServicio.instanciaServicioId}_{$task.taskId}_{$instanciaServicio.steps.$foo.stepId}')" title="{$task.control}">C1: {$task.control|truncate:20:"...":true}</b>
                {if $task.controlFile}
                  <span style="color:#fff; text-align:left"><br />
                  {foreach from=$task.controlFileInfo item=file}
                  Version: {$file.version} Fecha: {$file.date}<br />
                  <a href="{$WEB_ROOT}/download.php?file=tasks/{$file.servicioId}_{$file.stepId}_{$file.taskId}_{$file.control}_{$file.version}.{$file.ext}" target="_blank" style="color:#fff">&raquo; Ver </a>
                  {if $User.tipoPers == "Asistente" || $User.tipoPersonal == "Socio" || $User.tipoPersonal == "Gerente"}
									<span><a href="{$WEB_ROOT}/delete_task.php?id={$instanciaServicio.instanciaServicioId}&delete={$file.taskFileId}" onclick="return confirm('Esta seguro de eliminar este archivo?')" style='color:#fff'>&raquo; Borrar</a></span>
                  {/if}
                  <br />
                  {/foreach}	
                  </span>
                    {if $instanciaServicio.status neq "inactiva"}
                    <form method="post" enctype="multipart/form-data" style="font-size:8px">
                      <input type="hidden" id="stepId" name="stepId" value="{$instanciaServicio.steps.$foo.stepId}" />
                      <input type="hidden" id="taskId" name="taskId" value="{$task.taskId}" />
                      <input type="hidden" id="servicioId" name="servicioId" value="{$instanciaServicio.instanciaServicioId}" />
                      <input type="hidden" id="control" name="control" value="1" />
                      <input type="hidden" id="uplToken" name="uplToken" value="{$uplToken}" />
                      <input type="file" id="file_{$instanciaServicio.instanciaServicioId}_{$task.taskId}_{$instanciaServicio.steps.$foo.stepId}" name="file"  style="font-size:10px; width:70px; display:none" />
                      <input type="submit" id="enviar_{$instanciaServicio.instanciaServicioId}_{$task.taskId}_{$instanciaServicio.steps.$foo.stepId}" value="Enviar" class="btnEnviar" onclick="HideButtons()"  style="font-size:10px" />
                    </form>
                    {/if}
                {else}
                  {if $instanciaServicio.status neq "inactiva"}
                  <form method="post" enctype="multipart/form-data"  style="font-size:8px">
                     <input type="hidden" id="stepId" name="stepId" value="{$instanciaServicio.steps.$foo.stepId}" />
                      <input type="hidden" id="taskId" name="taskId" value="{$task.taskId}" />
                      <input type="hidden" id="servicioId" name="servicioId" value="{$instanciaServicio.instanciaServicioId}" />
                      <input type="hidden" id="control" name="control" value="1" />
                      <input type="hidden" id="uplToken" name="uplToken" value="{$uplToken}" />
                      <input type="file" id="file_{$instanciaServicio.instanciaServicioId}_{$task.taskId}_{$instanciaServicio.steps.$foo.stepId}" name="file"  style="font-size:10px; width:70px; display:none" />
                      <input type="submit" id="enviar_{$instanciaServicio.instanciaServicioId}_{$task.taskId}_{$instanciaServicio.steps.$foo.stepId}" value="Enviar" class="btnEnviar" onclick="HideButtons()"   style="font-size:10px" disabled="disabled"/>
                    </form>
                    {/if}
                {/if}
							{else}
              	N/A
              {/if}  
         </td> 
        {/foreach}
      
      </div>
				</td>
				{else}
				<td  colspan="3"></td>
				{/if}
    		{/for}
    	{/foreach}
		</tr>
{/foreach}
{/if}

</tbody>
</table>
<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="catalogos">Workflow  </h1>
  </div>
  
  
  <div class="clear">
  </div>
  
  <div id="portlets">
	Cliente: <b>{$myWorkflow.customerName}</b> Razon Social:<b>{$myWorkflow.contractName}</b> Fecha:
  {$myWorkflow.date} | <a href="{$WEB_ROOT}/servicios-cliente">Regresar</a><br />
  <div class="clear"></div>
  
  <div class="portlet">
      <div class="portlet-content nopadding borderGray" id="contenido" style="padding:15px">
          
     	{foreach from=$myWorkflow.steps item=step}
      	
      	<div style=" cursor:pointer; width:150px; float:left; height:100px; min-height:100px; border:solid; border-width:1px; margin:5px; padding:5px; text-align:center; {if $step.stepCompleted}background-color:#006633; color:#FFFFFF{else}background-color:#C00; color:#FFFFFF{/if}" onclick="ToggleTask({$step.stepId})">
        	Paso No. {$step.step}<br />
        	<b>{$step.nombreStep}</b><br />
          &raquo; Click para Ver Tareas &laquo;
          {if $step.stepCompleted}Completado{/if}
        </div>
        {if $step.step < $myWorkflow.totalSteps}
      	<div style="width:50px; float:left; height:70px; min-height:70px; border:solid; border-width:0px; margin:0px; padding:5px">
        	<img src="{$WEB_ROOT}/images/arrow.png" />
        </div>
        {/if}
      {/foreach}
      <div style="clear:both"></div>
      
     	{foreach from=$myWorkflow.steps item=step key=key}
      <div style="border:solid; border-width:1px; margin:10px; padding:10px; {if $stepId == $step.stepId}display:block{/if}display:none" id="step-{$step.stepId}" class="tasks">
     	{if $key == 0 || $step.prevStep.completed == 1}
     	<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
				<thead>
          <tr>
            <th width="100">Nombre</th>
            <th width="80">Dia de Vencimiento</th>
            <th width="80">Dias de Prorroga</th>
            <th width="350">Controles</th>     
          </tr>
        </thead>
				<tbody>
      	{foreach from=$step.tasks item=task}
          <tr id="1">
            <td align="center" class="id">{$task.nombreTask}</td>
            <td align="center" class="id">
            {$task.diaVencimiento} - Per: {$myWorkflow.periodicidad}
            </td>
            <td align="center" class="id">{$task.prorroga} Dias</td>
            <td align="center">
            	{if $task.control}
              	<b>Control 1: {$task.control}</b>
                {if $task.controlFile}
                  <img src="{$WEB_ROOT}/images/icons/activate.png" />
                  <span style="color:#093"><br />
                  {foreach from=$task.controlFileInfo item=file}
                  Version: {$file.version} Fecha: {$file.date}<a href="{$WEB_ROOT}/download.php?file=tasks/{$file.servicioId}_{$file.stepId}_{$file.taskId}_{$file.control}_{$file.version}.{$file.ext}" target="_blank">Ver Archivo</a><br />
                  {/foreach}	</span>
                {/if}
							{else}
              	N/A
              {/if}            

           
                  
              </td>  
          </tr>
        {foreachelse}
        <tr><td colspan="4" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
        {/foreach}
				</tbody>
			</table>
          {else}
          	No puedes realizar esta tarea hasta completar las anteriores.
          {/if}
      </div>
      {/foreach}
      </div>
      
    </div>

 </div>
  <div class="clear"> </div>

</div>
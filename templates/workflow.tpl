<div class="grid_16" id="content">
  <div class="grid_9">
  <h1 class="catalogos">Workflow  </h1>
  </div>
  <div class="clear">
  </div>
  <div id="portlets">
      <form method="post" name="frmWorkFlow" id="frmWorkFlow" onsubmit="return false">
          <input type="hidden" id="idWorkFlow" name="idWorkFlow" value="{$workFlowId}">
          <input type="hidden" id="type" name="type" value="changeDateWorkFlow">
	Cliente: <b>{$myWorkflow.customerName}</b> Razon Social:<b>{$myWorkflow.contractName}</b> Fecha:
   <input class="form-control btn btn-xs green" type="button" name="date-workflow"  id="date-workflow"  {if in_array(116,$permissions)||$User.isRoot}onclick="Calendario(this)"{/if} value="{$myWorkflow.date}" />
  </span> |{if in_array(101,$permissions)||$User.isRoot}<a href="{$WEB_ROOT}/download_tasks.php?id={$workFlowId}" style="font-weight:bold">Descargar Archivos</a>{/if} | <a href="{$WEB_ROOT}/{$from}">Regresar</a><br /></form>
  <div class="clear"></div>
  <div class="portlet">
      <div class="portlet-content nopadding borderGray" id="contenido" style="padding:15px">
          
     	{foreach from=$myWorkflow.steps item=step}
      	<div style=" cursor:pointer; width:150px; float:left; height:100px; min-height:100px; border:solid; border-width:1px; margin:5px; padding:5px; text-align:center; {if $step.stepCompleted}background-color:#006633; color:#FFFFFF{else}background-color:#C00; color:#FFFFFF{/if}" {if in_array(102,$permissions)||$User.isRoot}onclick="ToggleTask({$step.stepId})"{/if}>
        	Paso No. {$step.step}<br /><b>{$step.nombreStep}</b><br />
            {if in_array(102,$permissions)||$User.isRoot}
             &raquo; Click para Ver Tareas &laquo;
            {/if}
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
     	{*if $key == 0 || $step.prevStep.completed == 1*}
     	{if $key == 0 || 1 == 1}
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
                      Version: {$file.version} Fecha: {$file.date}
                      {if in_array(104,$permissions)||$User.isRoot}
                        <a href="{$WEB_ROOT}/download.php?file=tasks/{$file.servicioId}_{$file.stepId}_{$file.taskId}_{$file.control}_{$file.version}.{$file.ext}" target="_blank">&raquo; Ver Archivo</a>
                      {/if}
                      {if in_array(105,$permissions)||$User.isRoot}
                          {if $isDep}
                            <span><a href="{$WEB_ROOT}/delete_task.php?id={$myWorkflow.instanciaServicioId}&delete={$file.taskFileId}" onclick="return confirm('Esta seguro de eliminar este archivo?')">&raquo; Borrar Archivo</a></span>
                          {/if}
                      {/if}
                      <br />
                      {/foreach}
                  </span>
                  {if $myWorkflow.status neq "inactiva"}
                    {if in_array(103,$permissions)||$User.isRoot}
                        {if $isDep}
                            <form method="post" enctype="multipart/form-data">
                              <input type="hidden" id="stepId" name="stepId" value="{$step.stepId}" />
                              <input type="hidden" id="taskId" name="taskId" value="{$task.taskId}" />
                              <input type="hidden" id="servicioId" name="servicioId" value="{$myWorkflow.instanciaServicioId}" />
                              <input type="hidden" id="control" name="control" value="1" />
                              <input type="hidden" id="uplToken" name="uplToken" value="{$uplToken}" />
                              <input type="file" id="file" name="file" />
                              <input type="submit" value="Enviar" class="btnEnviar" onclick="HideButtons()" />
                            </form>
                        {/if}
                    {/if}
                   {/if}
                {else}
                  <img src="{$WEB_ROOT}/images/icons/cancel.png" />
                  {if $myWorkflow.status neq "inactiva"}
                      {if in_array(103,$permissions)||$User.isRoot}
                          {if $isDep}
                              <form method="post" enctype="multipart/form-data">
                              <input type="hidden" id="stepId" name="stepId" value="{$step.stepId}" />
                              <input type="hidden" id="taskId" name="taskId" value="{$task.taskId}" />
                              <input type="hidden" id="servicioId" name="servicioId" value="{$myWorkflow.instanciaServicioId}" />
                              <input type="hidden" id="control" name="control" value="1" />
                              <input type="hidden" id="uplToken" name="uplToken" value="{$uplToken}" />
                              <input type="file" id="file" name="file" />
                              <input type="submit" value="Enviar" class="btnEnviar" onclick="HideButtons()" />
                              </form>
                          {/if}
                      {/if}
                    {/if}
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
      <div style="clear:both"> </div>           
      <div class="formLine" style="text-align:center; margin-left:420px">            
      {if $myWorkflow.status neq "inactiva"}
          {if in_array(106,$permissions)||$User.isRoot}
            {if $isDep}
                <a class="button_notok" id="btnAddCancelWorkFlow" onclick="CancelarWorkFlow({$workFlowId})"><span>Desactivar</span></a>
            {/if}
          {/if}
       {else}
          {if in_array(106,$permissions)||$User.isRoot}
            <a class="button_ok" id="btnAddCancelWorkFlow" onclick="ReactivarWorkFlow({$workFlowId})"><span>Activar</span></a>
          {/if}
       {/if}
  <div class="clear"> </div>

</div>
{foreach from=$myWorkflow.steps item=step key=key}
    <div style="border:solid; border-width:1px; margin:10px; padding:10px; {if $stepId == $step.stepId}display:block{/if}display:none" id="step-{$step.stepId}{$workFlowId}" class="tasks">
    <div id="msg-step-{$step.stepId}" style="text-align: center; background-color: #2ae0bb;display:none"></div><br>
    <div style="{if $step.stepCompleted}background-color: #0b4d3f; color:white{/if} ">{$workFlowId} >> Paso No.{$step.step} {$step.nombreStep}</div><br>
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
                                    <span style="color:#093" id="spanStep-{$step.stepId}{$task.taskId}{$workFlowId}"><br />
                                        {foreach from=$task.controlFileInfo item=file}
                                            Version: {$file.version} Fecha: {$file.date}
                                                <a href="{$WEB_ROOT}/download.php?file=tasks/{$file.servicioId}_{$file.stepId}_{$file.taskId}_{$file.control}_{$file.version}.{$file.ext}" target="_blank">&raquo; Ver Archivo</a>
                                            {if $tipoPersonal == "Asistente" || $tipoPersonal == "Socio" || $tipoPersonal == "Gerente"}
                                                <span><a href="{$WEB_ROOT}/delete_task.php?id={$myWorkflow.instanciaServicioId}&delete={$file.taskFileId}" onclick="return confirm('Esta seguro de eliminar este archivo?')">&raquo; Borrar Archivo</a></span>
                                            {/if}
                                        <br />
                                        {/foreach}
                                    </span>
                                    {if $myWorkflow.status neq "inactiva"}
                                        <form method="post" enctype="multipart/form-data" onsubmit="return false;" id="frmFile{$step.stepId}{$task.taskId}{$workFlowId}">
                                            <input type="hidden" id="stepId" name="stepId" value="{$step.stepId}" />
                                            <input type="hidden" id="taskId" name="taskId" value="{$task.taskId}" />
                                            <input type="hidden" id="servicioId" name="servicioId" value="{$myWorkflow.instanciaServicioId}" />
                                            <input type="hidden" id="control" name="control" value="1" />
                                            <input type="hidden" id="uplToken" name="uplToken" value="{$uplToken}" />
                                            <input type="hidden" id="instanciaId" name="instanciaId" value="{$workFlowId}" />
                                            <input type="file" id="file" name="file" />
                                            <input type="submit" value="Enviar" class="btnEnviar" onclick="UploadFile('{$step.stepId}{$task.taskId}{$workFlowId}')" />
                                        </form>
                                    {/if}
                                {else}
                                    <img src="{$WEB_ROOT}/images/icons/cancel.png" />
                                    {if $myWorkflow.status neq "inactiva"}
                                        <form method="post" enctype="multipart/form-data">
                                            <input type="hidden" id="stepId" name="stepId" value="{$step.stepId}" />
                                            <input type="hidden" id="taskId" name="taskId" value="{$task.taskId}" />
                                            <input type="hidden" id="servicioId" name="servicioId" value="{$myWorkflow.instanciaServicioId}" />
                                            <input type="hidden" id="control" name="control" value="1" />
                                            <input type="hidden" id="uplToken" name="uplToken" value="{$uplToken}" />
                                            <input type="hidden" id="instanciaId" name="instanciaId" value="{$workFlowId}" />
                                            <input type="file" id="file" name="file" />
                                            <input type="submit" value="Enviar" class="btnEnviar" onclick="HideButtons()" />
                                        </form>
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
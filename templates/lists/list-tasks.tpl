<div style="border:solid; border-width:1px; margin:10px; padding:10px;" class="tasks">
    <div class="alert {if $msgRes}alert-success{/if} id="msgRes" {if !$msgRes}style="display:none"{/if}>
        {$msgRes}.
    </div>
    Cliente: <b> {$data.info.customerName}</b> | Razon Social: <b>{$data.info.contractName}</b> | Fecha:
    {$data.info.date} | <a href="{$WEB_ROOT}/download_tasks.php?id={$workFlowId}" style="font-weight:bold">Descargar Archivos</a>
    <div class="clearfix"></div>
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
        {foreach from=$data.tasks item=task}
            <tr id="1">
                <td align="center" class="id">{$task.nombreTask}</td>
                <td align="center" class="id">
                    {$task.diaVencimiento} - Per: {$data.info.periodicidad}
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
                            {if $data.info.status neq "inactiva"}
                                <form method="post" enctype="multipart/form-data" onsubmit="return false;" id="frmFile{$stepId}{$task.taskId}{$workFlowId}">
                                    <input type="hidden" name="type" value="uploadFile" />
                                    <input type="hidden" id="stepId" name="stepId" value="{$stepId}" />
                                    <input type="hidden" id="taskId" name="taskId" value="{$task.taskId}" />
                                    <input type="hidden" id="servicioId" name="servicioId" value="{$data.info.instanciaServicioId}" />
                                    <input type="hidden" id="control" name="control" value="1" />
                                    <input type="hidden" id="uplToken" name="uplToken" value="{$uplToken}" />
                                    <input type="hidden" id="instanciaId" name="instanciaId" value="{$workFlowId}" />
                                    <span class="btn btn-xs btn-success btn-file" id="file{$stepId}{$task.taskId}{$workFlowId}">
                                        <i class="fa fa-arrow-up"></i>
                                        <span>Subir archivo</span>
                                        <input type="file" id="file" name="file" onchange="UploadFile('{$stepId}{$task.taskId}{$workFlowId}')"/>
                                    </span>
                                    <!--<input type="submit" value="Enviar" class="btnEnviar" onclick="UploadFile('{$stepId}{$task.taskId}{$workFlowId}')" />-->
                                    <div style="display:none" id="porcentaje_{$stepId}{$task.taskId}{$workFlowId}" >0%</div>
                                    <progress style="display:none; width:100%" id="progress_{$stepId}{$task.taskId}{$workFlowId}" value="0" min="0" max="100"></progress>
                                    <span align="center" id="load{$stepId}{$task.taskId}{$workFlowId}" style="display:none">
                                        <img src="{$WEB_ROOT}/images/loading.gif" />
                                        <br />
                                        Guardando archivo espere...
                                        <br />&nbsp;
                                    </span>
                                </form>
                            {/if}
                        {else}
                            <img src="{$WEB_ROOT}/images/icons/cancel.png" />
                            {if $data.info.status neq "inactiva"}
                                <form method="post" enctype="multipart/form-data" onsubmit="return false;" id="frmFile{$stepId}{$task.taskId}{$workFlowId}">
                                    <input type="hidden" name="type" value="uploadFile" />
                                    <input type="hidden" id="stepId" name="stepId" value="{$stepId}" />
                                    <input type="hidden" id="taskId" name="taskId" value="{$task.taskId}" />
                                    <input type="hidden" id="servicioId" name="servicioId" value="{$data.info.instanciaServicioId}" />
                                    <input type="hidden" id="control" name="control" value="1" />
                                    <input type="hidden" id="uplToken" name="uplToken" value="{$uplToken}" />
                                    <input type="hidden" id="instanciaId" name="instanciaId" value="{$workFlowId}" />
                                    <span class="btn btn-xs btn-success btn-file" id="file{$stepId}{$task.taskId}{$workFlowId}">
                                        <i class="fa fa-arrow-up"></i>
                                        <span>Subir archivo</span>
                                        <input type="file" id="file" name="file" onchange="UploadFile('{$stepId}{$task.taskId}{$workFlowId}')"/>
                                    </span>

                                    <div style="display:none" id="porcentaje_{$stepId}{$task.taskId}{$workFlowId}" >0%</div>
                                    <progress style="display:none"  id="progress_{$stepId}{$task.taskId}{$workFlowId}" value="0" min="0" max="100"></progress>
                                    <span align="center"  id="load{$stepId}{$task.taskId}{$workFlowId}" style="display:none">
                                        <img src="{$WEB_ROOT}/images/loading.gif" />
                                        <br />
                                          Guardando archivo espere...
                                        <br />&nbsp;
                                    </span>
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
</div>

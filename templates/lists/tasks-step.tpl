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
                {$task.diaVencimiento} - Per: {$data.workflow.periodicidad}
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
                                    <span><a href="javascript:;" data-file="{$file.taskFileId}" data-step="{{$data.stepId}}" class="deleteFileWorkflow">&raquo; Borrar Archivo</a></span>
                                {/if}
                            {/if}
                  <br />
                            {/foreach}
              </span>
                        {if $data.workflow.status neq "inactiva"}
                            {if in_array(103,$permissions)||$User.isRoot}
                                {if $isDep}
                                    <form method="post" enctype="multipart/form-data" class="dropzone" id="frm-workflow" >
                                        <input type="hidden" id="stepId" name="stepId" value="{$data.stepId}" />
                                        <input type="hidden" id="taskId" name="taskId" value="{$task.taskId}" />
                                        <input type="hidden" id="servicioId" name="servicioId" value="{$data.workflow.instanciaServicioId}" />
                                        <input type="hidden" id="control" name="control" value="1" />
                                    </form>
                                {/if}
                            {/if}
                        {/if}
                    {else}
                        <img src="{$WEB_ROOT}/images/icons/cancel.png" />
                        {if $data.workflow.status neq "inactiva"}
                            {if in_array(103,$permissions)||$User.isRoot}
                                {if $isDep}
                                    <form method="post" enctype="multipart/form-data" class="dropzone" id="frm-workflow">
                                        <input type="hidden" id="stepId" name="stepId" value="{$data.stepId}" />
                                        <input type="hidden" id="taskId" name="taskId" value="{$task.taskId}" />
                                        <input type="hidden" id="servicioId" name="servicioId" value="{$data.workflow.instanciaServicioId}" />
                                        <input type="hidden" id="control" name="control" value="1" />
                                        <input type="hidden" id="uplToken" name="uplToken" value="{$uplToken}" />
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
<div class="grid_16" id="content">
    <div class="grid_9">
        <h1 class="catalogos">Workflow </h1>
    </div>
    <div class="clear"></div>

    <div id="portlets">
        {if $myWorkflow}
            <form method="post" name="frmWorkFlow" id="frmWorkFlow" onsubmit="return false">
                <input type="hidden" id="idWorkFlow" name="idWorkFlow" value="{$workFlowId}">
                <input type="hidden" id="type" name="type" value="changeDateWorkFlow">
                Cliente: <b>{$myWorkflow.customerName}</b> Razon Social:<b>{$myWorkflow.contractName}</b> Fecha:
                <input class="form-control btn btn-xs green" type="button" name="date-workflow" id="date-workflow"
                       {if in_array(116,$permissions)||$User.isRoot}onclick="Calendario(this)"{/if}
                       value="{$myWorkflow.date}"/>
                |{if in_array(101,$permissions)||$User.isRoot}<a href="{$WEB_ROOT}/download_tasks.php?id={$workFlowId}"
                                                                 style="font-weight:bold">Descargar Archivos</a>{/if}
                |{if $myWorkflow.customerId eq $CUSTOMER_CAPACITACION}<input type="button"
                                                                             class="form-control btn btn-xs error"
                                                                             data-id="{$workFlowId}"
                                                                             data-customer="{$myWorkflow.customerId}"
                                                                             value="Reiniciar" id="resetWorkflow"
                                                                             title="Reiniciar workflow"/>|{/if}<a
                        href="{$WEB_ROOT}/report-servicio">Regresar</a><br/>
            </form>
            <div class="clearfix"></div>
            <span style="display: none; color:red"
                  id="message_reset"><br>Reseteando workflow, espere un momento..........</span>
            <div class="portlet">
                <div class="portlet-content nopadding borderGray" id="contenido" style="padding:15px">
                    {foreach from=$myWorkflow.steps item=step}
                        <div class="{if in_array(102,$permissions)||$User.isRoot}boxStep{/if} {if $step.stepCompleted}completeStep{else}incompleteStep{/if} "
                             id="step-{$step.stepId}"
                             data-id="{$step.stepId}" {*if in_array(102,$permissions)||$User.isRoot}onclick="ToggleTask({$step.stepId})"{/if*} >
                            Paso No. {$step.step}<br/><b>{$step.nombreStep}</b><br/>
                            {if in_array(102,$permissions)||$User.isRoot}
                                &raquo; Click para Ver Tareas &laquo;
                            {/if}
                            {if $step.stepCompleted}Completado{/if}
                        </div>
                        {if $step.step < $myWorkflow.totalSteps}
                            <div class="arrowStep">
                                <img src="{$WEB_ROOT}/images/arrow.png"/>
                            </div>
                        {/if}
                    {/foreach}
                    <div style="clear:both"></div>
                    <div class="tasks zoneTaskHiden">
                    </div>
                </div>
            </div>
        {else}
            <div class="portlet">
                <div class="portlet-content" style="text-align: center">
                    <div class="boxStep">
                        No se encontro información del worflow
                    </div>
                </div>
            </div>
        {/if}
    </div>
    {if $myWorkflow}
    <div style="clear:both"></div>
    <div class="formLine" style="text-align:center; margin-left:420px">
        {if $myWorkflow.status neq "inactiva"}
            {if in_array(106,$permissions)||$User.isRoot}
                {if $isDep}
                    <a class="button_notok" id="btnAddCancelWorkFlow" onclick="CancelarWorkFlow({$workFlowId})"><span>Desactivar</span></a>
                {/if}
            {/if}
        {else}
            {if in_array(106,$permissions)||$User.isRoot}
                <a class="button_ok" id="btnAddCancelWorkFlow"
                   onclick="ReactivarWorkFlow({$workFlowId})"><span>Activar</span></a>
            {/if}
        {/if}
        <div class="clear"></div>
    </div>
    {/if}

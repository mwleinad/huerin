{foreach from=$myWorkflow.steps item=step name=levelSteps}
    <li class="jstree-node jstree-leaf {if $smarty.foreach.levelSteps.last}jstree-last{/if}" role="treeitem">
        <i class="jstree-icon jstree-ocl" role="presentation"></i>
        <a href="javascript:;"  onclick="ToggleTask('{$step.stepId}{$workFlowId}')">
            <i class="jstree-icon fa fa-file {if $step.stepCompleted} icon-state-success {else}icon-state-default{/if}   jstree-themeicon-custom" role="presentation"></i>
           <!-- <span style=" cursor:pointer;{if $step.stepCompleted}background-color:#006633; color:#FFFFFF{else}background-color:#C00; color:#FFFFFF{/if}">-->Paso No. {$step.step} &nbsp;
            <b>{$step.nombreStep}(<small>{if $step.stepCompleted}Completado{/if}</small>)</b>
               <!-- </span> -->
        </a>
    </li>
{/foreach}
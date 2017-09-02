{foreach from=$myWorkflow.steps item=step}
    <li><a href="javascript:;"  onclick="ToggleTask({$step.stepId})"><span style=" cursor:pointer;{if $step.stepCompleted}background-color:#006633; color:#FFFFFF{else}background-color:#C00; color:#FFFFFF{/if}">Paso No. {$step.step} &nbsp;
        <b>{$step.nombreStep}</b> &nbsp; {if $step.stepCompleted}Completado{/if}
                </span>
        </a>
    </li>
{/foreach}
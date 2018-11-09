<ul>
    {foreach from=$pasos item=item key=key}
            {if $item.isComplete}
                    {assign var=icon value="fa fa-file  icon-state-success icon-lg"}
            {else}
                {if $item.class eq 'Iniciado' || $item.class eq 'PorCompletar'}
                    {assign var=icon value="fa fa-file  icon-state-warning  icon-lg"}
                {else}
                    {assign var=icon value="fa fa-file  icon-state-danger  icon-lg"}
                {/if}
            {/if}
        <li data-jstree='{ "icon":"{$icon}" }' data-datos='{ "idWorkFlow":{$item.instanciaId},"stepId":{$item.stepId},"type":"listTasksStep","drill":true }'>
            <a href="javascript:;">  Paso {$item.step} - {$item.nombreStep}</a>

        </li>
    {/foreach}
</ul>


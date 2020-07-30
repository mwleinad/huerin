{if $steps|count > 0}
    <div class="formLine" style=" width:100%;display: inline-block;">
        <div style="width:100%;float:left"> Seleccione las opciones disponibles</div>
        <div style="width:100%;float:left">
            <ul id="lista-main">
                {foreach from=$steps item=item key=key}
                  <li>
                      <a href="javascript:void(0);" class="{if $item.tasks|@count > 0}deepList {/if}" id="level{$key}{$item.stepId}">[<small>{if $item.tasks|@count > 0}{'+'|lower}{else}{'x'|lower}{/if}</small>]-</a>
                      <input type="checkbox" name="steps[]" value='{$item|@json_encode}' checked />{$item.nombreStep}
                      {if $item.tasks|count > 0}
                          <ul class="noShow" id="level{$key}{$item.stepId}">
                              {foreach from=$item.tasks item=item2 key=key2}
                                  <li>
                                      <a href="javascript:void(0);">[<small>{'x'|lower}</small>]-</a>
                                      <input type="checkbox" name="tasks{$item.stepId}[]" value='{$item2|@json_encode}' checked />{$item2.nombreTask}
                                  </li>
                              {/foreach}
                          </ul>
                      {/if}
                  </li>
                {/foreach}
            </ul>
        </div>
        <hr>
    </div>
{/if}

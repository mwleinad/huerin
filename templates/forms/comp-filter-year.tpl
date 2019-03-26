<select name="year" id="year"  {if $class neq ''}class="{$class}"{else}class="largeInput"{/if} style="width: 80px; min-width: 80px">
        {if $all}
                <option value="" selected="selected">Todos</option>
        {/if}
        {for $init=2012 to $year}
        <option value="{$init}" {if $init == $year} selected="selected" {/if}>{$init}</option>
        {/for}
</select>
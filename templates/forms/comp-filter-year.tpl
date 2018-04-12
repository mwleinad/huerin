<select name="year" id="year"  class="largeInput" style="width: 80px; min-width: 80px">
    {for $init=2012 to $year}
        <option value="{$init}" {if $init == $year} selected="selected" {/if}>{$init}</option>
    {/for}
</select>
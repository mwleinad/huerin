<div class="container16">
    {foreach from=$charts item=item key=key}
        <div class="grid_7">
            <img src="{$WEB_ROOT}/sendFiles/charts/{$item.url}">
        </div>
    {/foreach}
</div>

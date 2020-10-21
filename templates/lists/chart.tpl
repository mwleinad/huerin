<div class="container_12">
    {foreach from=$charts item=item key=key}
        <div class="grid_7">
            <img src="{$WEB_ROOT}/sendFiles/charts/{$item.url}" class="img-responsive">
        </div>
    {/foreach}
</div>

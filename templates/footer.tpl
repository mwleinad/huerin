{if $page != "product-add" && $page != "obs-add" && $page != "costeo-prvw" && $page != "costeo-html"}
    {if $page != "login" && $page != "costeo-add"}
        <div class="container_16" id="footer">
            <a href="http://www.braunhuerin.com.mx" target="_blank">{$smarty.const.FOOTER}</a>
            <br><br>
         <b><a href="{$WEB_ROOT}/changelog.txt">Ver últimos cambios</a></b>
        </div>
    {/if}
{/if}
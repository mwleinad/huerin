<div id="menu">
	<ul class="group" id="menu_group_main">
        {if in_array(2,$permissions)}
		<li class="item first" id="two">
        <li class="item first" id="two">
        	<a href="{$WEB_ROOT}/customer" class="main{if $mainMnu == "contratos"} current{/if}">
            	<span class="outer">
                	<span class="inner users">Clientes</span>
                </span>
            </a>
        </li>
        {/if}
        {if in_array(3,$permissions)}
        <li class="item middle" id="four">
        	<a href="{$WEB_ROOT}/report-servicio" class="main{if $mainMnu == "servicios"} current{/if}">
            	<span class="outer">
                	<span class="inner content">Servicios</span>
                </span>
            </a>
        </li>
        {/if}
        {if in_array(6,$permissions)}
        <li class="item middle" id="three">
        	<a href="{$WEB_ROOT}/archivos/id/1" class="main{if $mainMnu == "archivos"} current{/if}">
            	<span class="outer">
                	<span class="inner media_library png">Departamentos</span>
                </span>
            </a>
        </li>
        {/if}
        {if in_array(7,$permissions)}
        <li class="item last" id="three">
        	<a href="{$WEB_ROOT}/report-documentacion-permanente" class="main{if $mainMnu == "reportes"} current{/if}">
            	<span class="outer">
                	<span class="inner reports png">Reportes</span>
                </span>
            </a>
        </li>
        {/if}
     		  
    </ul>
</div>
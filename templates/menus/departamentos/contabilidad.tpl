<div id="menu">
	<ul class="group" id="menu_group_main">    

		<li class="item first" id="two"> 
        <li class="item first" id="two">
        	<a href="{$WEB_ROOT}/customer" class="main{if $mainMnu == "contratos"} current{/if}">
            	<span class="outer">
                	<span class="inner users">Clientes</span>
                </span>
            </a>
        </li>

        <li class="item middle" id="four">
        	<a href="{$WEB_ROOT}/report-servicio" class="main{if $mainMnu == "servicios"} current{/if}">
            	<span class="outer">
                	<span class="inner content">Servicios</span>
                </span>
            </a>
        </li>
{*}        
        <li class="item middle" id="four">
        	<a href="{$WEB_ROOT}/cxc" class="main{if $mainMnu == "cxc"} current{/if}">
            	<span class="outer">
                	<span class="inner balance">C x C</span>
                </span>
            </a>
        </li>
        <li class="item middle" id="four">
        	<a href="{$WEB_ROOT}/sistema/nueva-factura" class="main {if $mainMnu == "admin-folios"} current{/if}">
            	<span class="outer">
                	<span class="inner invoice">Facturacion</span>
                </span>
            </a>
        </li>
{*}
        <li class="item middle" id="three">
        	<a href="{$WEB_ROOT}/archivos/id/1" class="main{if $mainMnu == "archivos"} current{/if}">
            	<span class="outer">
                	<span class="inner media_library png">Departamentos</span>
                </span>
            </a>
        </li>        

{*
        <li class="item middle" id="three">
        	<a href="{$WEB_ROOT}/cfdi" class="main{if $mainMnu == "cfdi"} current{/if}">
            	<span class="outer">
                	<span class="inner event_manager png">Comprobante<br /> Digital</span>
                </span>
            </a>
        </li>        
*}

        <li class="item last" id="three">
        	<a href="{$WEB_ROOT}/report-documentacion-permanente" class="main{if $mainMnu == "reportes"} current{/if}">
            	<span class="outer">
                	<span class="inner reports png">Reportes</span>
                </span>
            </a>
        </li>
     		  
    </ul>
</div>
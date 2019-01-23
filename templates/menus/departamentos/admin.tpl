<div id="menu">
	<ul class="group" id="menu_group_main">
        {if in_array(1,$permissions) || $User.isRoot}
			<li class="item first" id="one">
                <a href="{$WEB_ROOT}/{$firstPages[1]}" class="main{if $mainMnu == "catalogos"} current{/if}">
                    <span class="outer">
                        <span class="inner catalogos">Cat&aacute;logos</span>
                     </span>
                </a>
            </li>
        {/if}
        {if in_array(2,$permissions)|| $User.isRoot}
        <li class="item middle" id="two">
        	<a href="{$WEB_ROOT}/{$firstPages[2]}" class="main{if $mainMnu == "contratos"} current{/if}">
            	<span class="outer">
                	<span class="inner users">Clientes</span>
                </span>
            </a>
        </li>
        {/if}
        {if in_array(3,$permissions)|| $User.isRoot}
        <li class="item middle" id="four">
        	<a href="{$WEB_ROOT}/{$firstPages[3]}" class="main{if $mainMnu == "servicios"} current{/if}">
            	<span class="outer">
                	<span class="inner content">Servicios</span>
                </span>
            </a>
        </li>
        {/if}
        {if in_array(4,$permissions)|| $User.isRoot}
        <li class="item middle" id="four">
        	<a href="{$WEB_ROOT}/{$firstPages[4]}" class="main{if $mainMnu == "cxc"} current{/if}">
            	<span class="outer">
                	<span class="inner balance">C x C</span>
                </span>
            </a>
        </li>
        {/if}
        {if in_array(5,$permissions)|| $User.isRoot}
        <li class="item middle" id="four">
        	<a href="{$WEB_ROOT}/{$firstPages[5]}" class="main {if $mainMnu == "admin-folios"} current{/if}">
            	<span class="outer">
                	<span class="inner invoice">Facturacion</span>
                </span>
            </a>
        </li>
        {/if}
        {if in_array(6,$permissions)|| $User.isRoot}
        <li class="item middle" id="three">
        	<a href="{$WEB_ROOT}/archivos/id/{$firstDep}" class="main{if $mainMnu == "archivos"} current{/if}">
            	<span class="outer">
                	<span class="inner media_library png">Departamentos</span>
                </span>
            </a>
        </li>        
        {/if}
        {if in_array(7,$permissions)|| $User.isRoot}
        <li {if in_array(213,$permissions) || in_array(217,$permissions) || $User.isRoot} class="item middle"{else} class="item last"{/if} id="three">
        	<a href="{$WEB_ROOT}/{$firstPages[7]}" class="main{if $mainMnu == "reportes"} current{/if}">
            	<span class="outer">
                	<span class="inner reports png">Reportes</span>
                </span>
            </a>
        </li>
        {/if}
        {if in_array(213,$permissions)|| $User.isRoot}
            <li class="item middle" id="three">
                <a href="{$WEB_ROOT}/coffe" class="main{if $mainMnu == "coffe"} current{/if}">
            	<span class="outer">
                	<span class="inner event_manager png">Cafeteria</span>
                </span>
                </a>
            </li>
        {/if}
        {if in_array(218,$permissions)||$User.isRoot}
            <li class="item last" id="three">
                <a href="{$WEB_ROOT}/{$firstPages[217]}" class="main{if $mainMnu == "configuracion"} current{/if}">
            	<span class="outer">
                	<span class="inner settings png">Configuracion</span>
                </span>
                </a>
            </li>
        {/if}
     		  
    </ul>
</div>
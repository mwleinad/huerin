<div id="menu">
	<ul class="group" id="menu_group_main">    

        {if $User.roleId == 1 || $User.roleId == 2}        
        
       {if $infoUser.tipoPersonal == "Recepcion" || $infoUser.tipoPersonal == "Nomina"}
        <li class="item first" id="two">
        	<a href="{$WEB_ROOT}/customer" class="main{if $mainMnu == "contratos"} current{/if}">
            	<span class="outer">
                	<span class="inner users">Clientes</span>
                </span>
            </a>
        </li>
       
       {else}
			{if $infoUser.tipoPersonal != "Recepcion" &&  $infoUser.tipoPersonal != "Nomina"}
				<li class="item first" id="one">        	
        	<a href="{$WEB_ROOT}/personal" class="main{if $mainMnu == "catalogos"} current{/if}">
            	<span class="outer">
                	<span class="inner catalogos">Cat&aacute;logos</span>
                 </span>
            </a>
        </li>
			{/if}
		{if $infoUser.tipoPersonal == "Asistente"}
		<li class="item middle" id="two"> 
		{else}
        <li class="item middle" id="two">
		{/if}
        	<a href="{$WEB_ROOT}/customer" class="main{if $mainMnu == "contratos"} current{/if}">
            	<span class="outer">
                	<span class="inner users">Clientes</span>
                </span>
            </a>
        </li>
       {/if}

        <li class="item middle" id="four">
        	<a href="{$WEB_ROOT}/servicios" class="main{if $mainMnu == "servicios"} current{/if}">
            	<span class="outer">
                	<span class="inner content">Servicios</span>
                </span>
            </a>
        </li>
        
				{if $infoUser.tipoPersonal == "Socio" || $infoUser.tipoPersonal == "Gerente" || $infoUser.tipoPersonal == "Supervisor" || $infoUser.tipoPersonal == "Asistente"}
        <li class="item middle" id="four">
        	<a href="{$WEB_ROOT}/cxc" class="main{if $mainMnu == "cxc"} current{/if}">
            	<span class="outer">
                	<span class="inner balance">C x C</span>
                </span>
            </a>
        </li>
        {/if}
        
				{if $infoUser.tipoPersonal == "Socio" || $infoUser.tipoPersonal == "Gerente" || $infoUser.tipoPersonal == "Supervisor" || $infoUser.tipoPersonal == "Asistente"}
        <li class="item middle" id="four">
        	<a href="{$WEB_ROOT}/sistema/nueva-factura" class="main {if $mainMnu == "admin-folios"} current{/if}">
            	<span class="outer">
                	<span class="inner invoice">Facturacion</span>
                </span>
            </a>
        </li>
        {/if}
        
        {/if}

        {if $User.roleId == 3}
        <li class="item first" id="two">
        	<a href="{$WEB_ROOT}/customer" class="main{if $mainMnu == "contratos"} current{/if}">
            	<span class="outer">
                	<span class="inner users">Clientes</span>
                </span>
            </a>
        </li>
        <li class="item middle" id="four">
        	<a href="{$WEB_ROOT}/servicios" class="main{if $mainMnu == "servicios"} current{/if}">
            	<span class="outer">
                	<span class="inner content">Servicios</span>
                </span>
            </a>
        </li>
        
        {/if}
        
        {if $User.roleId != 4}   
        <li class="item last" id="three">
        	<a href="{$WEB_ROOT}/report-servicio" class="main{if $mainMnu == "reportes"} current{/if}">
            	<span class="outer">
                	<span class="inner reports png">Reportes</span>
                </span>
            </a>
        </li>
        {/if}
        

        {if $User.roleId == 4}
        <li class="item first" id="four">
        	<a href="{$WEB_ROOT}/servicios-cliente" class="main{if $mainMnu == "servicios"} current{/if}">
            	<span class="outer">
                	<span class="inner content">Mis Servicios</span>
                </span>
            </a>
        </li>

        <li class="item last" id="three">
        	<a href="{$WEB_ROOT}/report-cliente" class="main{if $mainMnu == "reportes"} current{/if}">
            	<span class="outer">
                	<span class="inner reports png">Reportes</span>
                </span>
            </a>
        </li>
        
        {/if}
     		  
    </ul>
</div>
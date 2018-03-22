<div id="menu">
  {if $User.tipoPers == "Asistente" || $User.tipoPers == "Socio"}
  	{include file="templates/menus/departamentos/admin.tpl"}
  {elseif $User.roleId == 4}
	<ul class="group" id="menu_group_main">
	  <li class="item first" id="four">
		  <a href="{$WEB_ROOT}/servicios-cliente" class="main{if $mainMnu == "servicios"} current{/if}">
			<span class="outer">
				<span class="inner content">Mis Servicios</span>
			</span>
		  </a>
	  </li>

	  <li class="item middle" id="three">
		  <a href="{$WEB_ROOT}/report-cliente" class="main{if $mainMnu == "reportes"} current{/if}">
			<span class="outer">
				<span class="inner reports png">Reportes</span>
			</span>
		  </a>
	  </li>
  	  <li class="item last" id="two">
		<a href="{$WEB_ROOT}/customer-only" class="main{if $mainMnu == "customer-only"} current{/if}">
			<span class="outer">
				<span class="inner users">Perfil</span>
			</span>
		</a>
	  </li>
	</ul>
  {else}
  	{include file="templates/menus/departamentos/contabilidad.tpl"} 
  {/if}
</div>
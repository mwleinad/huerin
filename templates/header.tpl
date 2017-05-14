    <div id="fview" style="display:none;">	
      <input type="hidden" id="inputs_changed" value="0" />  	
        <div id="fviewload" style="display:block"><img src="{$WEB_ROOT}/images/load.gif" border="0" /></div>
        <div id="fviewcontent" style="display:none"></div>
        <div id="modal">
            <div id="submodal">
               
            </div>
        </div>
    </div>
    <div style="position:relative" id="divStatus"></div>
{if $page != "login"}
    <div class="grid_logo" id="logo" style="height:69px; padding-left:10px; font-size:20px">
    <a href="{$WEB_ROOT}">
        <img src="http://www.braunhuerin.com.mx/imagenes/logo.png" height="60" border="0" />
    </a>
    </div>    
    <div class="grid_wlc">
        <div id="user_tools"><span>Bienvenido <a href="#">{$infoUser.name}</a>  |  <a href="{$WEB_ROOT}/logout">Salir</a></span></div>
    </div>    
      
    <div class="grid_16" id="header">

        <div class="spanner">

    {*$User|print_r*}
        {include file="menus/main.tpl"}
            </div>
    </div>
    
    <div class="grid_16">
        {include file="menus/submenu.tpl"}   
    </div>
    
{/if} 
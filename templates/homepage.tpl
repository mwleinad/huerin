<div class="grid_16" id="content">
    <!--  TITLE START  --> 
    <div class="grid_9">
    <h1 class="dashboard">Inicio</h1>
    </div>
     <div class="grid_6" id="eventbox">
         {if in_array(170,$permissions) || $User.isRoot}
             {if (in_array(173,$permissions)&&in_array(171,$permissions))|| $User.isRoot}
                 <a href="javascript:void(0)" class="inline_add" onclick="AddPendientePopup()">Agregar Pendiente</a>{/if}  {if (in_array(172,$permissions)&&in_array(174,$permissions))|| $User.isRoot}|
                 <a href="javascript:void(0)" class="inline_add" id="addNotice">Agregar Aviso</a>
             {/if}
         {/if}

     </div>
    <div class="clear">
    </div>
    <!--  TITLE END  -->    
    
    <div id="portlets">
        <div class="clear"></div>
        <div class="portlet">
            {if in_array(170,$permissions) || $User.isRoot}
            <div>
               <p align="center">Bienvenido:{$variable} <br />Selecciona alguna opcion de los menus.</p>
                <p>&nbsp;</p>
            </div>
                {if in_array(171,$permissions)}
                    <div style="text-align:center">
                    Pendientes
                    </div>
                    <div class="portlet-content nopadding borderGray" id="contenidoPendiente">
                      {include file="lists/pendientes.tpl"}
                    </div>
                {/if}
                {if in_array(172,$permissions) || $User.isRoot}
                    <div style="text-align:center">
                    Avisos
                    </div>
                    <div class="portlet-content nopadding borderGray" id="contenidoAviso">
                        {include file="lists/avisos.tpl"}
                    </div>
                {/if}
            {/if}
          <div class="portlet-content nopadding borderGray" style="text-align:center;display: flex;flex-direction: column">
              {if empty($permissions)}
                  <div style="background-color: rgba(162,67,48,0.97);">
                      <p style="overflow-wrap:break-word;color: #FFFFFF;font-weight: bold;"> Su cuenta no se encuentra configurada, contactese con el administrador de sistema</p>
                  </div>
              {/if}
              <div class="fb-page" data-href="https://www.facebook.com/braunhuerin" data-width="1000" data-height="600" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false" data-show-posts="true">
                  <div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/braunhuerin"><a href="https://www.facebook.com/braunhuerin">Braun Huerin SC</a></blockquote>
                  </div>
              </div>
          </div>
        </div>
   </div>
    <div class="clear"> </div>
   
  </div>
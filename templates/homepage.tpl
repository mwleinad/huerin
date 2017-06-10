<div class="grid_16" id="content">
    <!--  TITLE START  --> 
    <div class="grid_9">
    <h1 class="dashboard">Inicio</h1>
    </div>
     <div class="grid_6" id="eventbox">
         {if $User.roleId < 4}
             <a href="javascript:void(0)" class="inline_add" onclick="AddPendientePopup()">Agregar Pendiente</a> |
             <a href="javascript:void(0)" class="inline_add" id="addNotice">Agregar Aviso</a>
         {/if}

     </div>
    <div class="clear">
    </div>
    <!--  TITLE END  -->    
    
    <div id="portlets">

        <div class="clear"></div>
        
        <div class="portlet">     
            <div>
               <p align="center">Bienvenido:{$variable} <br />Selecciona alguna opcion de los menus.</p>
                <p>&nbsp;</p>
            </div>
            {if $User.roleId < 4}
       <div style="text-align:center">     
       Pendientes
       </div>
      <div class="portlet-content nopadding borderGray" id="contenido">
          
          {include file="lists/pendientes.tpl"}            

      </div>
            {/if}
       {if $User.roleId < 4}
       <div style="text-align:center">     
       Avisos
       </div>
      <div class="portlet-content nopadding borderGray" id="contenido">
          
          {include file="lists/avisos.tpl"}            

      </div>
          {/if}
      <div class="portlet-content nopadding borderGray" style="text-align:center">
<div class="fb-page" data-href="https://www.facebook.com/braunhuerin" data-width="1000" data-height="600" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false" data-show-posts="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/braunhuerin"><a href="https://www.facebook.com/braunhuerin">Braun Huerin SC</a></blockquote></div></div>

      </div>
      
        </div>

   </div>
    <div class="clear"> </div>
   
  </div>
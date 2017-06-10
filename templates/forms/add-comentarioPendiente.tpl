<div id="divForm">
	<form id="addPendienteForm" name="addPendienteForm" method="post" action="{$WEB_ROOT}/homepage" enctype="multipart/form-data" action="">
  
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left"> * Comentario:</div>
                 <textarea  id="comentario" name="comentario" class="smallInput" rows="8" cols="50"></textarea>
             <hr />
			</div>
            
			<div style="clear:both"></div>
			 * Campo requerido	
      			<div align="center" id="load" style="display:none; padding-bottom:5px">
	    			<img src="{$WEB_ROOT}/images/loading.gif" width="16" height="16" />
         			<br />Cargando...
      			</div>	
     		<div class="formLine" style="text-align:center; margin-left:300px" id="btnSaveNot">            
            <a class="button_grey" onclick="AddComentarioPendiente()"><span>Agregar</span></a>                       
     		</div>
			<div style="clear:both"></div>
        <table width="100%" border="1">
        <tr>
        <td>
        Nombre
        </td>
        <td>
        Fecha
        </td>
        <td>
        Comentario
        </td>
        </tr>
        {foreach from=$comentarios item=item}
        <tr>
        <td>
        {$item.name}
        </td>
        <td>
        {$item.fecha}
        </td>
        <td>
        {$item.comentario}
        </td>
        </tr>
        {/foreach}
        <tr>
        </tr>
        </table>
        
        <input type="hidden" id="type" name="type" value="saveAddComentarioPendiente"/>
        <input type="hidden" id="noticeId" name="noticeId" value="{$notice.pendienteId}"/>
		</fieldset>
	</form>
</div>

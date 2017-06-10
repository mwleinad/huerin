<br />
<br />
{if $cmpMsg}
<div align="center" style="color:#009900">{$cmpMsg}<br /><br /></div>
{/if}
{if $errMsg}
<div align="center" style="color:#FF0000">{$errMsg}<br /><br /></div>
{/if}
<div id="divForm">
	<form id="frmCertificado" name="frmCertificado" method="post" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="guardar_certificado" />
    <fieldset>				
             
      <div class="formLine" style="width:100%; text-align:left">
        <div style="width:150px;float:left">Certificados:</div> 
        <div style="width:350px;float:left">
        <select name="certificado" id="certificado" class="largeInput">
        {if $nom_certificado}
        <option value="{$nom_certificado}">{$nom_certificado}</option>
        {/if}
        </select>
            <div style="position:relative">
               <div style="display:none;position:absolute;top:-20; z-index:100" id="suggestionDiv"></div>
             </div>
        </div>       
       	<div style="clear:both; padding-top:5px"></div>
      </div>
      
      <div class="formLine" style="width:100%; text-align:left">
        <div style="width:150px;float:left">Fecha Certificado:</div> 
        <div style="width:750px;float:left"><input name="fecha" id="fecha" type="text" size="30" readonly="readonly" value="{$fecha_expiracion}" class="largeInput"/>
            <div style="position:relative">
               <div style="display:none;position:absolute;top:-20; z-index:100" id="suggestionDiv"></div>
             </div>
        </div>       
       	<div style="clear:both; padding-top:5px"></div>
      </div>
      
      <div class="formLine" style="width:100%; text-align:left">
        <div style="width:150px;float:left">* Ruta del Certificado:</div> 
        <div style="width:650px;float:left"><input type="file" name="file_certificado" id="file_certificado" class="largeInput" />
            <div style="position:relative">
               <div style="color:#FF0000; display:{if $errCertificado}block{else}none{/if}">{$errCertificado}</div>
             </div>
        </div>       
       	<div style="clear:both; padding-top:5px"></div>
      </div>
      
      <div class="formLine" style="width:100%; text-align:left">
        <div style="width:150px;float:left">* Ruta de la Llave Privada:</div> 
        <div style="width:650px;float:left"><input type="file" name="file_llave" id="file_llave" class="largeInput" />
            <div style="position:relative">
               <div style="color:#FF0000; display:{if $errLlave}block{else}none{/if}">{$errLlave}</div>
             </div>
        </div>       
       	<div style="clear:both; padding-top:5px"></div>
      </div>
      
      <div class="formLine" style="width:100%; text-align:left">
        <div style="width:150px;float:left">* Contrasena Llave Privada:</div> 
         <div style="width:650px;float:left"><input type="password" name="pass_llave" id="pass_llave" size="20" class="largeInput"/>
            <div style="position:relative">
               <div style="color:#FF0000; display:{if $errPass}block{else}none{/if}">{$errPass}</div>
             </div>
        </div>       
       	<div style="clear:both; padding-top:5px"></div>
 		<hr />
      </div>
      <div align="left">* Campos requeridos.</div>
	    <div class="formLine" style="text-align:center">
      	<a class="button" id="agregarCertificado" name="agregarCertificado"><span>Actualizar</span></a>     	</div>
         
  	</fieldset>
    </form>
</div>

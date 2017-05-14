		<div class="popupheader" style="z-index:70">
		<div id="fviewmenu" style="z-index:70">
	    <div id="fviewclose"><span style="color:#CCC" id="closePopUpDiv">
        <a href="javascript:void(0)">Close<img src="{$WEB_ROOT}/images/b_disn.png" border="0" alt="close" /></a></span>
      </div>
      </div>

      <div id="ftitl">
    	<div class="flabel">Agregar Folios</div>
			<div id="vtitl"><span title="Titulo">Agregar Folios</span></div>
    </div>
	<div id="draganddrop" style="position:absolute;top:45px;left:640px">
    		<img src="{$WEB_ROOT}/images/draganddrop.png" border="0" alt="mueve" />
    </div>
		</div>
		
   	 	<div class="wrapper">
            {if $nom_certificado != ''}
								{include file="{$DOC_ROOT}/templates/forms/agregar-folios.tpl"}
            {else}            
    			<div align="center" class="txtRed">Lo sentimos, pero no se encontro un certificado valido. 
                	<br />No podra asignar series y folios hasta subir uno.
                </div>
    		{/if}
            
		</div>
		<div class="popupheader" style="z-index:70">
		<div id="fviewmenu" style="z-index:70">
	    <div id="fviewclose"><span style="color:#CCC" id="closePopUpDiv">
        <a href="javascript:void(0)">Close<img src="{$WEB_ROOT}/images/b_disn.png" border="0" alt="close" /></a></span>
      </div>
      </div>

      <div id="ftitl">
    	<div class="flabel">&nbsp;</div>
			<div id="vtitl">
            	<span title="Titulo">Cuenta por Cobrar
        			<br />
                    <br />RFC: {$rfc}
                    <br />
                    <br />Serie y Folio: {$serie}{$folio}
                </span>
           </div>
    </div>
	<div id="draganddrop" style="position:absolute;top:45px;left:640px">
    		<img src="{$WEB_ROOT}/images/draganddrop.png" border="0" alt="mueve" />
   	</div>
		</div>
		
    <div class="wrapper">
			{include file="{$DOC_ROOT}/templates/forms/edit-cxc.tpl"}
		</div>
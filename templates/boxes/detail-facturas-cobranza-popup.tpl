<div class="popupheader" style="z-index:70">
    <div id="fviewmenu" style="z-index:70">
        <div id="fviewclose"><span style="color:#CCC" id="closePopUpDiv">
        <a href="javascript:void(0)">Close<img src="{$WEB_ROOT}/images/b_disn.png" border="0" alt="close" onclick="close_popup()" /></a></span>
        </div>
    </div>

    <div id="ftitl">
        <div class="flabel">&nbsp;</div>
        <div id="vtitl">
            	<span title="Titulo">
                    Facturas emitidas correspondiente a :
                    <br>Razon social :  {$results.razon}
                    <br />Mes : {$results.mes}
                </span>
        </div>
    </div>
    <div id="draganddrop" style="position:absolute;top:45px;left:640px">
        <img src="{$WEB_ROOT}/images/draganddrop.png" border="0" alt="mueve" />
    </div>
</div>

<div class="wrapper" id="myPaymentsDiv">
    {include file="{$DOC_ROOT}/templates/lists/details-facturas-cobranza.tpl"}
</div>
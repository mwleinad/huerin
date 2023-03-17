<div class="popupheader" style="z-index:70">
    <div id="fviewmenu" style="z-index:70">
        <div id="fviewclose"><span style="color:#CCC" id="closePopUpDiv">
        <a href="javascript:void(0)">Close<img src="{$WEB_ROOT}/images/b_disn.png" border="0" alt="close"/></a></span>
        </div>
    </div>

    <div id="ftitl">
        <div class="flabel">{if $contrato.acuerdo_comercial|strlen >0}Agregar{else}Actualizar{/if} acuerdo comercial</div>
        <div id="vtitl"><span title="Titulo">{if $contrato.acuerdo_comercial|strlen >0}Agregar{else}Actualizar{/if} acuerdo comercial</span></div>
    </div>
    <div id="draganddrop" style="position:absolute;top:45px;left:640px">
        <img src="{$WEB_ROOT}/images/draganddrop.png" border="0" alt="mueve"/>
    </div>
</div>

<div class="wrapper">
        {include file="{$DOC_ROOT}/templates/forms/frm-folios.tpl"}
</div>

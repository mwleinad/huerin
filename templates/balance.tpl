<div class="grid_16" id="content">

  <div class="grid_9">
  <h1 class="catalogos">Estado de cuenta saldos pendientes</h1>
  </div>
  {if in_array(130,$permissions) || $User.isRoot}
  <div class="grid_6" id="eventbox">
		  <a style="cursor:pointer" title="Exportar a Excel" onclick="printExcel('contenido')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
		  <a style="cursor:pointer" title="Exportar a PDF" onclick="printExcel('contenido', 'pdf')"><img src="{$WEB_ROOT}/images/pdf_icon.png" width="16" /></a>
  <div id="loadPrint">
  </div>
  </div>
  {/if}
  <div class="grid_6" id="eventbox" >
  </div>

  <div class="clear">
  </div>

  <div id="portlets">

  <div class="clear"></div>

  <div class="portlet">

<div id="divForm">
<form id="addCustomerFormSearch" name="addCustomerFormSearch" method="post">
			<input type="hidden" id="cliente" name="cliente" value="0"/>
			<input type="hidden" id="cuenta" name="cuenta" value="0"/>

    <table width="800" align="center">
        <tr style="background-color:#CCC">
            <td colspan="6" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
        </tr>
        <tr>
            <td align="center">Cliente:</td>
            <td align="center">A&ntilde;o:</td>
            <td align="center">Mes:</td>
            <td align="center"></td>
        </tr>
        <tr>
        <td>
            <div style="width:100%;float:left">
                <input type="text" size="35" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$customerNameSearch}" />

                <div id="loadingDivDatosFactura"></div>
                <div style="position:relative">
                    <div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
                    </div>
                </div>
            </div>
        </td>
            <td align="center">
              {include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl"}
            </td>
            <td>
                {include file="{$DOC_ROOT}/templates/forms/comp-filter-month.tpl" nameField='month' clase='largeInput' all=true}
            </td>
            <td align="center">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="button_grey" id="btnAddCity" onclick="BuscarServiciosActivos()"><span>Buscar</span></a>
            </td>
        </tr>
    </table>
</form>

<input type="hidden" id="type" name="type" value="{$tipo}" />
      <div class="portlet-content nopadding borderGray" id="contenido">

          {include file="lists/balance.tpl"}

      </div>

    </div>

 </div>
  <div class="clear"> </div>

</div>
<div class="grid_16" id="content">

  <div class="grid_9">
  <h1 class="catalogos">Estado de cuenta saldos pendientes</h1>
  </div>
    <div class="grid_6" id="eventbox">
		  <a style="cursor:pointer" title="Exportar a Excel" onclick="printExcel('contenido')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
		  <a style="cursor:pointer" title="Exportar a PDF" onclick="printExcel('contenido', 'pdf')"><img src="{$WEB_ROOT}/images/pdf_icon.png" width="16" /></a>
  <div id="loadPrint">
  </div>
  </div>
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
                <select name="year" id="year"  class="smallInput"  style="min-width:60px">
                    <option value="" selected="selected">Todos</option>
                    <option value="2012" {if $year == "2012"} selected="selected" {/if}>2012</option>
                    <option value="2013" {if $year == "2013"} selected="selected" {/if}>2013</option>
                    <option value="2014" {if $year == "2014"} selected="selected" {/if}>2014</option>
                    <option value="2015" {if $year == "2015"} selected="selected" {/if}>2015</option>
                    <option value="2016" {if $year == "2016"} selected="selected" {/if}>2016</option>
                    <option value="2017" {if $year == "2017"} selected="selected" {/if}>2017</option>
                    <option value="2018" {if $year == "2018"} selected="selected" {/if}>2018</option>
                    <option value="2019" {if $year == "2019"} selected="selected" {/if}>2010</option>
                    <option value="2020" {if $year == "2020"} selected="selected" {/if}>2020</option>
                </select>

            </td>
            <td>
                <select name="month" id="month"  class="smallInput"  style="min-width:100px">
                    <option value="" selected="selected">Todos</option>
                    <option value="1" {if $month == "1"} selected="selected" {/if}>Enero</option>
                    <option value="2" {if $month == "2"} selected="selected" {/if}>Febrero</option>
                    <option value="3" {if $month == "3"} selected="selected" {/if}>Marzo</option>
                    <option value="4" {if $month == "4"} selected="selected" {/if}>Abril</option>
                    <option value="5" {if $month == "5"} selected="selected" {/if}>Mayo</option>
                    <option value="6" {if $month == "6"} selected="selected" {/if}>Junio</option>
                    <option value="7" {if $month == "7"} selected="selected" {/if}>Julio</option>
                    <option value="8" {if $month == "8"} selected="selected" {/if}>Agosto</option>
                    <option value="9" {if $month == "9"} selected="selected" {/if}>Septiembre</option>
                    <option value="10" {if $month == "10"} selected="selected" {/if}>Octubre</option>
                    <option value="11" {if $month == "11"} selected="selected" {/if}>Noviembre</option>
                    <option value="12" {if $month == "12"} selected="selected" {/if}>Diciembre</option>
                </select>
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
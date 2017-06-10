 <div align="center"  id="divForm">
 
<form name="frmSearch" id="frmSearch" action="" method="post">
<input type="hidden" name="type" id="type" value="search" />
<input type="hidden" name="correo" id="correo" value="" />
<input type="hidden" name="texto" id="texto" value="" />
<input type="hidden" name="cliente" id="cliente" value="{$id}" />
<input type="hidden" name="responsableCuenta" id="responsableCuenta" value="0">
<table width="500" align="center">
<tr style="background-color:#CCC">
    <td colspan="4" bgcolor="#CCCCCC" align="center"><b>Filtro de Busqueda</b></td>
</tr>
<tr>
    <td align="center">Cliente</td>
    <td align="center">Estatus</td>               
    <td align="center">Mes</td>
    <td align="center">A&ntilde;o</td>               
    <td align="center"></td>
</tr>
<tr>	
    <td align="center">
    	<input type="text" size="35" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$search.rfc}" />
          <div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         	</div>
		</td>        
    <td align="center">
    	<select name="status" id="status"  class="smallInput">
      	<option value="0">Todos...</option>
      	<option value="PorIniciar" {if $search.status == "PorIniciar"} selected="selected" {/if} >Por Iniciar</option>
      	<option value="Iniciado" {if $search.status == "Iniciado"} selected="selected" {/if}  >Iniciado</option>
      	<option value="PorCompletar" {if $search.status == "PorCompletar"} selected="selected" {/if}  >Por Completar</option>
      	<option value="Completo" {if $search.status == "Completo"} selected="selected" {/if}  >Completo</option>
      	<option value="CompletoTardio" {if $search.status == "CompletoTardio"} selected="selected" {/if}  >Completo Tardio</option>
      </select>  
		</td>    

    <td align="center">
    	<select name="month" id="month"  class="smallInput">
      	<option value="01" {if $month == "01"} selected="selected" {/if}>Ene</option>
      	<option value="02" {if $month == "02"} selected="selected" {/if}>Feb</option>
      	<option value="03" {if $month == "03"} selected="selected" {/if}>Mar</option>
      	<option value="04" {if $month == "04"} selected="selected" {/if}>Abr</option>
      	<option value="05" {if $month == "05"} selected="selected" {/if}>May</option>
      	<option value="06" {if $month == "06"} selected="selected" {/if}>Jun</option>
      	<option value="07" {if $month == "07"} selected="selected" {/if}>Jul</option>
      	<option value="08" {if $month == "08"} selected="selected" {/if}>Ago</option>
      	<option value="09" {if $month == "09"} selected="selected" {/if}>Sep</option>
      	<option value="10" {if $month == "10"} selected="selected" {/if}>Oct</option>
      	<option value="11" {if $month == "11"} selected="selected" {/if}>Nov</option>
      	<option value="12" {if $month == "12"} selected="selected" {/if}>Dic</option>
      </select>  
		</td>    
    <td align="center">    	
    <select name="year" id="year"  class="smallInput">
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
    </tr>
<tr>
    <td colspan="3" align="center">
        <div style="margin-left:230px">
        <a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
        &nbsp;&nbsp;&nbsp;
        <a class="button_grey" id="btnGraph" onclick="showGraph()"><span>Ver Grafica</span></a>
        </div>
    </td>
</tr>
</table>
</form>
</div>
<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="reportes">Obligaciones Administradas</h1>
  </div>
     
  <div class="clear"></div>
  
  <div align="center">
  	<form name="frmSearch" id="frmSearch">
    <input type="hidden" name="type" value="doSearch" />
  	<table width="500" align="center">
    <tr>
    	<td align="center">
        Proyecto<br />
        <input type="text" name="name" id="name" class="smallInput" />
        </td>
        <td align="center">
        Folio<br />
        <input type="text" name="folio" id="folio" class="smallInput" /></td>
        <td align="center">
        Tipo<br />
        <select class="smallInput" name="contCatId" id="contCatId">
        <option value="">Todos</option>
        {foreach from=$categories item=item}
        <option value="{$item.contCatId}">{$item.name}</option>
        {/foreach}
        </select>
        </td>        
        <td align="center">
        Obligaciones<br />
        <select class="smallInput" name="stOblig" id="stOblig">
        <option value="">Todas</option>
        <option value="1">Cumplidas</option>
        <option value="2">Por Cumplir</option>
        <option value="3">Sin Obligaciones</option>
        </select>
        </td>
        <td align="center">
        Status<br />
        {include file="{$DOC_ROOT}/templates/lists/enumStatusDef2.tpl"}
        </td>
    </tr>
    <tr>
    	<td align="center" colspan="5">
        <input type="button" name="btnBuscar" id="btnBuscar" value="Buscar" onclick="doSearch()" class="btnGral" />
        </td>
    </tr>
    </table>
    </form>
  </div>
      
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
     
      <div class="portlet-content nopadding borderGray" id="contenido">
          
          {include file="lists/report-cobranza.tpl"}            
        
      </div>
      <br />
      <div align="right" id="totalRegs">Total de Registros: <b>{$totalRegs}</b></div>
      
      	<div id="loading" align="center" style="display:none">
            <img src="{$WEB_ROOT}/images/loading.gif" />
            <br />
            Cargando...
  		</div>
      
    </div>

 </div>
  <div class="clear"> </div>

</div>
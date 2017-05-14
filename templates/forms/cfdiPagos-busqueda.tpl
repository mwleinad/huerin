
    <div class="clear"></div>
    <div id="portlets">
    <!--THIS IS A WIDE PORTLET-->
    <div class="portlet">
        <div align="center">Filtros de B&uacute;squeda</div>
		<div class="portlet-content nopadding">
    <div id="divForm">
        <form name="frmBusqueda" id="frmBusqueda" method="post" action="">
     		<input type="hidden" name="type" id="type" value="buscar" />
          <table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" summary="Employee Pay Sheet">
            <thead>
              <tr>
                <th width="50" scope="col">RFC</th>
                <th width="50" scope="col">Razon Social</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td align="center"><input type="text" size="20" name="rfc" id="rfc" class="largeInput" value="" autocomplete="off"/>
                </td>
                <td align="center"><input type="text" size="20" name="razonSocial" id="razonSocial" class="largeInput" value="" autocomplete="off"/>
                </td>
              </tr>
            </tbody>
          </table>
        </form>
        </div>
		</div>
      </div>


	<!-- form-->
     <form name="frmBusqueda" id="frmBusqueda" method="post" action="">
     <input type="hidden" name="type" id="type" value="buscar" />
        <div class="folioRowOff" style="width:910px"></div>
        <div style="margin-left:420px">
        	<a class="button" name="btnBuscar" id="btnBuscar" onclick="Buscar()"><span>Buscar</span></a>
		</div>
        <div style="clear:both"></div>
        <br />
        <div id="loadBusqueda" style="display:none" align="center">
            <img src="http://www.facturase.com/images/loading.gif" width="16" height="16" />
            <br />
            Cargando...
        </div> 
     </form>
	<!-- End Form -->

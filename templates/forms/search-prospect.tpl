<div align="center"  id="divForm">
	<form id="frmCustomerSearch" name="frmCustomerSearch" method="post" action="{$WEB_ROOT}/export/customer.php" onsubmit="return false">
	<input type="hidden" id="type" name="type" value="search" />
	<table width="100%" align="center">
		<tr style="background-color:#CCC;">
			<td colspan="1" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
		</tr>
		<tr>
			<td style="width: 20%; text-align: center">Nombre</td>
		</tr>
		<tr>
			<td align="center">
				<input type="text" size="50" name="name" id="name" class="largeInput" autocomplete="off" value="" />
				<div id="loadingDivDatosFactura"></div>
				<div style="position:relative">
					<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
					</div>
				</div>
			</td>
			<td align="center">
		</tr>
		<tr>
			<td colspan="1" align="center">
				<div style="margin-left:430px">
				<a class="button_grey" id="search"><span>Buscar</span></a>
				</div>
			</td>
		</tr>
	</table>
	</form>
</div>
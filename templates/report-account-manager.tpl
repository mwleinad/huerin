<div class="grid_16" id="content">
	<div class="grid_9">
		<h1 class="reportes">Reporte de cuentas por gerente</h1>
	</div>
	<div class="clear"></div>
	<div id="portlets">
		<div class="clear"></div>
		<div class="portlet">
			<div align="center"  id="divForm">
				<form name="frmSearch" id="frmSearch"  method="post" onsubmit="return false;">
					<input type="hidden" name="type" id="type" value="accountByManager" />
					<table width="100%">
						<tr>
							<td align="center" colspan="5">
								<div style="display:inline-block;text-align: center;">
									<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
									<a class="button_grey" id="btnBuscar" "><span>Buscar</span></a>
								</div>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div class="portlet-content nopadding borderGray" id="contenido">
				<div style="text-align:center"><b>Este reporte puede tardar varios minutos si no eliges un cliente. Por favor sea paciente.</b></div>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>
	<div class="clear"></div>
</div>

<div class="popupheader" style="z-index:70">
	<div id="fviewmenu" style="z-index:70">
		<div id="fviewclose"><span style="color:#CCC" id="closePopUpDiv">Close<img src="{$WEB_ROOT}/images/b_disn.png" border="0" alt="close" /></span>
		</div>
	</div>
	<div id="ftitl">
		<div class="flabel">Historial Servicio</div>
		<div id="vtitl"><span title="Titulo">Historial Servicio</span></div>
	</div>
	<div id="draganddrop" style="position:absolute;top:45px;left:640px">
		<img src="{$WEB_ROOT}/images/draganddrop.png" border="0" alt="mueve" />
	</div>
</div>
<div class="wrapper">
	<pre>
  <table border="1" width="100%">
  <tr>
	<th>Movimiento</th>
	<th>Fecha movimiento</th>
  	<th>Inicio Operaciones</th>
  	<th>Inicio Factura</th>
  	<th>Costo</th>
  	<th>Usuario</th>
  </tr>
  {foreach from=$historial item=item}
  <tr>
	<td>{if $item.status eq 'bajaParcial'}Baja temporal{elseif $item.status eq 'baja'}Baja{elseif $item.status eq 'reactivacion'}Reactivacion{elseif $item.status eq 'activo'}Alta{/if}</td>
	<td>{$item.fecha}</td>
  	<td>{$item.inicioOperaciones}</td>
  	<td>{$item.inicioFactura}</td>
  	<td>{$item.costo}</td>
  	<td>{$item.name}</td>
  </tr>
  {/foreach}
  </table>
  </pre>
</div>

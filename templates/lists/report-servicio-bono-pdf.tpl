<style>
.stCompleto{
	background-color:#009900 !important;
}
.stCompletoTardio{
	background-color:#01FFFF !important;
}
.stPorCompletar{
	background-color:#FC0 !important;
}
.stIniciado{
	background-color:#ffff99 !important;
}
.stPorIniciar{
	background-color:#F00 !important;
}
.txtStCompleto,
.txtStPorIniciar,
.txtStPorCompletar
.txtStCompletoTardio{
	color:#FFFFFF !important;
}
.txtIniciado{
	color:#000000 !important;
}
body{
	font-family:"Courier New", Courier, monospace;
	font-size:8px;
}

/*******************************************************************************
  TABLE DESIGN 
*******************************************************************************/
#box-table-a {
	font-size: 12px;
	margin: 0px;
	text-align: left;
	border-collapse: separate;
	border-bottom:none;
}
#box-table-a th {
	font-size: 13px;
	font-weight: normal;
	padding: 8px;
/*	background: #EFEFEF;
*/	border-top: 1px solid #FFF;
	color: #333;
	text-align: center;
}
#box-table-a td {
	padding: 8px;
	background: none; 
	border-top: 1px solid #CCC;
	color: #666;
	border-bottom: none !important;
}

#box-table-a tr:hover td {
	background: #FBFBFB;
	color: #333;
}
#box-table-a tr.footer { background: none !important; }
#box-table-a tr.footer:hover td { background: none !important;  }

/*******************************************************************************
  TABLE DESIGN 
*******************************************************************************/
.box-table-b {
	font-size: 12px;
	margin: 0px;
	text-align: left;
	border-collapse: separate;
	border-bottom:none;
}
.box-table-b th {
	font-size: 13px;
	font-weight: normal;
	padding: 8px;
	background: #EFEFEF;
	border-top: 1px solid #FFF;
	color: #333;
	text-align: center;
}
.box-table-b td {
	padding: 8px;
	background: none; 
	border-top: 1px solid #CCC;
	color: #666;
	border-bottom: none !important;
}
.box-table-b tr.footer { background: none !important; }
.box-table-b tr.footer:hover td { background: none !important;  }

#box-table-b {
	font-size: 12px;
	margin: 0px;
	text-align: left;
	border-collapse: separate;
	border-bottom:none;
}
#box-table-b th {
	font-size: 13px;
	font-weight: normal;
	padding: 8px;
	background: #EFEFEF;
	border-top: 1px solid #FFF;
	color: #333;
	text-align: center;
}
#box-table-b td {
	padding: 8px;
	background: none; 
	border-top: 1px solid #CCC;
	color: #666;
	border-bottom: none !important;
}

#box-table-b tr.footer { background: none !important; }
#box-table-b tr.footer:hover td { background: none !important;  }

/*******************************************************************************
  TABLE DESIGN 
*******************************************************************************/
.box-table-a {
	font-size: 12px;
	margin: 0px;
	text-align: left;
	border-collapse: separate;
	border-bottom:none;
}
.box-table-a th {
	font-size: 13px;
	font-weight: normal;
	padding: 8px;
	background: #EFEFEF;
	border-top: 1px solid #FFF;
	color: #333;
	text-align: center;
}
.box-table-a td {
	padding: 8px;
	background: none; 
	border-top: 1px solid #CCC;
	color: #666;
	border-bottom: none !important;
}
.box-table-a tr:hover td {
	background: #FBFBFB;
	color: #333;
}
.box-table-a tr.footer { background: none !important; }
.box-table-a tr.footer:hover td { background: none !important;  }

@page { margin: 5px; }
body { margin: 5px; }
html { margin: 5px; }
</style>
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
<thead>
	<tr>
		<th align="center" width="80">Cliente</th>
		<th align="center" width="80">Razon Social</th>
		<th align="center" width="50">Ene</th>
		<th align="center" width="50">Feb</th>
		<th align="center" width="50">Mar</th>
		<th align="center" width="50">Abr</th>
		<th align="center" width="50">May</th>
		<th align="center" width="50">Jun</th>
		<th align="center" width="50">Jul</th>
		<th align="center" width="50">Ago</th>
		<th align="center" width="50">Sep</th>
		<th align="center" width="50">Oct</th>
		<th align="center" width="50">Nov</th>
		<th align="center" width="50">Dic</th>
	</tr>
</thead>
<tbody>

{if $clientes}
{foreach from=$clientes item=cliente key=key}
	{foreach from=$cliente.contracts item=contract key=keyContract}
		{foreach from=$contract.instanciasServicio item=servicio key=keyServicio}
  
<tr >
    <td align="center" class="" title="{$contract.responsable.name}">{$cliente.nameContact|wordwrap:15:"<br />"}</td>
    <td align="center" class="" title="{$contract.responsable.name}">{$contract.name|wordwrap:15:"<br />"}</td>
    {foreach from=$servicio.instancias item=instanciaServicio}
    <td align="center" class="st{$instanciaServicio.class} txtSt{$instanciaServicio.class} " title="{$instanciaServicio.class}">
	
<div style="cursor:pointer" onclick="GoToWorkflow('report-servicios', '{$instanciaServicio.instanciaServicioId}')">{$servicio.nombreServicio|wordwrap:10:"<br />":true}</div>
    </td>
    {/foreach}
</tr>
		{/foreach}
  {/foreach}
{/foreach}
{/if}

</tbody>
</table>
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
<thead>
	<tr>
		<th align="center" width="150">Cliente</th>
		<th align="center" width="150">Razones Sociales</th>
		<th align="center" width="150">Servicios</th>
		<th align="center" width="150">Completados</th>
		<th align="center" width="150">Pendientes</th>
		<th align="center" width="150">% Completado</th>
		<th align="center" width="150">Detalle</th>
	</tr>
</thead>
<tbody>

{if $clientes}
{foreach from=$clientes item=cliente key=key}
<tr class="st{$cliente.class}">
    <td align="center" class="txtSt{$cliente.class}">{$cliente.nameContact}</td>
    <td align="center" class="txtSt{$cliente.class}">{$cliente.totalContracts}</td>
    <td align="center" class="txtSt{$cliente.class}">{$cliente.totalServicios}</td>
    <td align="center" class="txtSt{$cliente.class}">{$cliente.totalServiciosCompletados}</td>
    <td align="center" class="txtSt{$cliente.class}">{$cliente.totalServiciosPendientes}</td>
    <td align="center" class="txtSt{$cliente.class}">{$cliente.totalServiciosPorcentaje}%</td>
    <td align="center" class="txtSt{$cliente.class}"><span style="color:#aaa; font-weight:bold; cursor:pointer" id="showCliente-{$cliente.customerId}" onclick="ShowClienteTable({$cliente.customerId})">[+]</span></td>
</tr>
<tr style="display:none" id="cliente-{$cliente.customerId}">
    <td align="center" class="" colspan="7">
    
    <table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
    <thead>
      <tr>
        <th align="center" width="20" style="background-color:#FFFFFF">&nbsp;</th>
        <th align="center" width="150">Razon Social</th>
        <th align="center" width="150">Responsable</th>
        <th align="center" width="50">Totales</th>
        <th align="center" width="50">Completados</th>
        <th align="center" width="50">Pendientes</th>
        <th align="center" width="70">% Completado</th>
        <th align="center" width="50">Detalle</th>
      </tr>
    </thead>
    <tbody>
    {foreach from=$cliente.contracts item=contract key=keyContract}
    <tr class="st{$contract.class}">
        <td align="center" class="" style="background-color:#FFFFFF">&nbsp;</td>
        <td align="center" class="txtSt{$contract.class}">{$contract.name}</td>
        <td align="center" class="txtSt{$contract.class}">{$contract.responsable.name}</td>
        <td align="center" class="txtSt{$contract.class}">{$contract.totalInstancias}</td>
        <td align="center" class="txtSt{$contract.class}">{$contract.totalInstanciasCompletadas}</td>
        <td align="center" class="txtSt{$contract.class}">{$contract.totalInstanciasPendientes}</td>
        <td align="center" class="txtSt{$contract.class}">{$contract.totalInstanciasPorcentaje}%</td>
        <td align="center" class="txtSt{$contract.class}"><span style="color:#aaa; font-weight:bold; cursor:pointer" id="showContract-{$contract.contractId}" onclick="ShowContractTable({$contract.contractId})">[+]</span></td>
    </tr>
    <tr id="contract-{$contract.contractId}" style="display:none">
    	<td colspan="8">
        <table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
        <thead>
          <tr>
            <th align="center" width="40" style="background-color:#FFFFFF">&nbsp;</th>
            <th align="center" width="100">Servicio</th>
            <th align="center" width="50">Pasos</th>
            <th align="center" width="50">Completados</th>
            <th align="center" width="50">Pendientes</th>
            <th align="center" width="70">% Completado</th>
            <th align="center" width="70">Workflow</th>
          </tr>
        </thead>
        <tbody>
        {foreach from=$contract.instanciaServicio item=servicio key=keyServicio}
        <tr class="st{$servicio.class}">
            <td align="center" class="" style="background-color:#FFFFFF">&nbsp;</td>
            <td align="center" class="txtSt{$servicio.class}">{$servicio.nombreServicio}</td>
            <td align="center" class="txtSt{$servicio.class}">{$servicio.totalSteps}</td>
            <td align="center" class="txtSt{$servicio.class}">{$servicio.completedSteps}</td>
            <td align="center" class="txtSt{$servicio.class}">{$servicio.totalSteps - $servicio.completedSteps}</td>
            <td align="center" class="txtSt{$servicio.class}">{$servicio.porcentajeSteps}%</td>
            <td align="center" class="txtSt{$servicio.class}">
            {if $User.roleId != 4}
	            <a onclick="GoToWorkflow('workflow', '{$servicio.instanciaServicioId}')" style="color:#FFFFFF">&raquo; Ver Workflow</a>
            {else}
	            <a onclick="GoToWorkflow('workflow-cliente', '{$servicio.instanciaServicioId}')" style="color:#FFFFFF">&raquo; Ver Workflow</a>
            {/if}
            </td>
        </tr>
        {/foreach}
    
        </tbody>
        </table>    
      
      </td>
    </tr>
    {/foreach}

    </tbody>
    </table>    
    </td>
</tr>
{/foreach}
{/if}

</tbody>
</table>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title>LOGS</title>
    <style>
        *{
            font-size: 8px;
            text-transform: uppercase;
        }
        .tableFullWidth{
            width: 100%;
            border: 1px solid #3b4857;
            padding-top: 2px;
            border-collapse: collapse;
        }
        table tr{
            border-bottom: 1px solid #3b4857;
            border-top: 1px solid #3b4857;
        }
        table thead th{
            background: transparent;
            font-weight: bold;
            color: #111111;
        }
        tr td, tr th{
            border-bottom: .3px solid #3b4857;
        }
        table tbody td{
            font-weight: normal;
        }
        .titleCenter{
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            padding-top: 2px;
            padding-bottom: 2px;
        }
        .textCenter{
            text-align: center;
            padding-left: 2px;
        }
    </style>
</head>
<body>
<h2>Bitacora de cambios realizados en servicios.</h2>
{foreach from=$data key=key item=data_item}
    <table  class="tableFullWidth">
        <thead>
        <tr>
            <th colspan="3" class="titleCenter">{$data_item.contract_name}</th>
        </tr>
        <tr>
            <th colspan="3">Servicios</th>
        </tr>
        <tr>
            <th class="textCenter">Nombre</th>
            <th class="textCenter">Antes</th>
            <th class="textCenter">Despues</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$data_item.servicios key=key2 item=item2}
            <tr>
                <td>{$item2.service_name}</td>
                <td>
                    {if $item2.antes.inicioOperaciones neq $item2.despues.inicioOperaciones}
                        <p><b>Fecha inicio operaciones</b> : {$item2.antes.inicioOperaciones}</p>
                    {/if}
                    {if $item2.antes.inicioFactura neq $item2.despues.inicioFactura}
                        <p><b>Fecha inicio factura</b> : {$item2.antes.inicioFactura}</p>
                    {/if}
                    {if $item2.antes.costo neq $item2.despues.costo}
                        <p><b>Costo</b> : {$item2.antes.costo}</p>
                    {/if}
                    {if $item2.antes.lastDateWorkflow neq $item2.despues.lastDateWorkflow}
                        <p><b>Fecha ultimo workflow</b> : {$item2.antes.lastDateWorkflow}</p>
                    {/if}
                </td>
                <td>
                    {if $item2.antes.inicioOperaciones neq $item2.despues.inicioOperaciones}
                        <p><b>Fecha inicio operaciones</b> : {$item2.despues.inicioOperaciones}</p>
                    {/if}
                    {if $item2.antes.inicioFactura neq $item2.despues.inicioFactura}
                        <p><b>Fecha inicio factura</b> : {$item2.despues.inicioFactura}</p>
                    {/if}
                    {if $item2.antes.costo neq $item2.despues.costo}
                        <p><b>Costo</b> : {$item2.despues.costo}</p>
                    {/if}
                    {if $item2.antes.lastDateWorkflow neq $item2.despues.lastDateWorkflow}
                        <p><b>Fecha ultimo workflow</b> : {$item2.despues.lastDateWorkflow}</p>
                    {/if}
                </td>
            </tr>
        {foreachelse}
            <tr><td colspan="3" class="textCenter">No se realizo ninguna modificacion</td></tr>
        {/foreach}
        </tbody>
    </table>
{foreachelse}
    <p>Ningun cambio realizado</p>
{/foreach}
</body>
</html>
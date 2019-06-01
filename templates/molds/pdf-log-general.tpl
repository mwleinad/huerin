<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title>Log</title>
    <style>
        *{
            font-size: 10px;
            text-transform: uppercase;
        }
        .tableFullWidth{
            width: 100%;
            border-collapse: collapse;
        }
        table tr{
            border-bottom: 1px solid #0e76a8;
            border-top: 1px solid #0e76a8;
        }
        table thead th{
            background: #0e76a8;
            font-size: 10px;
            font-weight: bold;
            color: #ffffff;
            padding-left: 2px;
        }
        table tbody td{
            font-size: 10px;
            font-weight: normal;
            padding-left: 2px;
        }
        table tbody tr.history>td{
            border-bottom: 1px solid #0e76a8;
        }
        .fieldBold{
            font-size: 10px;
            font-weight: bold;
            padding-left: 2px;
        }
        .fieldBoldBackground{
            font-size: 10px;
            font-weight: bold;
            background: #0e76a8;
            color: #ffffff;
            padding-left: 2px;
        }
        .alignJustify{
            text-align: justify;
            padding: 0;
            padding-left: 2px;
        }
        .cell20{
            width: 20%;
        }
        .cell10{
            width: 10%;
        }
        .titleCenter{
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .titleJustify{
            font-size: 11px;
            font-weight: bold;
            text-align: justify;
            padding-top: 2px;
            padding-bottom: 2px;
            padding-left: 2px;
        }
        .textCenter{
            text-align: center;
            padding-left: 2px;
        }
        .textJustify{
            text-align: justify;
            padding-left: 2px;
        }
        .titleCenterHistory{
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            padding-top: 1px;
            padding-bottom: 1px;
        }
        .tableHistory{
            width: 100%;
            border-collapse: collapse;
        }
        .tableHistory thead th{
            background: #0e76a8;
            font-size: 8px;
            font-weight: bold;
            color: #ffffff;
            padding-left: 2px;
        }
        .tableHistory tbody td{
            font-size: 7px;
            padding-left: 2px;
            font-weight: normal;
        }
    </style>
</head>
<body>
<div class="fieldBold">
    <p>{$body}</p>
</div>
<table cellpadding="0" cellspacing="0" class="tableFullWidth">
    <thead>
    <tr>
        <th colspan="2" class="titleCenter">Informacion detallada</th>
    </tr>
    <tr>
        <th class="fieldBold">Campo</th>
        <th class="fieldBold">Valor</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$changes key=key item=item}
        <tr>
            <td class="cell20">{$changes[$key]['campo']}</td>
            <td class="cell20">{$changes[$key]['valor']}</td>
        </tr>
        {foreachelse}
        <tr><td colspan="2" class="textCenter">Informacion no encontrada</td></tr>
    {/foreach}
    </tbody>
</table>
<hr />
{if $serviciosAfectados|count>0}
 <table cellpadding="0" cellspacing="0" class="tableFullWidth">
        <thead>
        <tr>
            <th colspan="6" class="titleCenter">Servicios puestos en status de baja</th>
        </tr>
        <tr>
            <th>Servicio</th>
            <th>Inicio operacion</th>
            <th>Inicio facturacion</th>
            <th>Costo</th>
            <th>ultimo workflow</th>
            <th>Status actual</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$serviciosAfectados key=key item=item}
            <tr>
                <td>{$item.nombreServicio}</td>
                <td>{$item.inicioOperaciones}</td>
                <td>{$item.inicioFactura}</td>
                <td>{$item.costo|number_format:2:'.':','}</td>
                <td>{if $item.status eq "bajaParcial"}{$item.lastDateWorkflow}{else}N/A{/if}</td>
                <td>{if $item.status eq "readonly"}Activo/Solo lectura{elseif $item.status eq "bajaParcial"}Baja temporal{else}{$item.status}{/if}</td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="6" class="textCenter">Ningun servicio afectado</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/if}
</body>
</html>
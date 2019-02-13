<html>
<head>
    <title>LOGS</title>
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
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
</head>
<body>

<table cellpadding="0" cellspacing="0" class="tableFullWidth">
    <thead>
    <tr>
        <th colspan="6" class="titleCenter">Servicios modificados</th>
    </tr>
    <tr>
        <th>Razon social</th>
        <th>Servicio</th>
        <th>Inicio operacion</th>
        <th>Inicio facturacion</th>
        <th>Costo</th
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$servicios key=key item=item}
        <tr>
            <td class="textJustify">{$item.name}</td>
            <td>{$item.nombreServicio}</td>
            <td>{$item.inicioOperaciones}</td>
            <td>{$item.inicioFactura}</td>
            <td>{$item.costo|number_format:2:',':'.'}</td>
            <td>{$item.status}</td>
        </tr>
        <tr class="history">
            <td class="cell20"></td>
             <td colspan="5">
                 <table class="tableHistory titleCenterHistory">
                     <thead>
                         <tr>
                             <th colspan="6" class="titleCenter titleHistory">Datos anteriores</th>
                         </tr>
                         <tr>
                             <th>Modificado por</th>
                             <th>Inicio operaciones</th>
                             <th>Inicio factura</th>
                             <th>Costo</th>
                             <th>Fecha modificacion</th>
                             <th>Operacion</th>
                         </tr>
                     </thead>
                  <tbody>
                     {foreach from=$item.history  key=key2 item=hist}
                         <tr>
                             <td>{$hist.namePerson}</td>
                             <td>{$hist.inicioOperaciones}</td>
                             <td>{$hist.inicioFactura}</td>
                             <td>{$hist.costo|number_format:2:',':'.'}</td>
                             <td>{$hist.fecha}</td>
                             <td>{$hist.status}</td>
                         </tr>
                     {foreachelse}
                         <tr><td colspan="6">Datos anteriores no encontrados</td></tr>
                     {/foreach}
                  </tbody>
                 </table>
             </td>
        </tr>
        {foreachelse}
        <tr><td colspan="6" class="textCenter">No se realizo ninguna modificacion</td></tr>
    {/foreach}
    </tbody>
</table>
</body>
</html>
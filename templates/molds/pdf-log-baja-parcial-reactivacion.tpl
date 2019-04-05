<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <title>Invoice</title>
        <style>
            *{
                font-size: 10px;
                text-transform: uppercase;
            }
            .tableFullWidth{
                width: 100%;
                border-collapse: collapse;
                border:1px solid #b9b9b9;
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
                border-top: 1px solid #b9b9b9;
                border-bottom: 1px solid #b9b9b9;
            }
            table tbody td{
                font-size: 10px;
                font-weight: normal;
                padding-left: 2px;
                border-top: 1px solid #b9b9b9;
                border-bottom: 1px solid #b9b9b9;
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
<div style="width: 650px">
    {include file="{$DOC_ROOT}/templates/molds/body-email-baja-parcial-reactivacion.tpl"}
    <table cellpadding="0" cellspacing="0" class="tableFullWidth">
        <thead>
        <tr>
            <th colspan="2" class="titleJustify">Encargados de area</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$encargados key=key item=item}
            <tr>
                <td class="fieldBold cell20">{$item.departamento}</td>
                <td>{$item.name}</td>
            </tr>
            {foreachelse}
            <tr><td colspan="2" class="textCenter">Sin encargados</td></tr>
        {/foreach}
        </tbody>
    </table>
    <hr />
    <table cellpadding="0" cellspacing="0" class="tableFullWidth">
        <thead>
            <tr>
                <th colspan="{if $endState=='bajaParcial'}5{else}4{/if}" class="titleCenter">Servicios modificados</th>
            </tr
            <tr>
                <th>Nombre servicio</th>
                {if $endState=='bajaParcial'}
                    <th>Fecha de ultimo workflow</th>
                {/if}
                <th>Inicio de operaciones</th>
                <th>Inicio de facturacion</th>
                <th>Costo</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$serviciosAfectados item=item key=key}
            <tr>
                <td>{$item.nombreServicio}</td>
                {if $endState=='bajaParcial'}
                    <td>{$item.ultimoWorkflow}</td>
                {/if}
                <td>{$item.inicioOperaciones}</td>
                <td>{$item.inicioFactura}</td>
                <td>{$item.costo}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {if $endState=='bajaParcial'}
        <p style="line-height: 1.5;">La columna "fecha de ultimo workflow", es la fecha del ultimo workflow que sera creado en plataforma, de esa fecha en adelante se suspende la creacion de workflows para el servicio.</p>
    {/if}
</div>
</body>
</html>
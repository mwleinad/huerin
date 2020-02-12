<html>
<head>
    <title>RECURSOS</title>
    <style>
        @page {
            margin: 80px 25px;
        }
        *{
            font-size: 12px;

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
            font-size: 12px;
            font-weight: normal;
            padding-left: 2px;
        }
        table tbody tr.history>td{
            border-bottom: 1px solid #0e76a8;
        }
        .fieldBoldBackground{
            font-size: 12px;
            font-weight: bold;
            background: #0e76a8;
            color: #ffffff;
            padding-left: 2px;
            text-transform: uppercase;
        }
        .alignJustify{
            text-align: justify;
            padding: 0;
            padding-left: 2px;
        }
        .titleCenter{
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            padding-top: 2px;
            padding-bottom: 2px;
            text-transform: uppercase;
        }

        .titleCenterHistory{
            font-size: 14px;
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
            text-align: left;
            background: #0e76a8;
            font-size: 12px;
            font-weight: bold;
            color: #ffffff;
            padding-left: 2px;
            text-transform: uppercase;
        }
        .tableHistory tbody td{
            font-size: 12px;
            padding-left: 2px;
            font-weight: normal;
            text-align: left;
        }
        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: auto;

            /** Extra personal styles **/
            background-color: #0e76a8;
            text-align: center;
        }
        header h3 {
            color: #FFFFFF;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;

        }
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
</head>
<body>
    <header>
        <h3>Reporte de inventario</h3>
    </header>
    <main>
        <table cellpadding="0" cellspacing="0" class="tableFullWidth" style="margin-top:40px;">
            <tr>
                <td class="fieldBoldBackground alignJustify">Tipo de recurso</td>
                <td class="fieldBoldBackground alignJustify">Nombre</td>
                <td class="fieldBoldBackground alignJustify">Fecha de compra</td>
                <td class="fieldBoldBackground alignJustify">Fecha de alta</td>
            </tr>
            <tr>
                <td class="alignJustify">{$info.tipo_recurso}</td>
                <td class="alignJustify">{$info.nombre}</td>
                <td class="alignJustify">{$info.fecha_compra}</td>
                <td class="alignJustify">{$info.fecha_alta}</td>
            </tr>
            <tr>
                <td class="fieldBoldBackground alignJustify">No. serie</td>
                <td class="fieldBoldBackground alignJustify">Status</td>
                <td class="fieldBoldBackground alignJustify">No. licencia</td>
                <td class="fieldBoldBackground alignJustify">Codigo activación</td>
            </tr>
            <tr>
                <td class="alignJustify">{$info.no_serie}</td>
                <td class="alignJustify">{$info.status}</td>
                <td class="alignJustify">{$info.no_licencia}</td>
                <td class="alignJustify">{$info.codigo_activacion}</td>
            </tr>
            <tr>
                <td class="fieldBoldBackground alignJustify">Fecha y hora de ultima modificación</td>
                <td class="fieldBoldBackground alignJustify"></td>
                <td class="fieldBoldBackground alignJustify"></td>
                <td class="fieldBoldBackground alignJustify"></td>
            </tr>
            <tr>
                <td class="alignJustify">{$info.fecha_ultima_modificacion}</td>
                <td class="alignJustify"></td>
                <td class="alignJustify"></td>
                <td class="alignJustify"></td>
            </tr>
            <tr>
                <td colspan="4" class="fieldBoldBackground">Descripcion</td>
            </tr>
            <tr>
                <td colspan="4" class="alignJustify">{$info.descripcion}</td>
            </tr>
        </table>
        <br><br>
        <table class="tableHistory titleCenterHistory">
            <thead>
            <tr>
                <th colspan="5" class="titleCenter titleHistory">Historico de responsables</th>
            </tr>
            <tr>
                <th>Nombre</th>
                <th>Fecha entrega</th>
                <th>Status</th>
                <th>Fecha baja</th>
                <th>Alta realizada por</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$info.responsables  key=key2 item=res}
                <tr>
                    <td>{$res.nombre}</td>
                    <td>{$res.fecha_entrega_responsable}</td>
                    <td>{$res.status}</td>
                    <td>{if $res.status eq "Baja"}{$res.fecha_liberacion_responsable}{/if}</td>
                    <td>{$res.usuario_creador}</td>
                </tr>
                {foreachelse}
                <tr><td colspan="5">Sin registros</td></tr>
            {/foreach}
            </tbody>
        </table>
        <br><br>
        <table class="tableHistory titleCenterHistory">
            <thead>
            <tr>
                <th colspan="3" class="titleCenter titleHistory">Mantenimientos realizados</th>
            </tr>
            <tr>
                <th>Realizado por</th>
                <th>Fecha de mantenimiento</th>
                <th>Descripcion</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$upkeeps  key=kup item=upkeep}
                <tr>
                    <td>{$upkeep.upkeep_responsable}</td>
                    <td>{$upkeep.upkeep_date}</td>
                    <td>{$upkeep.upkeep_description}</td>
                </tr>
                {foreachelse}
                <tr><td colspan="3">Sin registros</td></tr>
            {/foreach}
            </tbody>
        </table>
    </main>
</body>
</html>
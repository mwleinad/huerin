<html>
<head>
    <title>Propuesta</title>
    <style type="text/css">
        body {
            font-family: helvetica, Sans-Serif;
            font-size: 11px;
            line-height: 1;
        }
        #page-wrap {
            width: 700px;
            margin: 0 auto;
            page-break-inside: avoid;
        }
        table {
            font-size: 11px;
            line-height: 20px;
        }
        table.outline-table {
            border: 2px solid #ccc;
            border-spacing: 0;
        }
        tr.border-bottom td, td.border-bottom {
            border-bottom: 1px solid #ccc;
        }
        tr.border-top td, td.border-top {
            border-top: 1px solid #ccc;
        }
        tr.border-right td, td.border-right {
            border-right: 1px solid #ccc;
        }
        tr.border-left td, td.border-left {
            border-left: 1px solid #ccc;
        }
        tr.border-right td:last-child {
            border-right: 0px;
        }
        tr.center td, td.center {
            text-align: center;
            vertical-align: text-top;
        }
        td.pad-left {
            padding-left: 5px;
        }
        tr.right-center td, td.right-center {
            text-align: right;
            padding-right: 50px;
        }
        tr.right td, td.right {
            text-align: right;
        }
        .font-smallest{
            font-size: 8px;
            line-height: 10px;
            font-weight: bold;;
        }
        .font-smaller{
            font-size: 9px;
            line-height: 1.5;
            font-weight: bold;;
        }
        .bold {
            font-weight: bold;
        }
        .no-bold{
            font-weight: normal !important;
        }
        .no-border-left {
            border-left:none !important;
        }
        .no-border-right {
            border-right:none !important;
        }
        .no-border {
            border: none !important;
        }
        .small-height{
            max-height: 10px;
            height: 10px;
            min-height: 10px;
            margin: 0px;
        }
        .word-break{
            word-break: break-all;
        }
        .text-center {
            text-align: center;
        }
        .padding-vertical {
            padding: 10px 0px
        }
        .pre {
            font-family:"Courier New";
        }
        pre {
            line-height: 10px;
            white-space: pre-wrap;
        }
        .no-margin{
            margin: 0;
        }
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
</head>
<body>
<div id="page-wrap">
    <table width="100%">
        <tbody>
        <tr>
            <td width="100%" valign="top">
                <strong>Propuesta de costo de servicios</strong>
            </td>
        </tr>
        <tr>
            <td width="100%" valign="top">
                <strong>Estimado: </strong> {$prospect.name}<br>
            </td>
        </tr>
        </tbody>
    </table>
    <p class="bold no-margin">Costos de servicios a ofertar</p>
    <table width="100%" class="outline-table">
        <thead>
            <tr class="border-bottom border-right center font-smallest">
                <td class="border-top"><strong>Concepto</strong></td>
                <td class="border-top"><strong>Costo</strong></td>
            </tr>
        </thead>
        <tbody>
            {foreach from=$offer item=item key=key}
                <tr class="border-right border-bottom">
                    <td class="left">Servicio por {$item.concept}</td>
                    <td class="right">{$item.price|number}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>
</body>
</html>
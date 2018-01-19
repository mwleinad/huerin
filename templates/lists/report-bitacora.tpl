{assign var=meses value=['1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre']}
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:12px">
    <thead>
    <tr>
        <th colspan="10"></th>
    </tr>
    <tr>
        <th colspan="10" style="font-size:16px">BITACORA DE MOVIMIENTOS CORRESPONDIENTE AL MES {$meses[$mes]|UPPER}  </th>
    </tr>
    <tr>
        <th align="center" width="60">FECHA Y HORA</th>
        <th align="center" width="60">TIPO DE MOVIMIENTO</th>
        <th align="center" width="60">DESCRIPCION DEL MOVIMIENTO</th>
        <th align="center" width="60">USUARIO QUE MODIFICA</th>
        <th align="center" width="60">CLIENTE</th>
        <th align="center" width="60">RAZON SOCIAL</th>
        <th align="center" width="60">SERVICIO</th>
        <th align="center" width="60">RESPONSABLE CONTABILIDAD</th>
        <th align="center" width="60">INFORMACION ANTERIOR</th>
        <th align="center" width="60">INFORMACION ACTUALIZADA</th>

    </tr>
    </thead>
    <tbody>
    {foreach from=$registros item=item key=key}
        <tr>
            <td align="center">{$item.fecha}</td>
            <td align="center">{$item.tipo}</td>
            <td align="center">{$item.descripcion}</td>
            <td align="center">{$item.usuario}</td>
            <td align="center">{$item.nameContact}</td>
            <td align="center">{$item.name}</td>
            <td align="center">{$item.servicio}</td>
            <td align="center">{$item.respContabilidad}</td>
            <td align="center">
                {foreach from=$item.oldValue  item=itemo key=ko}
                    {$ko} => {$itemo} <br>
                {/foreach}
            </td>
            <td align="center">
                {foreach from=$item.newValue  item=itemn key=kn}
                    {$kn} => {$itemn} <br>
                {/foreach}
            </td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="10" align="center">Ning&uacute;n registro encontrado.</td>
        </tr>
    {/foreach}
    </tbody>
</table>
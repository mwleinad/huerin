<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
<thead>
	<tr>
		<th align="center" width="60">Gerente</th>
		<th align="center" width="60">Supervisor</th>
		<th align="center" width="60">Razon social</th>
        <th align="center" width="60">Sector</th>
        <th align="center" width="60">Subsector</th>
        <th align="center" width="60">Actividad</th>
	</tr>
</thead>
<tbody>
{foreach from=$registros item=item key=key}
    {foreach from=$item.companies item=company}
        <tr>
            <td align="center">{$item.name}</td>
            <td align="center">{$company.supervisor}</td>
            <td align="center">{$company.name}</td>
            <td align="center">{$company.actividad.sector}</td>
            <td align="center">{$company.actividad.subsector}</td>
            <td align="center">{$company.actividad.actividad}</td>
        </tr>
    {foreachelse}
    <tr>
        <td colspan="6" align="center">Ning&uacute;n registro encontrado</td>
    </tr>
    {/foreach}
{foreachelse}
    <tr>
        <td colspan="6" align="center">Ning&uacute;n registro encontrado</td>
    </tr>
{/foreach}
</tbody>
</table>
<br>
<br>
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
    <thead>
        <tr>
            <th></th>
            <th></th>
            <th colspan="{($sectores|count)*2}" style="text-align: center">
                SECTOR
            </th>
        </tr>
        <tr>
            <th>Gerente</th>
            <th>Supervisor</th>
            {foreach from=$sectores item=tot key=keyTot}
                <th>{$tot.sector_name}</th>
                <th>%</th>
            {/foreach}
        </tr>
    </thead>
    {foreach from=$totales item=item2 key=key}
        {foreach from=$item2.supervisores item=supervisor}
            <tr>
                <td align="center">{$item2.name}</td>
                <td align="center">{$supervisor.name}</td>
                {foreach from=$sectores item=totalSector key=keyTotal}
                    <td style="text-align: center">{$supervisor.sectores[$keyTotal].total}</td>
                    <td style="text-align: center">{$supervisor.sectores[$keyTotal].total / $item2.totalRow[$keyTotal].total}</td>
                {/foreach}
            </tr>
            {foreachelse}
            <tr>
                <td colspan="2" align="center">Ning&uacute;n registro encontrado</td>
            </tr>
        {/foreach}
        {foreachelse}
        <tr>
            <td colspan="2" align="center">Ning&uacute;n registro encontrado</td>
        </tr>
        {foreach from=$item2.totalRow}

        {/foreach}
    {/foreach}
</table>

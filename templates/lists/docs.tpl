<table width="40%" align="center" cellspacing="0" cellpadding="0" border="1">
<tr>
    <td align="center" width="30%"><b>FECHA</b></td>
    <td align="center"><b>{$titInfo|upper}</b></td>
    <td width="20%"></td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
{foreach from=$resDocs item=item key=key}
{if $key != 0}
<tr>
    <td width="70%" height="20">{$item.fecha}</td>
    <td width="70%" height="20">{$item.desc}</td>
    <td>
        <a href="javascript:void(0)" onclick="DeleteDocs({$key},{$docBasicId})">
            <img src="{$WEB_ROOT}/images/icons/delete.png" border="0" />
        </a>
    </td>
</tr>
{/if}
{foreachelse}
<tr>
	<td colspan="3" align="center">Ningun documento encontrado</td>
</tr>
{/foreach}
</table> 
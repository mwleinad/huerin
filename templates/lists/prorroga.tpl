<table width="30%" align="center" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td colspan="2" align="center"><b>HISTORIAL DE PRORROGA</b></td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
{foreach from=$resProrroga item=item key=key}
<tr>
    <td width="70%" height="20">{$item}</td>
    <td>
        <a href="javascript:void(0)" onclick="DeleteProrroga({$key},{$docGralId})">
            <img src="{$WEB_ROOT}/images/icons/delete.png" border="0" />
        </a>
    </td>
</tr>
{foreachelse}
<tr>
	<td colspan="2" align="center">Ninguna fecha encontrada</td>
</tr>
{/foreach}
</table> 
<table width="90%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td align="center" width="50">APLICA</td>
    <td align="center">NOMBRE</td>
    <td align="center" width="150">FECHA RECIBIDO <br />ROQUE&Ntilde;I STRAFFON</td>
    <td align="center" width="10"></td>
    <td align="center" width="30">DESC.</td>
    <td align="center" width="10">DOCS.</td>
    <td align="center" width="10">ARCHIVOS</td>
</tr>
{foreach from=$docsBasic item=item key=key}
<tr>
    <td align="center">
    <input type="checkbox" name="apDB[]" id="apDB_{$item.docBasicId}" value="{$item.docBasicId}" {if $item.aplica}checked{/if} onclick="toggleDocBasic({$item.docBasicId})" />
    </td>
    <td align="center" valign="top">{$item.name}</td>
    <td align="center"><br />
    <div style="float:left; padding-left:25px; padding-right:5px">
    <input type="text" name="fechaRecDB_{$item.docBasicId}" id="fechaRecDB_{$item.docBasicId}" size="10" value="{$item.fechaRec}" {if !$item.aplica}disabled="disabled"{/if} />
    </div>
    <div id="trigRDB_{$item.docBasicId}" style="float:left;{if !$item.aplica}display:none;{/if}">
    	<img src="{$WEB_ROOT}/images/icons/calendar.png" width="16" height="16" id="calendar-triggerRDB{$item.docBasicId}"  />
        <script type="text/javascript">
		Calendar.setup({
			inputField : "fechaRecDB_{$item.docBasicId}",
			trigger    : "calendar-triggerRDB{$item.docBasicId}",
			dateFormat : "%d-%m-%Y",
			min: {$cal.min},
    		max: {$cal.max},
			onSelect   : function() { this.hide() }
		});
		</script>
        </div>
    <div style="clear:both"></div>
    <div id="lstF_{$item.docBasicId}">
    	{foreach from=$item.docs item=itm key=ky}
        {if $ky != 0}
            {$itm.fecha}<br />
        {/if}
        {/foreach}
    </div>    
    </td>
    <td align="left"></td>
    <td align="center">
    {if $item.info}{$item.info}{else}<br />{/if}
    <input type="text" name="descDB_{$item.docBasicId}" id="descDB_{$item.docBasicId}" size="15" value="{$item.descripcion}" {if !$item.aplica}disabled="disabled"{/if} />
    <div id="lstD_{$item.docBasicId}">
    	{foreach from=$item.docs item=itm key=ky}
        {if $ky != 0}
            {$itm.description}<br />
        {/if}
        {/foreach}
    </div>  
    </td>
    <td align="center">
    	<div style="height:25px">
        <a href="javascript:void(0)" onclick="addDocsDiv({$item.docBasicId})">
            <img src="{$WEB_ROOT}/images/icons/add.png" border="0" />
        </a>
        </div>
        <div id="lstA_{$item.docBasicId}">
        {foreach from=$item.docs item=itm key=ky}
        {if $itm.archivo}
            <a href="{$WEB_ROOT}/archivos/{$itm.archivo}" target="_blank">
			<img src="{$WEB_ROOT}/images/icons/file.png" border="0" /></a>         
        {/if}
        <br />
        {/foreach}
        </div>
    </td>
    <td align="center">
        <a href="{$WEB_ROOT}/docs-files/docBasicId/{$item.docBasicId}" rel="clearbox[width=500,,height=300]">
            <img src="{$WEB_ROOT}/images/icons/add.png" border="0" />
        </a>
    </td>
</tr>
{foreachelse}
<tr><td align="center" colspan="8">Ningun documento encontrado.</td></tr>
{/foreach}
</table>
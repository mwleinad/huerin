<table width="90%" cellpadding="0" cellspacing="0" border="0" style="border:1">
<tr>
	<td align="center" width="50">APLICA</td>
	<td align="center">NOMBRE</td>
    <td align="center" width="120">FECHA DE VENCIMIENTO</td>
    
    <td align="center" width="120">FECHA DE CUMPLIMIENTO</td>
    
    <td align="center" width="50">PRORROGA</td>
</tr>
{foreach from=$docsGral item=item key=key}
<tr>
	<td align="center" valign="top">
    <input type="checkbox" name="apDG[]" id="apDG[]" value="{$item.docGralId}" {if $item.aplica}checked{/if} onclick="checkObligacion(this,{$item.docGralId})" />
    </td>
    <td align="center" valign="top">{$item.name}</td>
    <td align="center">
    <div style="float:left">
    <input type="text" name="fechaDG_{$item.docGralId}" id="fechaDG_{$item.docGralId}" size="10" readonly="readonly" value="{$item.fecha}" />
    <div id="list_{$item.docGralId}" align="left">
    {if $item.prorrogas}        
        <b>Prorrogas:</b>
        {foreach from=$item.prorrogas item=itm key=ky}
            <br />{$itm.fecha}            
        {/foreach}
    {/if}
    </div>
    </div>
    <div style="float:left">
   
    	<img src="{$WEB_ROOT}/images/icons/calendar.png" width="16" height="16" id="calDG{$item.docGralId}" align="right" />
        <script type="text/javascript">
		Calendar.setup({
			inputField : "fechaDG_{$item.docGralId}",
			trigger    : "calDG{$item.docGralId}",
			dateFormat : "%d-%m-%Y",						
			onSelect   : function() { this.hide() }
		});
		</script>
     </div>
    </td>
    <td align="center" valign="top">
    <div style="float:left">
    <input type="text" name="fechaRecDG_{$item.docGralId}" id="fechaRecDG_{$item.docGralId}" size="10" readonly="readonly" value="{$item.fechaRec}" />
    </div>
    
    <div style="float:left">
   
    	<img src="{$WEB_ROOT}/images/icons/calendar.png" width="16" height="16" id="calRDG{$item.docGralId}"  />
        <script type="text/javascript">
		Calendar.setup({
			inputField : "fechaRecDG_{$item.docGralId}",
			trigger    : "calRDG{$item.docGralId}",
			dateFormat : "%d-%m-%Y",
			min: {$cal.min},
    		max: {$cal.max},
			onSelect   : function() { this.hide() }
		});
		</script>
    </div>
    </td>
    <td align="center">
        <a href="javascript:void(0)" onclick="addProrrogaDiv({$item.docGralId})">
            <img src="{$WEB_ROOT}/images/icons/add.png" border="0" />
        </a>
    </td>
</tr>
{foreachelse}
<tr><td align="center" colspan="5">Ningun documento encontrado.</td></tr>
{/foreach}
</table>
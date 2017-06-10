<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:0">
<tr>
	<td align="center">NOMBRE</td>
    <td align="center">ENVIADO</td>
    <td align="center" width="80">FECHA DE RECIBIDO DE ROQUE&Ntilde;I</td>
    <td align="center" width="10"></td>
    <td align="center" width="80">FECHA DE ENVIO A WALMART</td>
    <td align="center" width="10"></td>
    <td align="center" width="50">ARCHIVOS</td>
</tr>
{foreach from=$docsSellado item=item key=key}
<tr>
    <td width="60%" align="left">{$item.name}</td>
    <td align="center">
    <input type="checkbox" name="recDS[]" id="recDS[]" value="{$item.docSelladoId}" {if $item.enviado}checked{/if} />
    </td>
    <td align="center">
    <input type="text" name="fechaRecDS_{$item.docSelladoId}" id="fechaRecDS_{$item.docSelladoId}" size="10" readonly="readonly" value="{$item.fechaRec}" />
    </td>
    <td align="left">
    	<img src="{$WEB_ROOT}/images/icons/calendar.png" width="16" height="16" id="calendar-triggerRDS{$item.docSelladoId}"  />
        <script type="text/javascript">
		Calendar.setup({
			inputField : "fechaRecDS_{$item.docSelladoId}",
			trigger    : "calendar-triggerRDS{$item.docSelladoId}",
			dateFormat : "%d-%m-%Y",
			min: {$cal.min},
			max: {$cal.max},			
			onSelect   : function() { this.hide() }
		});
		</script>
    </td>
    <td align="center">
    <input type="text" name="fechaDS_{$item.docSelladoId}" id="fechaDS_{$item.docSelladoId}" size="10" readonly="readonly" value="{$item.fecha}" />
    </td>
    <td align="left">
    	<img src="{$WEB_ROOT}/images/icons/calendar.png" width="16" height="16" id="calendar-triggerDS{$item.docSelladoId}"  />
        <script type="text/javascript">
		Calendar.setup({
			inputField : "fechaDS_{$item.docSelladoId}",
			trigger    : "calendar-triggerDS{$item.docSelladoId}",
			dateFormat : "%d-%m-%Y",
			min: {$cal.min},
    		max: {$cal.max},
			onSelect   : function() { this.hide() }
		});
		</script>
    </td>
    <td align="center" align="right">    
    <div style="float:right">
    <a href="{$WEB_ROOT}/sellado-files/docSelladoId/{$item.docSelladoId}" rel="clearbox[width=500,,height=300]">
    	<img src="{$WEB_ROOT}/images/icons/add.png" border="0" />
    </a>
    </div>
    <div id="selFile_{$item.docSelladoId}" style="float:right; margin-right:10px">
    {if $item.archivo}
    <a href="{$WEB_ROOT}/archivos/{$item.archivo}" target="_blank">
    <img src="{$WEB_ROOT}/images/icons/file.png" border="0" />
    </a>
    {/if}
    </div>
    </td>
</tr>
{foreachelse}
<tr><td align="center" colspan="5">Ningun documento encontrado.</td></tr>
{/foreach}
</table>
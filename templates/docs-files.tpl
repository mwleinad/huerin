<div align="center"><b>ARCHIVOS PARA {$infD.name}</b></div>
<br />

{if !$showForm}
<div align="center">
<table width="400" cellpadding="0" cellspacing="0" border="1" align="center">
<tr>
	<td align="center" bgcolor="#CCCCCC" height="30" width="70"><b>FECHA</b></td>
    <td align="center"><b>{$infD.info}</b></td>
    <td align="center" width="100"><b>ARCHIVO</b></td>
    <td width="60"></td>
</tr>

<tr>
    <td align="center" height="20"><div id="divFecha">---</div></td>
    <td align="center"><div id="divDesc">---</div></td>
    <td align="center">
    	{if $resDocs.0.archivo}
    		<a href="{$WEB_ROOT}/{if $resDocs.0.edit}archivos{else}temp{/if}/{$resDocs.0.archivo}" target="_blank">{$resDocs.0.archivo}</a>
        {else}
        	<a href="{$WEB_ROOT}/docs-files/idKey/{$docBasicId}-0">Subir</a>
        {/if}
    </td>
    <td align="center">
    	{if $resDocs.0.archivo}
    		<a href="{$WEB_ROOT}/docs-files/delKey/{$docBasicId}-0" style="color:#F00">Eliminar</a>        
        {/if}
    </td>
</tr>

{foreach from=$resDocs item=item key=key}
{if $key != 0}
<tr>
    <td align="center" height="20">{$item.fecha}</td>
    <td align="center">{$item.desc}</td>
    <td align="center">
    	{if $item.archivo}
    		<a href="{$WEB_ROOT}/{if $item.edit == 1}archivos{else}temp{/if}/{$item.archivo}" target="_blank">{$item.archivo}</a>
        {else}
        	<a href="{$WEB_ROOT}/docs-files/idKey/{$docBasicId}-{$key}">Subir</a>
        {/if}
    </td>
    <td align="center">
    	{if $item.archivo}
    		<a href="{$WEB_ROOT}/docs-files/delKey/{$docBasicId}-{$key}" style="color:#F00">Eliminar</a>        
        {/if}
    </td>
</tr>
{/if}
{/foreach}
</table>
</div>
{if $cmpMsg}
<br />
<div align="center" style="color:#090"><b>{$cmpMsg}</b></div>
{/if}

{/if}

{if $showForm}

<div align="center">
<form  method="post" name="frmPdf" id="frmPdf" action="{$WEB_ROOT}/docs-files/idKey/{$idKey}" enctype="multipart/form-data">
<input type="hidden" name="action" id="action" value="save">
<b>Archivo (*.pdf o *.jpg):</b>
<br/>
<input name="archivo" id="archivo" type="file" class="input-text" size="30" value="" />
<input type='hidden' name="docBasicId" value="{$docBasicId}" />
<input type="hidden" name="k" value="{$k}" />

<br/>

    <button type="submit" class="positive" >
        Guardar
    </button>

</form>
</div>

{if $msgError}
<br />
<div align="center" style="color:#FF0000"><b>{$msgError}</b></div>
{/if}

{/if}


{if !$showForm}
<script type="text/javascript">

var fecha = parent.document.getElementById("fechaRecDB_{$docBasicId}").value;
var desc = parent.document.getElementById("descDB_{$docBasicId}").value;

if(fecha != ''){
	var f = fecha.split("-");
	var date = f[2] + "-" + f[1] + "-" + f[0];
	document.getElementById("divFecha").innerHTML = date;
}

if(desc != '')
	document.getElementById("divDesc").innerHTML = desc;

</script>
{/if}
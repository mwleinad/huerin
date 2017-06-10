<div align="center"><b>ARCHIVO PARA {$infD.name}</b></div>
<br />
<br />

{if $cmpMsg}
<div align="center" style="color:#090"><b>{$cmpMsg}</b></div>
<br />
{/if}

<div align="center">
{if $inFile.archivo == ""}
<form  method="post" name="frmPdf" id="frmPdf" action="{$WEB_ROOT}/sellado-files/docSelladoId/{$docSelladoId}" enctype="multipart/form-data">
<input type="hidden" name="action" id="action" value="save">
<b>Archivo (*.pdf o *.jpg):</b>
<br/>
<input name="archivo" id="archivo" type="file" class="input-text" size="30" value="" />
<input type='hidden' name="docSelladoId" value="{$docSelladoId}" />

<br/>

    <button type="submit" class="positive" >
        Guardar
    </button>

</form>
	<script type="text/javascript">
		var contCatId = parent.document.getElementById("contCatId").value;
		
		if(contCatId == 1)
			parent.document.getElementById("selFile_{$docSelladoId}").innerHTML = "";
		else
			parent.document.getElementById("selFileC_{$docSelladoId}").innerHTML = "";
	</script>
{else}
	<a href="{$WEB_ROOT}/{if $inFile.edit == 1}archivos{else}temp{/if}/{$inFile.archivo}" target="_blank">
    <img src="{$WEB_ROOT}/images/icons/file.png" border="0" />
    <br />
	{$inFile.archivo}
    </a>
    
    <br />
  	<a href="{$WEB_ROOT}/sellado-files/delKey/{$docSelladoId}" style="color:#F00">Eliminar</a>        
    
    <script type="text/javascript">
	    var contCatId = parent.document.getElementById("contCatId").value;
		var folder = "{if $inFile.edit == 1}archivos{else}temp{/if}";
		var file = '<a href="{$WEB_ROOT}/'+folder+'/{$inFile.archivo}" target="_blank"><img src="{$WEB_ROOT}/images/icons/file.png" border="0" /></a>';
						
		if(contCatId == 1)
			parent.document.getElementById("selFile_{$docSelladoId}").innerHTML = file;
		else
			parent.document.getElementById("selFileC_{$docSelladoId}").innerHTML = file;
	</script>
       
{/if}
</div>

{if $msgError}
<div align="center" style="color:#FF0000"><b>{$msgError}</b></div>
{/if}
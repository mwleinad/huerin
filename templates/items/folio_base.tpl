    
              <tr>
                <td>{$folio.serie}</td>
                <td>{$folio.folioInicial}</td>
                <td>{$folio.consecutivo}</td>
                <td>{$folio.folioFinal}</td>
                <td>{if $folio.logo}<img src="{$folio.logo}" width="50" height="50" />{/if}</td>
                
                {if $info.version == "auto"}
                <td>{if $folio.qr}<img src="{$folio.qr}" width="50" height="50" />{/if}</td>
                {/if}
                <td>
                {if $info.version == "auto"}    
                <form method="post" action="" enctype="multipart/form-data" name="qr-{$folio.serieId}">
                <input type="file" name="qr" id="qr" /><input type="hidden" name="serieId" id="serieId" value="{$folio.serieId}"/><input type="submit" value="Cambiar QR" />  
                </form>
            		{/if}
                <br />
    <form method="post" action="" enctype="multipart/form-data" name="cedula-{$folio.serieId}">
    <input type="file" name="cedula" id="cedula" />
   	<input type="hidden" name="serieId" id="serieId" value="{$folio.serieId}"/>
    <input type="submit" value="Cambiar Logo" />
    </form>
    
    </td>
                <td> 
                   {*}   {if $infoUser.tipoPersonal != "Recepcion"}
		<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$folio.serieId}"/>
    <img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$folio.serieId}"/>
    									{/if}{*}</td>
                
              </tr>

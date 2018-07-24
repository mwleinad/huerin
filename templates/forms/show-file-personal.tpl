<div id="divForm">
        <div class="formLine" style="width:100%; text-align:center" id="file-up">
		<fieldset>
             <table border="1" width="100%">
                 <thead>
                    <tr>
                        <th>#</th>
                        <th><b>Nombre expediente</b></th>
                        <th><b>Fecha carga</b></th>
                        <th><b>Acciones</b></th>
                    </tr>
                 </thead>
                 <tbody>
                  {foreach from=$expedientes item=item key=key}
                      <tr>
                          <td>{$key+1}</td>
                          <td>{$item.name}</td>
                          <td>{$item.fecha}</td>
                          <td>

                              {if $item.findFile}
                                  {assign var=titleBtn  value='Actualizar'}
                              {else}
                                  {assign var=titleBtn  value='Subir'}
                              {/if}
                              <input type="hidden" id="exist_file{$item.personalId}{$item.expedienteId}" value="{$item.findFile}">
                              <form  class="dropzone"  id="ln_{$item.personalId}_{$item.expedienteId}">
                                  <input name="file_name" type="hidden" value="file_{$item.personalId}_{$item.expedienteId}">
                              </form>
                              {*}<a href="javascript:void(0);" title="{$titleBtn}" id="subir_{$item.personalId}_{$item.expedienteId}" style="cursor:pointer">
                                  <img src="{$WEB_ROOT}/images/upCloud24.png">
                              </a>{*}
                              {if $item.findFile}
                                  <a href="{$WEB_ROOT}/expedientes/{$item.personalId}/{$item.path}" title="Descargar" style="cursor:pointer" target="_blank">
                                      <img src="{$WEB_ROOT}/images/downCloud24.png">
                                  </a>
                                  <a href="javascript:void(0);" onclick="deleteExpediente({$item.expedienteId},{$item.personalId})" title="Eliminar archivo" style="cursor:pointer">
                                      <img src="{$WEB_ROOT}/images/deleteCloud24.png">
                                  </a>
                              {/if}
                              {*include file="{$DOC_ROOT}/templates/forms/custom-dropzone.tpl" id="{$item.personalId}{$item.expedienteId}"*}

                          </td>
                      </tr>
                  {/foreach}
                 </tbody>
             </table>
		</fieldset>
        </div>
</div>

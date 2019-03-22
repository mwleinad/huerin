<div class="grid_16" id="content">
  <div class="grid_9">
  <h1 class="catalogos">Bases de datos y respaldos</h1>
  </div>
  <div class="grid_6" id="eventbox">
  <div id="loadPrint">
  </div>
  </div>
  <div class="clear">
  </div>
  <div id="portlets">
  <div class="clear"></div>
  <div class="portlet">
      <div class="portlet-content nopadding borderGray" id="contenido">
          <table  width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
              <thead>
               <tr>
                   <th style="width:70%">Nombre de base de datos</th>
                   <th  style="width:30%">Acciones</th>
               </tr>
              </thead>
              {foreach from=$listDatabases item=item key=key}
                  <tr class="{if $key%2 == 0}Off{else}On{/if}">
                      <td>{$item}</td>
                      <td>
                        <a href="javascript:;" title="Realizar respaldo" class="spanAll spanBackup" data-name="{$item}">
                          <img src="{$WEB_ROOT}/images/icons/backup_16x16.png">
                        </a>
                        <span id="span_{$item|trim}" style="color:#006400;font-weight: bold;"></span>
                      </td>
                  </tr>
              {foreachelse}
                  <tr>
                      <td colspan="2">
                      </td>
                  </tr>
              {/foreach}
          </table>
      </div>
    </div>
 </div>
  <div class="clear"> </div>
</div>
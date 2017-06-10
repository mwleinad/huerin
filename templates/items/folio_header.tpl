   <div class="clear"></div>
    <!--THIS IS A WIDE PORTLET-->
    <div class="portlet">
        <div class="portlet-header fixed">
        {if $info.limite > 0}
					Se han utilizado {$info.expedidos} Folios de {$info.limite} disponibles.
        {else} 
        	Tu paquete contiene folios ilimitados.
        {/if}</div>
		<div class="portlet-content nopadding">
          <table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" summary="Employee Pay Sheet">
            <thead>
              <tr>
                <th width="60" scope="col">Serie</th>
                <th width="60" scope="col">Ini</th>
                <th width="60" scope="col">Con</th>
                <th width="60" scope="col">Final</th>
                <th width="60" scope="col">Logo</th>
                                {if $info.version == "auto"}
                <th width="60" scope="col">QR</th>
                {/if}
                <th width="600" scope="col"></th>
                <th width="100" scope="col">Accion</th>
              </tr>
            </thead>
            <tbody>

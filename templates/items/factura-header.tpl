    <div class="clear"></div>
    <!--THIS IS A WIDE PORTLET-->
    <div class="portlet">
        <div class="portlet-header fixed"><img src="{$WEB_ROOT}/images/icons/user.gif" width="16" height="16" alt="Latest Registered Users" /> Resumen de Comprobantes Emitidos</div>
		<div class="portlet-content nopadding">
        <form action="" method="post">
          <table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" summary="Employee Pay Sheet">
            <thead>
              <tr>
                <th width="70" scope="col">RFC</th>
                <th width="200" scope="col">Nombre</th>
                <th width="109" scope="col">Fecha</th>
                <th width="89" scope="col">Monto</th>
                <th width="81" scope="col">Folio</th>
                {if $info.version == "construc" || $info.version == "v3"}

                <th width="173" scope="col">UUID</th>
{/if}
                <th width="34" scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody>
	
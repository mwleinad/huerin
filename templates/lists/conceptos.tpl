{if count($conceptos)}

   <div class="clear"></div>
    <!--THIS IS A WIDE PORTLET-->
    <div class="portlet">
        <div class="portlet-header fixed">Datos Generales</div>
		<div class="portlet-content nopadding">
        <form action="" method="post">
          <table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" summary="Employee Pay Sheet">
            <thead>
              <tr>
                <th width="50" scope="col">&nbsp;</th>
                <th width="50" scope="col">Cant.</th>
                <th width="100" scope="col">Unid.</th>
                <th width="50" scope="col">Id.</th>
                <th width="100" scope="col">Categoria</th>
                <th width="500" scope="col">Descripcion</th>
                <th width="100" scope="col">V. Unitario</th>
                <th width="100" scope="col">Importe</th>
                <th width="50" scope="col">E. Iva</th>
                <th width="100" scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody>
<div id="">

  <div style="clear:both"></div>
</div>
	{foreach from=$conceptos item=concepto key=key}
		{include file="{$DOC_ROOT}/templates/items/concepto_base.tpl"}
	{/foreach}
{else}
No se encontraron conceptos  
{/if}
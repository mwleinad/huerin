<div class="grid_16" id="content">

    <div class="grid_9">
        <h1 class="catalogos">Importar desde archivo</h1>
    </div>
    <div class="grid_6"  id="eventbox">

            <label>Descarga layout</label>
            <select  class="smallInput " name="formato" id="formato">
                <option value="">Seleccionar</option>
                <option value="add_contract">Importar nuevas razones sociales/ contratos </option>
                <option value="add_customer">Importar nuevos clientes principales</option>
                <option value="update_contract">Actualizar razones sociales existentes</option>
                <option value="update_customer">Actualizar clientes principales existentes</option>
            </select>
    </div>
    <div class="clear">
    </div>

    <div id="portlets">
        <div class="clear"></div>
        {include file="forms/form-exp-imp-data.tpl"}
        <div class="portlet">
            <div class="portlet-content nopadding borderGray" id="contenido">

            </div>

        </div>

    </div>
    <div class="clear"> </div>

</div>
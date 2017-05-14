<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="content_edit">Detalles de la Razon Social para {$infoRazonSocial.name}</h1>
  </div>
  
  <div class="grid_6 backbutton" id="eventbox">
		<a href="{$WEB_ROOT}/contract/id/{$infoRazonSocial.customerId}" >&raquo; Regresar</a>
  </div>
  
  <div class="clear">
  </div>
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
			{if $canEdit && $User["userId"] == 13}
      <div class="portlet-content nopadding borderGray" style="text-align:center">
      	<form action="http://comprobantedigital.mx/sistema/fromBraun.php" method="POST" target="_blank">
         	<input type="hidden" id="fromBraun" name="fromBraun" value="Si" />
         	<input type="hidden" id="productId" name="productId" value="v3" />
         	<input type="hidden" id="emailPersonal" name="emailPersonal" value="{$infoRazonSocial.email}" />
         	<input type="hidden" id="email" name="email" value="{$infoRazonSocial.email}" />
         	<input type="hidden" id="password" name="password" value="cfdi" />
         	<input type="hidden" id="socioId" name="socioId" value="0" />
         	<input type="hidden" id="nombre" name="nombre" value="{$infoRazonSocial.nombreComercial}" />
         	<input type="hidden" id="telPersonal" name="telPersonal" value="{$infoRazonSocial.phone}" />
         	<input type="hidden" id="celular" name="celular" value="{$infoRazonSocial.telefonoContactoAdministrativo}" />
         	<input type="hidden" id="razonSocial" name="razonSocial" value="{$infoRazonSocial.name}" />
         	<input type="hidden" id="calle" name="calle" value="{$infoRazonSocial.address}" />
         	<input type="hidden" id="noInt" name="noInt" value="{$infoRazonSocial.noIntAddress}" />
         	<input type="hidden" id="noExt" name="noExt" value="{$infoRazonSocial.noExtAddress}" />
         	<input type="hidden" id="colonia" name="colonia" value="{$infoRazonSocial.coloniaAddress}" />
         	<input type="hidden" id="localidad" name="localidad" value="{$infoRazonSocial.municipioAddress}" />
         	<input type="hidden" id="municipio" name="municipio" value="{$infoRazonSocial.municipioAddress}" />
         	<input type="hidden" id="ciudad" name="ciudad" value="{$infoRazonSocial.municipioAddress}" />
         	<input type="hidden" id="cp" name="cp" value="{$infoRazonSocial.cpAddress}" />
         	<input type="hidden" id="estado" name="estado" value="{$infoRazonSocial.estadoAddress}" />
         	<input type="hidden" id="pais" name="pais" value="{$infoRazonSocial.paisAddress}" />
         	<input type="hidden" id="telefono" name="telefono" value="{$infoRazonSocial.telefonoContactoAdministrativo}" />
         	<input type="hidden" id="rfc" name="rfc" value="{$infoRazonSocial.rfc}" />
         	<input type="hidden" id="regimenFiscal" name="regimenFiscal" value="{$infoRazonSocial.nombreRegimen}" />
          Timbres?
          <select id="folios" name="folios">
          	<option value="50">50 Timbres</option>
          	<option value="100">100 Timbres</option>
          	<option value="150">150 Timbres</option>
          	<option value="200">200 Timbres</option>
          	<option value="500">500 Timbres</option>
          	<option value="1000">1000 Timbres</option>
          	<option value="2000">2000 Timbres</option>
          </select>
          Es Cliente Interno?
          <select id="interno" name="interno">
          	<option value="No">No</option>
          </select>
          <input type="submit" value="Genera cuenta en Comprobante Digital" />
         </form>  
      </div> 
      {/if}    

      <div class="portlet-content nopadding borderGray" id="contenido">
          {include file="forms/view-contract-new.tpl"}            
      </div>
      
        <div style="clear:both"></div>
        
        <div class="divBotones" align="center">
        	<div id="divLoading">
                <img src="{$WEB_ROOT}/images/loading.gif" />
                <br />Guardando...
            </div>
                {*if $User.roleId < 3}
            <div class="btnSave" onclick="VerifyForm()"></div>
            		{/if*}
        </div>
      
    </div>

 </div>
  <div class="clear"> </div>
  <div class="grid_9">
  &nbsp;
  </div>
  <div class="grid_7 backbutton" id="eventbox">
      {if $User.roleId < 4}

      <a href="{$WEB_ROOT}/customer" >Regresar</a>
      {/if}
  </div>
</div>
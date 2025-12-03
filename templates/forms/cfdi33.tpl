{if $info.version == "auto"}
	No puedes facturar debido a que tu esquema de CBB expir&oacute; el 1ro de Abril. Por favor contactenos a nuestros telefonos o correos de asistencia para un cambio de esquema a CFDi.
{else}
<div id="divForm">
	<form id="nuevaFactura" name="nuevaFactura" method="post">
        <input type="hidden" id="calle" name="calle" value="" />
        <input type="hidden" id="pais" name="pais" value="" />
        <input type="hidden" id="userId" name="userId" value="" />
        <input type="hidden" id="parent" name="parent" value="" />
        <input type="hidden" id="ticketChain" name="ticketChain" value="{$ticketChain}" />
        {if isset($notaVentaId)}
        	<input type="hidden" id="notaVentaId" name="notaVentaId" value="{$notaVentaId}" />
        {/if}
    <fieldset>
      <div class="formLineFact">
         <div class="container_16">
           <div class="grid_8">
               <div class="fullWidthTitleFormControl">Modalidad de factura</div>
               <div style="width:100%;float:left">
                   <select name="modo_factura" id="modo_factura" class="largeInput">
                       <option value="">-- seleccionar una opción --</option>
                       <option value="1">Generar factura normal</option>
                       <option value="2">Generar factura para sustitución</option>
                   </select>
               </div>
           </div>
         </div>
          <div class="clearfix">
      </div>
      <div class=""><hr></div>
      <div class="formLineFact sustitucionInvoice noShow">
          <div class="container_16">
              <div class="grid_4">
                  <div class="fullWidthTitleFormControl">Serie</div>
                  <div style="width:100%;float:left">
                      <input type="text"
                             id="serieAnterior"
                             name="serieAnterior"
                             placeholder="Capturar serie"
                             class="largeInput">
                  </div>
              </div>
              <div class="grid_4">
                  <div class="fullWidthTitleFormControl">Folio</div>
                  <div style="width:100%;float:left">
                      <div style="width:100%;float:left">
                          <input type="number"
                                 id="folioAnterior"
                                 name="folioAnterior"
                                 placeholder="Capturar folio"
                                 class="largeInput">
                      </div>
                  </div>
              </div>
              <div class="grid_4">
                  <div id="loading-cargar-dato" class="noShow">
                      <img src="{$WEB_ROOT}/images/loading.gif"/>
                  </div>
                  <div style="width:100%;float:left">&nbsp;</div>
                  <div style="width:100%;float:left">
                     <a class="button"
                        id="btnLoadDataBefore"
                        title="Cargar datos"><span>Cargar datos de la factura</span></a>
                  </div>
              </div>
          </div>
        <div class="clearfix"></div>
          <div class=""><hr></div>
      </div>

      {if $version == "auto" && ($info.usuarioId == 272 || $info.empresaId == 165 || $info.empresaId == 180)}
            <div>Sobrescribir Fecha y Folio</div>
            <div class="formLineFact" style="text-align:left;">
                <div style="width:90px;float:left">Fecha:</div>
                <div style="width:40px;float:left">D&iacute;a:</div>
                <div style="width:60px;float:left">
                    <input name="fechaSobreDia" id="fechaSobreDia" type="text" value="{$post.rfc}" size="2" class="largeInput" placeholder="dd" maxlength="2"/>
                </div>
                <div style="width:40px;float:left">Mes:</div>
                <div style="width:60px;float:left">
                    <input name="fechaSobreMes" id="fechaSobreMes" type="text" value="{$post.rfc}" size="2" class="largeInput" placeholder="mm" maxlength="2"/>
                </div>
                <div style="width:40px;float:left">A&ntilde;o:</div>
                <div style="width:80px;float:left">
                    <input name="fechaSobreAnio" id="fechaSobreAnio" type="text" value="{$post.rfc}" size="4" class="largeInput" placeholder="aaaa" maxlength="4"/>
                </div>
                <div style="width:90px;float:left">Folio:</div>
                <div style="width:172px;float:left">
                    <input name="folioSobre" id="folioSobre" type="text" value="{$post.rfc}" size="20" class="largeInput"/>
                </div>
                <div style="clear:both"></div>
            </div>
            {/if}
      <div><span id="loadingDivDatosFactura"></span></div>
      <div class="formLineFact normalInvoice noShow" style="text-align:left;">
            <div class="titleLabel">B&uacute;sca RFC o Raz&oacute;n Social:</div>
            <div class="controlMd"><input name="rfc" id="rfc" type="text" value="{$post.rfc}" class="largeInput" autocomplete="off"/>
            <div style="position:relative">
                <div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv"></div>
            </div>
        </div>
        <div class="titleLabel">Raz&oacute;n Social:</div>
            <div class="controlLg">
                <textarea name="razonSocial" id="razonSocial" disabled="disabled"
                          style="background-color:#eee; overflow:auto;"
                          cols="63"
                          rows="5">{$post.razonSocial}&#10;{$post.calle} {$post.noExt}</textarea>
            </div>
            <div style="clear:both"></div>
      </div>
      <div class="formLineFact normalInvoice noShow">
          <div class="titleLabel">Forma de Pago:(*)</div>
          <div class="controlLg">
            <select name="formaDePago" id="formaDePago"  class="largeInput">
            {foreach from=$formasDePago item=formaDePago}
                <option value="{$formaDePago.c_FormaPago}"
                    {if $formaDePago.c_FormaPago == "01"} selected{/if}
                >{$formaDePago.descripcion}</option> <br />
            {/foreach}
            </select>
          </div>
          <div class="titleLabel">N&uacute;mero de Cuenta:</div>
          <div class="controlSm"><input name="NumCtaPago" id="NumCtaPago" type="text" value=""  size="4" maxlength="4" class="largeInput"/></div>
          <div  class="moreBtnControl" onclick="ToggleDiv('facturaOpciones')"><b>[+] M&aacute;s Opciones</b></div>
          <div style="clear:both"></div>
      </div>
      <div class="formLine normalInvoice noShow"><hr/></div>
		<div id="facturaOpciones" style="display:none" class="normalInvoice noShow">
            <div class="formLineFact">
                <div class="titleLabel">% de IVA:</div>
                <div class="controlSm">
                 <select name="tasaIva" id="tasaIva" class="largeInput" style="width:100px">
                    {foreach from=$ivas item=iva}
                        <option value="{$iva}">{$iva}</option> <br />
                    {/foreach}
                </select></div>
                <div class="titleLabel">Tipo de Moneda:</div>
                <div class="controlSm">
                 <select name="tiposDeMoneda" id="tiposDeMoneda"  class="largeInput" >
                {foreach from=$tiposDeMoneda item=moneda}
                <option value="{$moneda.tipo}">{$moneda.moneda}</option>
                {/foreach}
                </select></div>

                <div class="titleLabel">Tipo de Cambio:</div>
                <div class="controlSm"><input name="tipoDeCambio" id="tipoDeCambio" type="text" value="{$post.tipoDeCambio}" maxlength="7"  class="largeInput" /></div>

                <div class="titleLabel">% de Descuento:</div>
                <div class="controlXsm"> <input name="porcentajeDescuento" id="porcentajeDescuento" type="text" value="{$post.porcentajeDescuento}" maxlength="5"  class="largeInput" /></div>

                <div style="clear:both"></div>
              </div>
            <div class="formLineFact">
                <div class="titleLabel">Metodo de Pago:</div>
                <div class="controlLg">
                    <select name="metodoDePago" id="metodoDePago"  class="largeInput">
                    {foreach from=$metodosDePago item=metodoDePago}
                        <option value="{$metodoDePago.c_MetodoPago}"
                            {if $metodoDePago.c_MetodoPago == "PUE"} selected{/if}
                        >{$metodoDePago.descripcion}({$metodoDePago.c_MetodoPago})</option> <br />
                    {/foreach}
                    </select>
                </div>
                <div class="titleLabel">Condiciones de Pago:</div>
                <div class="controlLg"><input name="condicionesDePago" id="condicionesDePago" type="text" value="{$post.condicionesDePago}" class="largeInput" style="width:390px"/></div>
                <div style="clear:both"></div>
              </div>
            <div class="formLineFact">
                <div class="titleLabel">% Retenci&oacute;n Iva:</div>
                <div class="controlSm">
                    <select name="porcentajeRetIva" id="porcentajeRetIva"  class="largeInput">
                    {foreach from=$retIvas item=iva}
                    <option value="{$iva}">{$iva}</option> <br />
                    {/foreach}
                    </select>
                </div>
                <div class="titleLabel">% Retenci&oacute;n ISR:</div>
                <div class="controlSm">
                    <select name="porcentajeRetIsr" id="porcentajeRetIsr"  class="largeInput">
                    {foreach from=$retIsrs item=isr}
                    <option value="{$isr}">{$isr}</option> <br />
                    {/foreach}
                    </select>
                </div>
                <div class="titleLabel">% IEPS:</div>
                <div class="controlSm"><input name="porcentajeIEPS" id="porcentajeIEPS" type="text" value="{$post.porcentajeIEPS}" size="12"  class="largeInput"  onblur="UpdateIepsConcepto()"/></div>

                <div class="titleLabel">Sucursal:</div>
                <div class="controlMd">
                <select name="sucursalId" id="sucursalId"  class="largeInput" style="width:185px">
                {foreach from=$sucursales item=sucursal}
                <option value="{$sucursal.sucursalId}">{$sucursal.identificador}</option>
                {/foreach}
                </select></div>

                <div style="clear:both"></div>
              </div>
      </div>
      <div class="formLine noShow" id="jumpfacturaOpciones" style="display:none">
        <hr />
      </div>
      <div class="formLineFact normalInvoice noShow">
          <div class="titleLabel">Seleccionar Serie:</div>
          <div class="controlLg">
              <select name="tiposComprobanteId" id="tiposComprobanteId"  class="largeInput" style="width:315px">
                  {foreach from=$seriesTiposComprobante item=serie}
                      <option value="{$serie.tiposComprobanteId}-{$serie.serieId}">
                          {$serie.razonSocial} - {$serie.serie}{$serie.consecutivo}
                      </option>
                  {/foreach}
              </select>
          </div>
          <div class="titleLabel">Facturar con version :</div>
          <div class="controlMd">
              <select name="usarCfdiEspecifico" id="usarCfdiEspecifico"  class="largeInput" style="width:100px">
                  <option value="3.3" {if $versionCFDIDefault == 3.3}selected{/if}> CFDI 3.3</option>
                  <option value="4.0" {if $versionCFDIDefault == 4.0}selected{/if}> CFDI 4.0</option>
              </select>
          </div>
				{if $SITENAME == "FACTURASE" && ($info.empresaId == 249 || $info.empresaId == 307 || $info.empresaId == 308 || $info.empresaId == 483 || $info.empresaId == 535)}
                    <div style="width:50px;float:left">Formato normal?:</div>
                    <div style="width:40px;float:left">
        	            <input name="formatoNormal" id="formatoNormal" type="checkbox" value="1" class="largeInput"/>
			        </div>
    		    {/if}
      	  <div style="clear:both"></div>
      </div>
      <div class="formLineFact normalInvoice noShow">
          <div class="titleLabel">Uso CFDi:</div>
          <div class="controlLg">
              <select name="usoCfdi" id="usoCfdi"  class="largeInput" style="width:315px">
                {foreach from=$usoCfdi item=uso}
                    <option value="{$uso.c_UsoCfdi}" {if $uso.c_UsoCfdi == 'G03'}selected{/if}>{$uso.descripcion}</option>
                {/foreach}
              </select>
          </div>
          <div class="titleLabel">Fecha de timbrado:</div>
          <div class="controlLg">
                <select name="fechaTimbrado" id="fechaTimbrado" class="largeInput" style="width:315px">
                    <option value="">Fecha actual</option>
                    <option value="24">24 horas anterior</option>
                    <option value="48">48 horas anterior</option>
                    <option value="72">72 horas anterior</option>
                </select>
          </div>
       	 <div style="clear:both"></div>
      </div>
      <div class="formLineFact normalInvoice noShow">
          <div class="titleLabel">CFDi relacionado Serie:</div>
          <div class="controlSm">
            <input name="cfdiRelacionadoSerie" id="cfdiRelacionadoSerie" type="text" value="" placeholder="A" class="largeInput" size="6"/>
          </div>
          <div class="titleLabel">Folio:</div>
          <div class="controlSm">
            <input name="cfdiRelacionadoFolio" id="cfdiRelacionadoFolio" type="text" value="" placeholder="125" class="largeInput"  size="6"/>
          </div>
          <div class="titleLabel" ">Tipo relacion:</div>
      <div class="controlLg normalInvoice noShow">
          <select name="tipoRelacion" id="tipoRelacion"  class="largeInput" style="width:315px">
            <option value="">No tiene CFDi relacionado</option>
         	{foreach from=$tipoRelacion item=relacion}
                <option value="{$relacion.c_TipoRelacion}">{$relacion.descripcion}</option>
            {/foreach}
          </select>
          <div style="clear:both"></div>
      </div>
         {if $info.empresaId == 113}
            <div class="formLineFact">
                <div style="width:90px;float:left">Tiempo Limite:</div>
                <div style="width:135px;float:left">
                <input name="tiempoLimite" id="tiempoLimite" type="text" value="" size="18"  class="largeInput"/>
                </div>
            </div>
         {/if}
         {if $version == "auto" && ($info.empresaId == 39 || $info.empresaId == 180)}
          <div class="formLineFact">
              <div style="width:90px;float:left">% ISH:</div>
              <div style="width:126px;float:left"><input name="porcentajeISH" id="porcentajeISH" type="text" value="2"  size="12"  class="largeInput"/></div>
              <div style="clear:both"></div>
          </div>
     {/if}
         <div class="formLine normalInvoice noShow"><hr /></div>
         <span id="loadingDivConcepto"></span>
         <div class="formLineFact normalInvoice noShow">
              <div class="colDiv"><em style="color: #ff0000">*</em> Cantidad</div>
              <div class="colDiv"><em style="color: #ff0000">*</em> # Identificacion</div>
              <div class="colDiv"> <em style="color: #ff0000">*</em> Unidad</div>
              <div class="colDiv"><em style="color: #ff0000">*</em> Precio S/IVA</div>
              <div class="colDiv">Precio C/IVA</div>
              <div class="colDiv">Exento Iva</div>
              <div style="clear:both"></div>
         </div>
	</form>
    <form id="conceptoForm" name="conceptoForm" class="normalInvoice noShow">
            <input type="hidden" id="type" name="type" value="agregarConcepto" />
			<input type="hidden" id="totalConceptos" value="$conceptos" >
			<input type="hidden" id="conceptoId" name="conceptoId" value="" >
			<input type="hidden" id="nombreServicioOculto" name="nombreServicioOculto" value="">
      <div class="formLineFact">
          <div class="colDiv">
          <input name="cantidad" id="cantidad" type="text" value="{$post.cantidad}"  size="8" class="largeInput" placeholder="Cantidad"/></div>
          <div class="colDiv">
          <input name="noIdentificacion" id="noIdentificacion" type="text" value="{$post.noIdentificacion}"  size="8"  class="largeInput" placeholder="# Id"/>
              <div style="position:relative">
                    <div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionProductDiv">
                    </div>
              </div>
          </div>
          <div class="colDiv">
          <input name="unidad" id="unidad" type="text" value="{$post.unidad}"  size="8" class="largeInput"  placeholder="Unidad"/></div>
          <div class="colDiv">
          <input name="valorUnitario" id="valorUnitario" type="text" value="{$post.valorUnitario}"  size="8" class="largeInput"  placeholder="Valor S/I"  onblur="UpdateValorUnitarioConIva()"/></div>
          <div class="colDiv">
          <input name="valorUnitarioCI" id="valorUnitarioCI" type="text" value="{$post.valorUnitarioCI}"  size="8" class="largeInput"  placeholder="Valor C/I" onblur="UpdateValorUnitarioSinIva()"/></div>
          <div class="colDiv">
              <select name="excentoIva" id="excentoIva" class="largeInput">
                {foreach from=$excentoIva item=iva}
                    <option value="{$iva}">{$iva}</option> <br />
                {/foreach}
              </select>
          </div>
          <div class="colDiv">
              <div  id="agregarConceptoDiv" class="button">
                  <span id="agregarConceptoDivSpan">Agregar</span>
              </div>
              <div style="padding-top: 3px"></div>
              <div  id="cancelarConceptoDiv" class="button" onclick="CancelarConcepto()">
                  <span id="cancelarConceptoDivSpan">Cancelar</span>
              </div>
          </div>
          <div class="colDiv">

          </div>
      	  <div style="clear:both"></div>
        </div>
      <div class="formLineFact">
          <div class="colDiv">Clv Prod o Serv</div>
          <div class="colDiv">Clave Unidad</div>
          <div class="colDiv">C. Predial</div>
          <div class="colDiv">IEPS Tasa o Cuota</div>
          <div class="colDiv">IEPS</div>
          <div class="colDiv">ISH</div>
      	  <div style="clear:both"></div>
      </div>
      <div class="formLineFact">
          <div class="colDiv">
              <input name="c_ClaveProdServ" id="c_ClaveProdServ" type="text" value="84111500"  size="8" class="largeInput" placeholder=""/></div>
          <div class="colDiv">
              <input name="c_ClaveUnidad" id="c_ClaveUnidad" type="text" value="E48"  size="8"  class="largeInput" placeholder=""/>
          </div>

          <div class="colDiv">
            <input name="cuentaPredial" id="cuentaPredial" type="text" value="{$post.cuentaPredial}"  size="8" class="largeInput"  placeholder="Opcional"/>
          </div>
          <div class="colDiv">
            <select style="width: 100px" name="iepsTasaOCouta" id="iepsTasaOCouta" class="largeInput">
                <option value="Tasa">Tasa</option>
                <option value="Cuota">Cuota</option>
            </select>
          </div>
          <div class="colDiv">
            <input name="iepsConcepto" id="iepsConcepto" type="text" value="{$post.ieps}"  size="8" class="largeInput"  placeholder="IEPS"/>
          </div>
          <div class="colDiv">
            <input name="ishConcepto" id="ishConcepto" type="text" value="{$post.ish}"  size="8" class="largeInput"  placeholder="% ISH"/>
          </div>
      		<div style="clear:both"></div>
        </div>

        {if $info.moduloImpuestos == "Si"}
        {if $expiredImpuestos}
      	    Modulo de Impuestos Locales Congelado hasta confirmacion de Pago.
        {else}
                <div class="formLineFact">
                    <div style="width:500px;float:left"><b>Extras para impuestos (Si es 0, no se mostrara)</b></div>
                    <div style="width:100px;float:left">Subtotal</div>
                    <div style="width:100px;float:left">IVA</div>
                    <div style="clear:both"></div>
                </div>
                <div class="formLineFact">
                    <div style="width:500px;float:left">
                      <input name="amortizacionFiniquito" id="amortizacionFiniquito" type="text" size="48" class="largeInput" placeholder="" value="IMPORTE DE LA ESTIMACION No 01 (UNO) Y FINIQUITO"/></div>
                    <div style="width:100px;float:left">
                      <input name="amortizacionFiniquitoSubtotal" id="amortizacionFiniquitoSubtotal" type="text" value="0.00"  size="8"  class="largeInput" placeholder=""/>
                    </div>
                    <div style="width:100px;float:left">
                      <input name="amortizacionFiniquitoIva" id="amortizacionFiniquitoIva" type="text" value="0.00"  size="8"  class="largeInput" placeholder=""/>
                    </div>
                    <div style="clear:both"></div>
                </div>
                <div class="formLineFact">
                    <div style="width:300px;float:left">Amortizacion del anticipo</div>
                    <div style="width:200px;float:left">IVA Amortizacion</div>
                    <div style="clear:both"></div>
                </div>
                <div class="formLineFact">
                    <div style="width:300px;float:left">
                      <input name="amortizacion" id="amortizacion" type="text" size="28" class="largeInput" placeholder="" value="0.00"/></div>
                    <div style="width:200px;float:left">
                      <input name="amortizacionIva" id="amortizacionIva" type="text" value="0.00"  size="8"  class="largeInput" placeholder=""/>
                    </div>
                    <div style="clear:both"></div>
                </div>
            {/if}
            {/if}
        <div class="formLineFact">
            <div class="colDiv300">Fecha correspondiente a facturar</div>
            <div class="colDiv300 divVincularToServicio">Vincular concepto a servicio de empresa</div>
            <div style="clear:both"></div>
        </div>
      <div class="formLineFact">
        <div class="colDiv300">
            <input name="fechaCorrespondiente"
                   id="fechaCorrespondiente"
                   type="date"
                   value=""
                   class="largeInput"
                   placeholder="Fecha correspondiente"
            />
        </div>
        <div class="colDiv300 divVincularToServicio">
            <input name="vincularToServicio"
                   id="vincularToServicio"
                   type="checkbox"
                   class="largeInput"
            />
        </div>
        <div style="clear:both"></div>
      </div>
      <div class="formLineFact">
          <label><em style="color: #ff0000">*</em> Descripción</label>
          <div style="width:900px;float:left;">
            <textarea placeholder="Escribe tu concepto aquí" name="descripcion" id="descripcion" cols="33" rows="5" class="largeInput wide" style="font-family: Courier New, Courier, monospace !important">{$post.descripcion}</textarea>
          </div>
      		<div style="clear:both"></div>
      		<span style="color: #f00; font-weight: bold">La descripción solo puede tener un maximo de 1000 caracteres. Nueva regla del SAT! </span>
          <div style="clear:both"></div>
            <span style="color: #f00; font-weight: bold">Importante: Utilizar en la descripcion solo nombres de servicio presentes en catalogo.</span>
      </div>
      <div class="formLineFact"></div>
	  </form>
    <div class="formLine normalInvoice noShow">
        <hr />
    </div>
    <b>Conceptos Cargados:</b>
    <div id="conceptos" class="normalInvoice noShow">
            {if $conceptos|count > 0}
                {include file="{$DOC_ROOT}/templates/lists/conceptos.tpl"}
            {else}
                Ninguno (Has click en Agregar para agregar un concepto)
            {/if}
    </div>
    <br /><br />
    <div class="formLineFact normalInvoice noShow">
          <div>Observaciones</div>
          <div><textarea placeholder="Observaciones" name="observaciones" cols="33" rows="5" id="observaciones" class="largeInput wide"></textarea></div>
  	</div>
    <div class="formLine normalInvoice noShow">
        <hr />
    </div>
    {if $info.moduloImpuestos == "Si"}
        {if $expiredImpuestos}
      	    Modulo de Impuestos Locales Congelado hasta confirmacion de Pago.
        {else}
        <div class="formLineFact normalInvoice noShow">
		    <div style="width:300px;float:left; cursor:pointer" onclick="ToggleDiv('impuestosOpciones')">[+] Mostrar Formulario de Impuestos<br /><br /></div>
		</div>
		<span id="loadingDivImpuesto"></span>
		<br>

        <div style="clear:both"></div>
        <div id="impuestosOpciones" style="display:none">
            <div class="formLineFact">
                <div style="width:80px;float:left">Tasa %:</div>
                <div style="width:350px;float:left">Impuesto</div>
                <div style="width:80px;float:left">IVA%</div>
                <div style="width:80px;float:left">Importe</div>
            </div>
            <div style="clear:both"></div>

            <form id="impuestoForm" name="impuestoForm">
            <div class="formLineFact">
                <div style="width:80px;float:left">
                    <input name="tasa" id="tasa" type="text" value="{$post.tasa}"  size="5" class="largeInput"/>
                </div>
                <div style="width:350px;float:left">
                    <input name="impuestoId" id="impuestoId" type="text" value="{$post.impuestoId}"  size="39" class="largeInput"/>
                    <div style="position:relative">
                        <div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionImpuestoDiv"></div>
                    </div>
                </div>
                <div style="width:80px;float:left">
                    <input name="iva" id="iva" type="text" value="0"  size="5" class="largeInput"/></div>
                <div style="width:80px;float:left">
                    <input name="importe" id="importe" type="text" value="0"  size="5" class="largeInput"/></div>
                <div style="width:146px;float:left">
                    <select name="tipo" id="tipo" class="largeInput">
                        <option value="retencion">Retenci&oacute;n</option>
                        <option value="deduccion">Deducci&oacute;n</option>
                        <option value="impuesto">Impuesto</option>
                        <option value="amortizacion">Amortizaci&oacute;n</option>
                    </select>
                </div>
                <div style="width:145px;float:left; cursor:pointer" id="agregarImpuestoDiv" class="button">
                    <span>Agregar Impuesto</span>
                    </div>
                <div style="clear:both"></div>
              </div>
            </form>
        Impuestos Cargados:
        <div id="impuestos">
            Ninguno (Has click en Agregar Impuesto o Retenci&oacute;n para agregar)
        </div>
        <br /><br />
        <div class="formLineFact" style="float:left; width:180px">
            <div>Autorizo:</div>
            <div><textarea name="autorizo" id="autorizo" class="largeInput" style="text-align:center"></textarea></div>
        </div>
        <div class="formLineFact"  style="float:left; width:180px">
            <div>Recibi&oacute;:</div>
            <div><textarea name="recibio" id="recibio" class="largeInput" style="text-align:center"></textarea></div>
        </div>
        <div class="formLineFact"  style="float:left; width:180px">
            <div>VoBo:</div>
            <div><textarea name="vobo" id="vobo" class="largeInput" style="text-align:center"></textarea></div>
        </div>
        <div class="formLineFact"  style="float:left; width:180px">
            <div>Reviso:</div>
            <div><textarea name="reviso" id="reviso" class="largeInput" style="text-align:center"></textarea></div>
        </div>
        <div class="formLineFact"  style="float:left; width:180px">
            <div>Pago:</div>
            <div><textarea name="pago" id="pago" class="largeInput" style="text-align:center"></textarea></div>
        </div>
        <div style="clear:both"></div>
            </div>
      {/if}
    {/if}
        <div style="clear:both"></div>
     	<div class="formLineFact normalInvoice noShow">
        	<div>Totales Desglosados</div>
          	<div id="totalesDesglosadosDiv">
			{if $totalDesglosado|count > 0}
            	{include file="{$DOC_ROOT}/templates/boxes/total-desglosado.tpl"}
            {else}
				<b>Para poder generar un Comprobante necesitas Agregar al menos un concepto</b>
			{/if}
          	</div>
            <br />
            <div id="showFactura"></div>
		</div>
        <div class="formLine">
            <hr />
        </div>
	    <div class="formLineFact normalInvoice noShow" style="width: 100%;text-align:center;" id="reemplazarBoton">
            <a class="button" id="generarFactura" name="generarFactura"><span>Generar Comprobante</span></a>
            <a class="button" id="vistaPrevia" name="vistaPrevia"><span>Vista Previa</span></a>
            {if $ticketId>0}
              <a class="button" id="regresarVentas" name="regresarVentas" onClick="regresarVentas();" ><span>Regresar</span></a>
            {/if}
     	</div>
      	<div style="clear:both"></div>
        </div>
  	</fieldset>
</div>
{/if}

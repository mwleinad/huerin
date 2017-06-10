<div id="divForm">
	<form id="nuevaFactura" name="nuevaFactura" method="post">
        <input type="hidden" id="userId" name="userId" value="" />
    <fieldset>

				<div>
        	Datos de Facturacion <span id="loadingDivDatosFactura"></span>
        </div>
        <div class="formLine" style="text-align:left;">
        <div style="width:90px;float:left">RFC Receptor:</div> 
        <div style="width:172px;float:left"><input name="rfc" id="rfc" type="text" value="{$post.rfc}" style="width:150px" class="largeInput" autocomplete="off" />
        <div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         </div>
        </div>
        <div style="width:140px;float:left">Razon Social:</div>
        <div style="width:320px;float:left"><input name="razonSocial" id="razonSocial" type="text" value="{$post.razonSocial}"  disabled="disabled" style="background-color:#eee; width:500px" class="largeInput"/></div>
       	<div style="clear:both"></div>
       
      </div>
       <div class="formLine" style="padding-top:3px">
          <div style="width:90px;float:left">Calle:</div> 
          <div style="width:320px;float:left"><input name="calle" id="calle" type="text" value="{$post.calle}" disabled="disabled" style="background-color:#eee; width:280px" class="largeInput"/></div>
          <div style="width:90px;float:left">No. Exterior:</div> 
          <div style="width:155px;float:left"><input name="noExt" id="noExt" type="text" value="{$post.noExt}" disabled="disabled" style="background-color:#eee" size="15" class="largeInput"/></div>
          <div style="width:90px;float:left">No. Interior:</div>
          <div style="width:155px;float:left"><input name="noInt" id="noInt" type="text" value="{$post.noInt}" disabled="disabled" style="background-color:#eee" size="18" class="largeInput"/></div>
      		<div style="clear:both"></div>
        </div>  

      <div class="formLine" style="padding-top:3px">
          <div style="width:90px;float:left">Colonia:</div> 
          <div style="width:320px;float:left"><input name="colonia" id="colonia" type="text" value="{$post.colonia}" disabled="disabled" style="background-color:#eee; width:280px" size="40" class="largeInput"/></div>
          <div style="width:90px;float:left">Municipio:</div> 
          <div style="width:155px;float:left"><input name="municipio" id="municipio" type="text" value="{$post.municipio}" disabled="disabled" style="background-color:#eee" size="15" class="largeInput"/></div>
          <div style="width:90px;float:left">Estado:</div>
          <div style="width:155px;float:left"><input name="estado" id="estado" type="text" value="{$post.estado}" disabled="disabled" style="background-color:#eee" size="18" class="largeInput" /></div>
      		<div style="clear:both"></div>
        </div>  

      <div class="formLine" style="padding-top:3px">
          <div style="width:90px;float:left">Localidad:</div> 
          <div style="width:320px;float:left"><input name="localidad" id="localidad" type="text" value="{$post.localidad}" disabled="disabled" style="background-color:#eee; width:280px" size="40" class="largeInput"/></div>
          <div style="width:90px;float:left">CP:</div> 
          <div style="width:155px;float:left"><input name="cp" id="cp" type="text" value="{$post.cp}" disabled="disabled" style="background-color:#eee" size="15" class="largeInput"/></div>
          <div style="width:90px;float:left">Pais:</div>
          <div style="width:155px;float:left"><input name="pais" id="pais" type="text" value="{$post.pais}" disabled="disabled" style="background-color:#eee" size="18" class="largeInput"/></div>
      		<div style="clear:both"></div>
        </div>  

      <div class="formLine" style="padding-top:3px">
          <div style="width:90px;float:left">Referencia:</div> 
          <div style="width:320px;float:left"><input name="referencia" id="referencia" type="text" value="{$post.referencia}" disabled="disabled" style="background-color:#eee; width:280px" size="40" class="largeInput"/></div>
          <div style="width:90px;float:left">Email:</div> 
          <div style="width:400px;float:left"><input name="email" id="email" type="text" value="{$post.email}" disabled="disabled" style="background-color:#eee;width:402px" class="largeInput"/></div>
      		<div style="clear:both"></div>
        </div>  

      <div class="formLine" style="padding-top:3px">
          <div style="width:90px;float:left">Forma de Pago:</div> 
          <div style="width:320px;float:left"><input name="formaDePago" id="formaDePago" type="text" value="Pago en Una Sola Exhibicion" style="width:280px" class="largeInput"/></div>
          <div style="width:90px;float:left">Condiciones de Pago:</div> 
          <div style="width:400px;float:left"><input name="condicionesDePago" id="condicionesDePago" type="text" value="{$post.condicionesDePago}" style="width:402px" class="largeInput"/></div>
      		<div style="clear:both"></div>
        </div>  
        
      <div class="formLine" style="padding-top:3px">
          <div style="width:90px;float:left">Metodo de Pago:</div> 
          <div style="width:200px;float:left">
              <select name="metodoDePago" id="metodoDePago" class="largeInput">
                  <option value="01">Efectivo</option>
                  <option value="02">Cheque</option>
                  <option value="03">Transferencia</option>
                  <option value="04">Tarjetas de Credito</option>
                  <option value="05">Monederos electrónicos</option>
                  <option value="06">Dinero electrónico</option>
                  <option value="08">Vales de despensa</option>
                  <option value="28">Tarjeta de Debito </option>
                  <option value="29">Tarjeta de Servicio </option>
                  <option value="99">Otros</option>
                  <option value="NA" selected="selected">NA</option>

              </select>
          </div>

          <div style="width:90px;float:left">Numero de Cuenta:</div> 
          <div style="width:100px;float:left"><input name="NumCtaPago" id="NumCtaPago" type="text" value=""  size="4" maxlength="4" class="largeInput"/></div>

          <div style="width:90px;float:left">% de IVA:</div> 
          <div style="width:120px;float:left">
           <select name="tasaIva" id="tasaIva" class="largeInput" style="width:100px">
         	{foreach from=$ivas item=iva}
          <option value="{$iva}">{$iva}</option> <br />
          {/foreach}
          </select></div>

          <div style="width:90px;float:left">Tipo de Moneda:</div>
          <div style="width:120px;float:left">
           <select name="tiposDeMoneda" id="tiposDeMoneda"  class="largeInput" style="width:100px">
         	{foreach from=$tiposDeMoneda item=moneda}
          <option value="{$moneda}">{$moneda}</option>
          {/foreach}
          </select></div>
      		<div style="clear:both"></div>
        </div>  

      <div class="formLine" style="padding-top:3px">
          <div style="width:90px;float:left">% Retencion Iva:</div> 
          <div style="width:135px;float:left">
          <select name="porcentajeRetIva" id="porcentajeRetIva"  class="largeInput">
         	{foreach from=$retIvas item=iva}
          <option value="{$iva}">{$iva}</option> <br />
          {/foreach}
          </select>
          </div>
          <div style="width:62px;float:left">% IEPS:</div> 
          <div style="width:126px;float:left"><input name="porcentajeIEPS" id="porcentajeIEPS" type="text" value="{$post.porcentajeIEPS}"  size="12"  class="largeInput"/></div>

          <div style="width:85px;float:left">% de Descuento:</div> 
          <div style="width:155px;float:left"><input name="porcentajeDescuento" id="porcentajeDescuento" type="text" value="{$post.porcentajeDescuento}"  size="15"  class="largeInput"/></div>
          <div style="width:90px;float:left">Tipo de Cambio:</div>
          <div style="width:135px;float:left"><input name="tipoDeCambio" id="tipoDeCambio" type="text" value="{$post.tipoDeCambio}" size="18"  class="largeInput"/></div>
      		<div style="clear:both"></div>
        </div>  

      <div class="formLine" style="padding-top:3px">
          <div style="width:90px;float:left">% Retencion ISR:</div> 
          <div style="width:135px;float:left">
          <select name="porcentajeRetIsr" id="porcentajeRetIsr"  class="largeInput">
         	{foreach from=$retIsrs item=isr}
          <option value="{$isr}">{$isr}</option> <br />
          {/foreach}
          </select></div>
          <div style="width:90px;float:left">Comprobante:</div> 
          <div style="width:340px;float:left">
          <select name="tiposComprobanteId" id="tiposComprobanteId"  class="largeInput" style="width:315px">
          <option value="0">Seleccione...</option>
         	{foreach from=$comprobantes item=comprobante}
          <option value="{$comprobante.tiposComprobanteId}-{$comprobante.serieId}">
              {if $comprobante.serie == "B"}JACOBO BRAUN BRUCKMAN
              {elseif $comprobante.serie == "C"}BHSC CONTADORES SC
              {else}BRAUN HUERIN SC{/if} {$comprobante.nombre} - {$comprobante.serie}{$comprobante.consecutivo}</option>
          {/foreach}
          </select></div>
          <div style="width:90px;float:left">Sucursal:</div>
          <div style="width:155px;float:left">
          <select name="sucursalId" id="sucursalId"  class="largeInput" style="width:150px">
         	{foreach from=$sucursales item=sucursal}
          <option value="{$sucursal.sucursalId}">{$sucursal.identificador}</option> <br />
          {/foreach}
          </select></div>
      		<div style="clear:both"></div>
        </div>  

     <div class="formLine" style="padding-top:3px">
 					<hr />   
        </div>  

<span id="loadingDivConcepto"></span>
      <div class="formLine">
          <div style="width:150px;float:left">Cantidad:</div> 
          <div style="width:150px;float:left">No. Ident:</div> 
          <div style="width:150px;float:left">Unidad:</div>
          <div style="width:150px;float:left">Valor Unitario:</div>
          <div style="width:150px;float:left">Excento Iva:</div>
      		<div style="clear:both"></div>
        </div>  
	</form>

      <form id="conceptoForm" name="conceptoForm">
      <div class="formLine">
          <div style="width:150px;float:left">
          <input name="cantidad" id="cantidad" type="text" value="{$post.cantidad}"  size="15" class="largeInput" placeholder="Cantidad"/></div>
          <div style="width:150px;float:left">
          <input name="noIdentificacion" id="noIdentificacion" type="text" value="{$post.noIdentificacion}"  size="15"  class="largeInput" placeholder="No. Identificacion" autocomplete="off" />
          <div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionProductDiv">
        	 	</div>
         </div>
          </div>
          <div style="width:150px;float:left">
          <input name="unidad" id="unidad" type="text" value="{$post.unidad}"  size="15" class="largeInput"  placeholder="Unidad"/></div>
          <div style="width:150px;float:left">
          <input name="valorUnitario" id="valorUnitario" type="text" value="{$post.valorUnitario}"  size="15" class="largeInput"  placeholder="Valor Unitario"/></div>
          <div style="width:150px;float:left">
          <select name="excentoIva" id="excentoIva" class="largeInput" style="width:120px">
         	{foreach from=$excentoIva item=iva}
          <option value="{$iva}">{$iva}</option> <br />
          {/foreach}
          </select></div>
          <div style="width:132px;float:left; cursor:pointer" id="agregarConceptoDiv" class="button"><span>Agregar</span></div>
      		<div style="clear:both"></div>
        </div>  
      <div class="formLine">
          <div style="width:30%;float:left">
          <textarea placeholder="Escribe tu concepto aqui" name="descripcion" id="descripcion" cols="33" rows="5" class="largeInput wide">{$post.descripcion}</textarea>
</div>
      		<div style="clear:both"></div>
 					<hr />   
        </div>  
      {*if $info.empresaId == 15 || $info.empresaId == 86}  
      <div class="formLine">
          <div style="width:30%;float:left">
          Categoria: <input name="categoriaConcepto" id="categoriaConcepto" type="text" value="{$post.categoriaConcepto}"  size="100" class="largeInput"  placeholder="Categoria: Cambiar Si desea Crear una nueva categoria de Concepto."/>
</div>
      		<div style="clear:both"></div>
 					<hr />   
        </div>
      {/if*}    
			</form>
      Conceptos Cargados:
			<div id="conceptos">
      Ninguno (Has click en Agregar para agregar un concepto)
      </div>
      <br /><br />     


{if $version == "construc" || $version == "demo"}  
<span id="loadingDivImpuesto"></span>
      <div class="formLine">
          <div style="width:80px;float:left">Tasa %:</div> 
          <div style="width:350px;float:left">Impuesto</div> 
          <div style="width:80px;float:left">IVA%</div> 
          <div style="width:80px;float:left">Importe</div> 
      		<div style="clear:both"></div>
        </div>  

      <form id="impuestoForm" name="impuestoForm">
      <div class="formLine">
          <div style="width:80px;float:left">
          <input name="tasa" id="tasa" type="text" value="{$post.tasa}"  size="5" class="largeInput"/></div>
          <div style="width:350px;float:left">
          <input name="impuestoId" id="impuestoId" type="text" value="{$post.impuestoId}"  size="44" class="largeInput"/>
          <div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionImpuestoDiv">
        	 	</div>
         </div>
          </div>
          <div style="width:80px;float:left">
          <input name="iva" id="iva" type="text" value="0"  size="5" class="largeInput"/></div>
          <div style="width:80px;float:left">
          <input name="importe" id="importe" type="text" value="0"  size="5" class="largeInput"/></div>
          <div style="width:146px;float:left">
          <select name="tipo" id="tipo" class="largeInput">
        <option value="retencion">Retencion</option>
        <option value="deduccion">Deduccion</option>
        <option value="impuesto">Impuesto</option>
        <option value="amortizacion">Amortizacion</option>
        </select>
        </div>
          <div style="width:145px;float:left; cursor:pointer" id="agregarImpuestoDiv" class="button"><span>Agregar Impuesto</span></div>
      		<div style="clear:both"></div>
 					<hr />   
        </div>  
			</form>
      Impuestos Cargados:
			<div id="impuestos">
      Ninguno (Has click en Agregar Impuesto o Retencion para agregar)
      </div>
      <br /><br />     

{if $info.empresaId == 15}
     <div class="formLine">
          <div>menos-2% I.S.N:</div> 
          <div><input name="isn" id="isn" /></div>
  		</div>        
     <div class="formLine">
          <div>menos-5% al millar S.F.P</div> 
          <div><input name="spf" id="spf" /></div>
  		</div>        
{/if}

     <div class="formLine" style="float:left; width:180px">
          <div>Autorizo:</div> 
          <div><textarea name="autorizo" id="autorizo" class="largeInput" style="text-align:center"></textarea></div>
  		</div>        
     <div class="formLine"  style="float:left; width:180px">
          <div>Recibio:</div> 
          <div><textarea name="recibio" id="recibio" class="largeInput" style="text-align:center"></textarea></div>
  		</div>        
     <div class="formLine"  style="float:left; width:180px">
          <div>VoBo:</div> 
          <div><textarea name="vobo" id="vobo" class="largeInput" style="text-align:center"></textarea></div>
  		</div>
     <div class="formLine"  style="float:left; width:180px">
          <div>Reviso:</div> 
          <div><textarea name="reviso" id="reviso" class="largeInput" style="text-align:center"></textarea></div>
  		</div>        
     <div class="formLine"  style="float:left; width:180px">
          <div>Pago:</div> 
          <div><textarea name="pago" id="pago" class="largeInput" style="text-align:center"></textarea></div>
 					<hr />   
  		</div>
      <div style="clear:both"></div>     

{/if}
     <div class="formLine">
          <div>Observaciones</div> 
          <div><textarea placeholder="Observaciones" name="observaciones" cols="33" rows="5" id="observaciones" class="largeInput wide"></textarea></div>
 					<hr />   
  		</div>        
     <div class="formLine">
          <div>Totales Desglosados</div> 
          <div id="totalesDesglosadosDiv">
          Necesitas Agregar al menos un concepto
          </div>
 					<hr />   
  		</div>        


      <div style="clear:both"></div>


	    	<div class="formLine" style="text-align:center" id ="reemplazarBoton">
        <a class="button" id="generarFactura" name="generarFactura"><span>Generar Comprobante</span></a>
        <a class="button" id="vistaPrevia" name="vistaPrevia"><span>Vista Previa</span></a>
        
     	</div>
         
  	</fieldset>
    
 
</div>

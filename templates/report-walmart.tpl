<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="reportes">Reporte Walmart - Hist. de Documentaci&oacute;n</h1>
  </div>
  
  <div class="grid_6" id="eventbox">
      
  </div>
  
  <div class="clear">
  </div>
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
           
        {include file="{$DOC_ROOT}/templates/boxes/report-walmart-status.tpl"}
      
        <div style="clear:both"></div>
        <br />
        
        <div align="center">
        <table width="900" cellpadding="0" cellspacing="0" border="1" class="bordTbl_" align="center">
         <tr>
            <td width="200" align="center" height="50"><b>CONTROL DE DOCUMENTACION</b></td>
            <td width="50" align="center"></td>
            <td width="50" align="center"></td>
            <td width="50" align="center"></td>
            <td width="200" align="center"><b>OBLIGACIONES</b></td>
            <td width="50" align="center"></td>
            <td width="" align="center"></td>
            <td width="50" align="center"></td>
            <td width="200" align="center"><b>CONTROL ADMVO. INTERNO</b></td>
        </tr>
                
        {foreach from=$docs item=item key=key}
        <tr>
        	<td align="center" height="190">
            {if $item.B.name != ""}
            <div class="boxEntW st{$item.B.status} txtSt{$item.B.status}" id="rounded-box2">
            	<b>Documento:</b>
                <br />
                {$item.B.name}
                <br /><br />
                <b>Proyecto:</b>
                <br />
            	{$item.B.proyecto}
                <br /><br />
                <b>Fecha de Recibido:</b> 
                <br />
                {if $item.B.fechaRec == ''}
                	Pendiente
                {else}
                	{$item.B.fechaRec}
                {/if}
            </div>
            {/if}
            </td>
            <td>
            {if $item.B.name != ""}
             <br /><br />
             ---------
            {/if}
            </td>
            <td class="timeLine">
            <br /><br />
                {if $item.B.mes}
                	{$item.B.mes}
                {else}
                	{$item.O.mes}
                {/if}
            </td>
            <td>
            {if $item.O.name != ""}
             <br /><br />
             ---------
             {/if}
            </td>
            <td align="center">
            {if $item.O.name != ""}           
             <div class="boxEntW st{$item.O.status} txtSt{$item.O.status}" id="rounded-box2">
                <b>Documento:</b>
                <br />
                {$item.O.name}
                <br /><br />
                <b>Proyecto:</b>
                <br />
                {$item.O.proyecto}                
                <br /><br />                
                {if $item.O.fechaRec}
                    <b>Fecha de Recibido:</b> {$item.O.fechaRec}
                    <br />
                    <b>Fecha de Entrega:</b> {$item.O.fechaEnt}
                {else}                
                    <b>Fecha de Entrega:</b>
                    <br />{$item.O.fechaEnt}
                {/if}
            </div>
            {/if}
            </td>
             <td align="center">
             {if $item.O.name != ""}
             <br /><br />
             -------
             {/if}
             </td>
            <td align="center" class="timeLine">
            	<br /><br />
                {if $item.O.mes}
                	{$item.O.mes}
                {else}
                	{$item.S.mes}
                {/if}
            </td>
             <td align="center">
             {if $item.S.name != ""}  
             <br /><br />
             -------
             {/if}
             </td>
            <td align="center">
             
             {if $item.S.name != ""}           
             <div class="boxEntW st{$item.S.status} txtSt{$item.S.status}" id="rounded-box2">
                <b>Documento:</b>
                <br />
                {$item.S.name}
                <br /><br />
                <b>Proyecto:</b>
                <br />
                {$item.S.proyecto}
                <br /><br />
                {if $item.S.fechaRec}
                    <b>Fecha Rec. Roque&ntilde;i:</b> {$item.S.fechaRec}
                    <br />
                    <b>Fecha Env. Walmart:</b> {$item.S.fechaEnt}
                {else}                
                    <b>Fecha de Envio a Walmart:</b>
                    <br />
                    {if $item.S.fechaEnt == ""}
                    	Pendiente
                    {else}
                    	{$item.S.fechaEnt}
                    {/if}
                {/if}                
            </div>          
            {/if}
            
            </td>
        </tr>
        {/foreach}
        </table>
        </div>
        
       <br />
       
       {include file="{$DOC_ROOT}/templates/boxes/report-walmart-status.tpl"}
       
        <br /><br />
       <div class="lnkRegresar" align="center">
       <a href="{$WEB_ROOT}/contract">Regresar</a>
       </div>
      
    </div>
	    
 </div>
  <div class="clear"> </div>
 
</div>
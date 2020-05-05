<div align="center" style="width:500px; margin-left:310px">
   <div style="width:15px; height:15px; background-color:#009900; float:left"></div>
   <div style="float:left; padding-right:10px">&nbsp;{if $data.green}{$data.green}{else}Completas{/if}</div>
   <div style="width:15px; height:15px; background-color:#FC0; float:left"></div>
   <div style="float:left; padding-right:10px">&nbsp;{if $data.yellow}{$data.yellow}{else}Por completar{/if}</div>
   <div style="width:15px; height:15px; background-color:#F00; float:left"></div>
   <div style="float:left; padding-right:10px">&nbsp;{if $data.red}{$data.red}{else}Sin iniciar{/if}</div>
   {if $page eq 'report-servicio' || $page eq 'report-bonos'}
      {if $page eq 'report-servicio'}
         <div style="width:15px; height:15px; background-color:#768389; float:left"></div>
         <div style="float:left; padding-right:10px">Baja temporal</div>
      {/if}
      <div style="width:15px; height:15px; background-color:#EFEFEF; float:left"></div>
      <div style="float:left; padding-right:10px">&nbsp;Con facturas canceladas</div>
   {/if}

</div>
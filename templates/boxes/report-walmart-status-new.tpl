<div align="center" style="width:500px; margin-left:310px">
   {if array_key_exists('green',$data)}
   <div style="width:15px; height:15px; background-color:#009900; float:left"></div>
   <div style="float:left; padding-right:10px">&nbsp{if $data.green}{$data.green}{else}Completas{/if}</div>
   {/if}
   {if array_key_exists('yellow',$data)}
   <div style="width:15px; height:15px; background-color:#FC0; float:left"></div>
   <div style="float:left; padding-right:10px">&nbsp{if $data.yellow}{$data.yellow}{else}Por completar{/if}</div>
   {/if}
   {if array_key_exists('red',$data)}
   <div style="width:15px; height:15px; background-color:#F00; float:left"></div>
   <div style="float:left; padding-right:10px">&nbsp{if $data.red}{$data.red}{else}Sin iniciar{/if}</div>
   {/if}
   {if array_key_exists('gray',$data)}
      <div style="width:15px; height:15px; background-color:#EFEFEF; float:left"></div>
      <div style="float:left; padding-right:10px">&nbsp{if $data.gray}{$data.gray}{else}No aplica{/if}</div>
   {/if}
</div>
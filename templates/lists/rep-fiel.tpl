<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
<tr rowspan="10">
    <th colspan="3" class="divInside" align="center">
        ARCHIVOS VENCIDOS O PROXIMO A VENCER DE CLIENTES A CUENTA DE {$namePersonal|upper} Y SUBORDINADOS DEL AREA DE {$depto|upper}
    </th>
</tr>
<tr>
    <th align="center"  class="cabeceraTabla">#</th>
    <th align="center"  class="cabeceraTabla">CLIENTE</th>
    <th align="center" class="cabeceraTabla">RAZON SOCIAL</th>
</tr>
</thead>
<tbody>
{assign var='totalDeposito' value="0"}
{foreach from=$registros item=item key=key}
    <tr>
        <td align="center">{$key +1}</td>
        <td align="center">{$item.nameContact}</td>
        <td align="center">{$item.name}</td>
      {foreach from=$item.filesExpirate item=it key=ky}
       <td  align="center"
        class="{if $it.typeExpirate eq 'PorVencer'}
                      st{'PorCompletar'} txtSt{'PorCompletar'}
               {else}
                        st{'PorIniciar'} txtSt{'PorIniciar'}
               {/if}">{$it.descripcion}<br>{if $it.typeExpirate eq 'PorVencer'}Vence al {$it.date}{elseif $it.typeExpirate eq 'Vencido'}Vencido en {$it.date}{else}{$it.date}<br>Corregir fecha de expiracion{/if}
       </td>
      {/foreach}
    </tr>
    {foreachelse}
    <tr>
        <td colspan="5" align="center">Ning&uacute;n registro encontrado.</td>
    </tr>
{/foreach}
</tbody>
</table>
<select name="arrendatario" id="arrendatario" class="smallInput">
<option value="centros" {if $info.arrendatario == "centros"}selected{/if}>Arrendadora de Centros Comerciales S. de R.L. de C.V.</option>
{if $contCatId == 1 || $info.contCatId == 1}
<option value="walmart" {if $info.arrendatario == "walmart"}selected{/if}>Nueva Walmart de M&eacute;xico S. de R.L. de C.V.</option>
{/if}
</select>
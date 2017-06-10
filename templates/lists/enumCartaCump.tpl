<select class="smallInput" name="cartaCump" id="cartaCump" onchange="checkCartaCump()">
<option value="0" {if $info.cartaCump == 0}selected{/if}>No</option>
<option value="1" {if $info.cartaCump == 1}selected{/if}>Si</option>
</select>
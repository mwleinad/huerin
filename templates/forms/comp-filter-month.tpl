<select id="{$nameField}" name="{$nameField}" class="{$clase}">
    {if $all}
        <option value="" selected="selected">Todos</option>
    {/if}
    <option value="1" {if $month == "01"} selected="selected" {/if}>Enero</option>
    <option value="2" {if $month == "02"} selected="selected" {/if}>Febrero</option>
    <option value="3" {if $month == "03"} selected="selected" {/if}>Marzo</option>
    <option value="4" {if $month == "04"} selected="selected" {/if}>Abril</option>
    <option value="5" {if $month == "05"} selected="selected" {/if}>Mayo</option>
    <option value="6" {if $month == "06"} selected="selected" {/if}>Junio</option>
    <option value="7" {if $month == "07"} selected="selected" {/if}>Julio</option>
    <option value="8" {if $month == "08"} selected="selected" {/if}>Agosto</option>
    <option value="9" {if $month == "09"} selected="selected" {/if}>Septiembre</option>
    <option value="10" {if $month == "10"} selected="selected" {/if}>Octubre</option>
    <option value="11" {if $month == "11"} selected="selected" {/if}>Noviembre</option>
    <option value="12" {if $month == "12"} selected="selected" {/if}>Diciembre</option>
 </select>
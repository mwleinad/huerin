{foreach from=$clientes item=item key=key}
    <ul class="menu_arbol">
        <li><a href="javascript:;" onclick="showLevel('level1-{$item.nameContact}')">[+]</a>- {$item.nameContact}
           <ul style="display: none;" id="level1-{$item.nameContact}">
               {foreach from=$item.razones item=r  key=k}
                <li><a  href="javascript:;">[+]</a> - {$k}</li>
               {/foreach}
           </ul>

        </li>
    </ul>
{/foreach}
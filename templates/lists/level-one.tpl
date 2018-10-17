<ul>
    {foreach from=$clientes item=item key=key name=levelOne}
        <li>
            {$item.nameContact}
            <ul>
                {foreach from=$item.contracts item=lev2 key=klev2}
                    <li>{$lev2.name}
                        <ul>
                            {foreach from=$lev2.servicios item=serv key=kserv}
                                <li class="jstree-closed"
                                    data-datos='{
                                    "contractId":{$lev2.contractId},
                                    "servicioId":{$serv.servicioId},
                                    "type":"findInstancias",
                                    "initOp":"{$serv.inicioOperaciones}",
                                    "status":"{$serv.status}"
                                     }'>{$serv.nombreServicio}
                                </li>
                            {/foreach}
                        </ul>
                    </li>
                {/foreach}
            </ul>
        </li>
    {/foreach}
</ul>


{assign var=meses value=['1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre']}

<ul>
    {foreach from=$instancias item=item key=key}
        {if $item.class eq "CompletoTardio" || $item.class eq "Completo"}
            {assign var=icon value="fa fa-folder icon-state-success icon-lg"}
        {elseif $item.class eq "PorCompletar" || $item.class eq "Iniciado"}
            {assign var=icon value="fa fa-folder icon-state-warning  icon-lg"}
        {else}
            {assign var=icon value="fa fa-folder icon-state-danger  icon-lg"}
        {/if}
        {if !$item.instanciaServicioId}
            <li data-jstree='{ "icon":"fa fa-folder fa-large icon-state-default","state":{ "disabled":true } }'>{$meses[$key]|upper}</li>
        {else}
            <li data-jstree='{ "icon":"{$icon}" }'
                data-datos='{
                "instanciaId":{$item.instanciaServicioId},
                "tipoServicioId":{$item.tipoServicioId},
                "finstancia":"{$item.finstancia}",
                "type":"findPasos" }' class="jstree-closed">
                {$meses[$key]|upper}
            </li>
        {/if}
    {foreachelse}
        <li data-jstree='{ "icon":"fa fa-folder fa-large icon-state-default","state":{ "disabled":true } }'>Vacio</li>
    {/foreach}
</ul>


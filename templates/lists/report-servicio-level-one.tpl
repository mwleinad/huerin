{assign var=meses value=['1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Noviembre']}

{foreach from=$clientes item=item key=key}
    <ul class="menu_arbol">
        <li><a href="javascript:;" onclick="showLevel('level2-{$item.nameContact}')">[+]</a>- {$item.nameContact}
           <ul style="display: none;" id="level2-{$item.nameContact}">
               {foreach from=$item.razones item=razon  key=keyRazon}
                <li><a  href="javascript:;" onclick="showLevel('level3-{$razon.rfc}')">[+]</a> - {$razon.nombreRazon}
                    <ul style="display: none" id="level3-{$razon.rfc}">
                         {foreach from=$razon.servicios item=servicio key=keyServicio}
                             <li><a  href="javascript:;" onclick="showLevel('level4-{$servicio.tipoServicioId}-{$servicio.servicioId}')">[+]</a> -{$servicio.nombreServicio}
                                   <ul style="display:none" id="level4-{$servicio.tipoServicioId}-{$servicio.servicioId}">
                                       {foreach from=$servicio.instanciasXservicio item=it key=keyInstancia}
                                           {if $it ne ""}
                                            <li><a  href="javascript:;" onclick="showLevel('level5-{$it.instanciaServicioId}')">[+]</a>{$meses[$keyInstancia]}
                                                <ul style="display:none" id="level5-{$it.instanciaServicioId}">
                                                    <li class="{if $it.status neq 'inactiva'}
                                                                {if $it.class eq 'CompletoTardio'}
                                                                  st{'Completo'} txtSt{'Completo'}
                                                                {else}
                                                                  {if $it.class eq 'Iniciado'}
                                                                    st{'PorCompletar'} txtSt{'PorCompletar'}
                                                                  {else}
                                                                    st{$it.class} txtSt{$it.class}
                                                                  {/if}
                                                                {/if}
                                                              {/if}">
                                                        {$servicio.nombreServicio}-{$it.instanciaServicioId}</li>
                                                </ul>

                                            </li>
                                           {/if}
                                       {/foreach}
                                   </ul>
                             </li>
                          {/foreach}
                    </ul>
                </li>
               {/foreach}
           </ul>

        </li>
    </ul>
{/foreach}
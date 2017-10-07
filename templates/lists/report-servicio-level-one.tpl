{assign var=meses value=['1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Noviembre']}

{foreach from=$clientes item=item key=key name=levelOne}
    <ul  class="jstree-container-ul jstree-children">
        <li class="jstree-node jstree-closed {if $smarty.foreach.levelOne.last}jstree-last{/if}" id="before-level2-{$item.nameContact}"><i onclick="showLevel('level2','{$item.nameContact}')" class="jstree-icon jstree-ocl" role="presentation"></i>
            <a href="javascript:;" class="jstree-anchor" onclick="showLevel('level2','{$item.nameContact}')">
                <i class="jstree-icon jstree-themeicon fa fa-folder fa-2x icon-state-warning  jstree-themeicon-custom" role="presentation"></i>
                <span style="font-size:14px">{$item.nameContact}</span>
            </a>
           <ul id="level2-{$item.nameContact}"  class="jstree-children" role="group">
               {foreach from=$item.razones item=razon  key=keyRazon name=levelRazones}
                <li class="jstree-node jstree-closed {if $smarty.foreach.levelRazones.last}jstree-last{/if}" role="treeitem" id="before-level3-{$razon.rfc}"><i onclick="showLevel('level3','{$razon.rfc}')" class="jstree-icon jstree-ocl" role="presentation"></i>
                    <a  href="javascript:;" onclick="showLevel('level3','{$razon.rfc}')" class="jstree-anchor">
                        <i class="jstree-icon jstree-themeicon fa fa-folder fa-2x icon-state-warning  jstree-themeicon-custom" role="presentation"></i>
                        <span style="font-size:14px"> {$razon.nombreRazon}</span>
                    </a>
                    <ul id="level3-{$razon.rfc}" class="jstree-children" role="group">
                         {foreach from=$razon.servicios item=servicio key=keyServicio name=levelServicios}
                             <li class="jstree-node jstree-closed {if $smarty.foreach.levelServicios.last}jstree-last{/if}" role="treeitem" id="before-level4-{$servicio.tipoServicioId}-{$servicio.servicioId}"><i onclick="showLevel('level4','{$servicio.tipoServicioId}-{$servicio.servicioId}')" class="jstree-icon jstree-ocl" role="presentation"></i>
                                 <a  href="javascript:;" onclick="showLevel('level4','{$servicio.tipoServicioId}-{$servicio.servicioId}')" class="jstree-anchor">
                                     <i class="jstree-icon jstree-themeicon fa fa-folder fa-2x icon-state-warning  jstree-themeicon-custom" role="presentation"></i>
                                     <span style="font-size:14px">{$servicio.nombreServicio}</span>
                                 </a>
                                   <ul id="level4-{$servicio.tipoServicioId}-{$servicio.servicioId}" class="jstree-children" role="group">
                                       {foreach from=$servicio.instanciasXservicio item=it key=keyInstancia name=levelInsServ}
                                           {if $it ne ""}
                                            <li class="jstree-node jstree-closed" id="before-level5-{$it.instanciaServicioId}"><i onclick="showLevel('level5','{$it.instanciaServicioId}')" class="jstree-icon jstree-ocl" role="presentation"></i>
                                                <a  href="javascript:;" onclick="showLevel('level5','{$it.instanciaServicioId}')" class="jstree-anchor">
                                                    <i class="jstree-icon jstree-themeicon fa fa-folder fa-2x icon-state-warning  jstree-themeicon-custom" role="presentation"></i>
                                                    <span style="font-size:14px"> {$meses[$keyInstancia]|upper}</span>
                                                </a>
                                                <ul id="level5-{$it.instanciaServicioId}" class="jstree-children" role="group">
                                                    <li class="jstree-node jstree-closed jstree-last" id="before-level6-{$it.instanciaServicioId}"><i onclick="ShowSixLevel({$it.instanciaServicioId})" class="jstree-icon jstree-ocl" role="presentation"></i>
                                                        <a  href="javascript:;" onclick="ShowSixLevel({$it.instanciaServicioId})" class="jstree-anchor">
                                                            <i class="jstree-icon jstree-themeicon fa fa-folder fa-2x icon-state-warning  jstree-themeicon-custom" role="presentation"></i>
                                                            <span style="font-size:14px" class="{if $it.status neq 'inactiva'}
                                                                {if $it.class eq 'CompletoTardio'}
                                                                  st{'Completo'} txtSt{'Completo'}
                                                                {else}
                                                                  {if $it.class eq 'Iniciado'}
                                                                    st{'PorCompletar'} txtSt{'PorCompletar'}
                                                                  {else}
                                                                    st{$it.class} txtSt{$it.class}
                                                                  {/if}
                                                                {/if}
                                                              {/if}">{$servicio.nombreServicio}-{$it.instanciaServicioId}</span>
                                                        </a>
                                                        <ul id="ul-six-level-{$it.instanciaServicioId}"  class="jstree-children" role="group">
                                                            
                                                        </ul>
                                                    </li>
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
<div align="center"  id="divForm">

    <form name="frmSearch" id="frmSearch" action="" method="post">
        <input type="hidden" name="type" id="type" value="search" />
        <input type="hidden" name="correo" id="correo" value="" />
        <input type="hidden" name="texto" id="texto" value="" />
        <input type="hidden" name="rfc" id="rfc" value="{$nameContact}" />
        <input type="hidden" name="deep" id="deep" value="0" />
        <input type="hidden" name="responsableCuenta" id="responsableCuenta" value="0" />
        <input type="hidden" name="departamentoId" id="departamentoId" value="0" />
        <input type="hidden" name="atrasados" id="atrasados" value="0" />

        <table width="800" align="center">
            <tr style="background-color:#CCC">
                <td colspan="6" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
            </tr>
            <tr>
{*
                <td align="center">Cliente:</td>
*}
{*
                <td align="center">Responsable:</td>
                <td align="center">Incluir Subordinados:</td>
                <td align="center">Solo Atrasados:</td>
*}
{*
                <td align="center">Departamento:</td>
*}
                <td align="center">A&ntilde;o:</td>
{*
                <td align="center"></td>
*}
            </tr>
            <tr>

{*
                <td align="center">
                    <input type="text" size="35" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$search.rfc}" />
                    <div id="loadingDivDatosFactura"></div>
                    <div style="position:relative">
                        <div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
                        </div>
                    </div>
                </td>
*}
{*                <td align="center">
                    <select name="responsableCuenta" id="responsableCuenta"  class="smallInput">
                        *}{* if $User.roleId=="1" *}{*
                        <option value="0" selected="selected">Todos...</option>
                        *}{* /if *}{*
                        {foreach from=$personals item=personal}
                            <option value="{$personal.personalId}" {if $search.responsableCuenta == $personal.personalId} selected="selected" {/if} >{$personal.name}</option>
                        {/foreach}
                    </select>
                </td>*}
{*
                <td align="center">
                    <input name="deep" id="deep" type="checkbox"/>
                </td>
*}
{*
                <td align="center">
                    <input name="atrasados" id="atrasados" type="checkbox"/>
                </td>
*}

{*
                <td align="center">
                    <select name="departamentoId" id="departamentoId"  class="smallInput">
                        <option value="" selected="selected">Todos...</option>
                        {foreach from=$departamentos item=depto}
                            <option value="{$depto.departamentoId}" >{$depto.departamento}</option>
                        {/foreach}
                    </select>
                </td>
*}


                <td align="center">
                    <select name="year" id="year"  class="smallInput" style="min-width:60px">
                        <option value="2012" {if $year == "2012"} selected="selected" {/if}>2012</option>
                        <option value="2013" {if $year == "2013"} selected="selected" {/if}>2013</option>
                        <option value="2014" {if $year == "2014"} selected="selected" {/if}>2014</option>
                        <option value="2015" {if $year == "2015"} selected="selected" {/if}>2015</option>
                        <option value="2016" {if $year == "2016"} selected="selected" {/if}>2016</option>
                        <option value="2017" {if $year == "2017"} selected="selected" {/if}>2017</option>
                        <option value="2018" {if $year == "2018"} selected="selected" {/if}>2018</option>
                        <option value="2019" {if $year == "2019"} selected="selected" {/if}>2010</option>
                        <option value="2020" {if $year == "2020"} selected="selected" {/if}>2020</option>
                    </select>

                </td>
            </tr>
            <tr>
                <td colspan="6" align="center">
                    <div style="margin-left:380px">
                        <a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>
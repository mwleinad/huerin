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
        <table style="width:30%" align="center">
            <tr style="background-color:#CCC">
                <td colspan="6" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
            </tr>
            <tr>
                <td align="center">A&ntilde;o:</td>
            </tr>
            <tr>
            <td align="center">
            <select name="year" id="year"  {if $class neq ''}class="{$class}"{else}class="largeInput"{/if} style="width: 80px; min-width: 80px">
                {for $init=2012 to $year}
                    <option value="{$init}" {if $init == $year} selected="selected" {/if}>{$init}</option>
                {/for}
            </select>
            </td>
            </tr>
            <tr>
                <td colspan="6"  align="center">
                    <div style="display: inline-block">
                        <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
                        <a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>
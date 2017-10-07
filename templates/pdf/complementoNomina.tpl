{if $xmlData.nomina}
<p class="bold">Complemento de nomina</p>
    <table width="100%" class="outline-table">
        <tbody>
        <tr class="border-bottom border-right center font-smallest">
            <td class="border-top" width="10%"><strong>Tipo Nomina</strong></td>
            <td class="border-top" width="10%"><strong>Fecha Pago</strong></td>
            <td class="border-top" width="15%"><strong>Fecha inicial pago</strong></td>
            <td class="border-top" width="15%"><strong>Fecha final pago</strong></td>
            <td class="border-top" width="10%"><strong># Dias pagados</strong></td>
            <td class="border-top" width="15%"><strong>Total percepciones</strong></td>
            <td class="border-top" width="15%"><strong>Total deducciones</strong></td>
            <td class="border-top" width="10%"><strong>Total otros pagos</strong></td>
        </tr>
        <tr class="border-right border-bottom">
            <td class="left">{$xmlData.nomina.data.TipoNomina}</td>
            <td class="left">{$xmlData.nomina.data.FechaPago}</td>
            <td class="left">{$xmlData.nomina.data.FechaInicialPago}</td>
            <td class="left">{$xmlData.nomina.data.FechaFinalPago}</td>
            <td class="left">{$xmlData.nomina.data.NumDiasPagados}</td>
            <td class="left">{$xmlData.nomina.data.TotalPercepciones}</td>
            <td class="left">{$xmlData.nomina.data.TotalDeducciones}</td>
            <td class="left">{$xmlData.nomina.data.TotalOtrosPagos}</td>
        </tr>
        <tr class="border-right border-bottom">
            <td colspan="2" class="left">Registro patronal</td>
            <td colspan="2" class="left">{$xmlData.nomina.emisor.RegistroPatronal}</td>
            <td class="left">CURP</td>
            <td colspan="3" class="left">{$xmlData.nomina.emisor.Curp}</td>
        </tr>
        <tr class="border-right border-bottom">
            <td colspan="4" class="left">
                Receptor CURP: {$xmlData.nomina.receptor.Curp}<br>
                # Seguridad Social: {$xmlData.nomina.receptor.NumSeguridadSocial}<br>
                Fecha inicio relacion laboral: {$xmlData.nomina.receptor.FechaInicioRelLaboral}<br>
                Antiguedad: {$xmlData.nomina.receptor.Antiguedad}<br>
                Tipo contrato: {$xmlData.nomina.receptor.TipoContrato}<br>
                Tipo regimen: {$xmlData.nomina.receptor.TipoRegimen}<br>
                # Empleado: {$xmlData.nomina.receptor.NumEmpleado}<br>
            </td>
            <td colspan="4" class="left">
                Departamento: {$xmlData.nomina.receptor.Departamento}<br>
                Puesto: {$xmlData.nomina.receptor.Puesto}<br>
                Riesgo puesto: {$xmlData.nomina.receptor.RiesgoPuesto}<br>
                Periodicidad pago: {$xmlData.nomina.receptor.PeriodicidadPago}<br>
                Salario base: {$xmlData.nomina.receptor.SalarioBaseCotApor}<br>
                Salario diario integrado: {$xmlData.nomina.receptor.SalarioDiarioIntegrado}<br>
                Clave entidad federativa: {$xmlData.nomina.receptor.ClaveEntFed}<br>
            </td>
        </tr>
        {if $xmlData.nomina.percepciones.percepcion|count > 0}
        <tr class="border-right border-bottom">
            <td colspan="8" class="left">
                Percepciones
            </td>
        </tr>
        <tr class="border-right border-bottom">
            <td colspan="8" class="left">
                <table width="100%" class="outline-table">
                    <tbody>
                    <tr class="border-bottom border-right center font-smallest">
                        <td class="border-top" width="15%"><strong>Total Sueldos</strong></td>
                        <td class="border-top" width="15%"><strong>Total Separacion Indemnizacion</strong></td>
                        <td class="border-top" width="40%"><strong>Total Jubilacion Pension Retiro</strong></td>
                        <td class="border-top" width="15%"><strong>Total Gravado</strong></td>
                        <td class="border-top" width="15%"><strong>Total Exento</strong></td>
                    </tr>
                    <tr class="border-right border-bottom">
                        <td class="left">{$xmlData.nomina.percepciones.data.TotalSueldos|number}</td>
                        <td class="left">{$xmlData.nomina.percepciones.data.TotalSeparacionIndemnizacion|number}</td>
                        <td class="left">{$xmlData.nomina.percepciones.data.TotalJubilacionPensionRetiro|number}</td>
                        <td class="left">{$xmlData.nomina.percepciones.data.TotalGravado|number}</td>
                        <td class="left">{$xmlData.nomina.percepciones.data.TotalExento|number}</td>
                    </tr>
                    <tr class="border-bottom border-right center font-smallest">
                        <td class="border-top" width="15%"><strong>Tipo Percepcion</strong></td>
                        <td class="border-top" width="15%"><strong>Clave</strong></td>
                        <td class="border-top" width="40%"><strong>Concepto</strong></td>
                        <td class="border-top" width="15%"><strong>Importe Gravado</strong></td>
                        <td class="border-top" width="15%"><strong>Importe Gravado</strong></td>
                    </tr>
                    {foreach from=$xmlData.nomina.percepciones.percepcion item=percepcion}
                        <tr class="border-right border-bottom">
                            <td class="left">{$percepcion.TipoPercepcion}</td>
                            <td class="left">{$percepcion.Clave}</td>
                            <td class="left">{$percepcion.Concepto}</td>
                            <td class="left">{$percepcion.ImporteGravado|number}</td>
                            <td class="left">{$percepcion.ImporteExento|number}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </td>
        </tr>
        {/if}
        {if $xmlData.nomina.deducciones.deduccion|count > 0}
            <tr class="border-right border-bottom">
                <td colspan="8" class="left">
                    Deducciones
                </td>
            </tr>
            <tr class="border-right border-bottom">
                <td colspan="8" class="left">
                    <table width="100%" class="outline-table">
                        <tbody>
                        <tr class="border-bottom border-right center font-smallest">
                            <td colspan="2" class="border-top" width="50%"><strong>Total Otras Deducciones</strong></td>
                            <td colspan="2" class="border-top" width="50%"><strong>Total Impuestos Retenidos</strong></td>
                        </tr>
                        <tr class="border-right border-bottom">
                            <td colspan="2" class="left">{$xmlData.nomina.deducciones.data.TotalOtrasDeducciones|number}</td>
                            <td colspan="2" class="left">{$xmlData.nomina.deducciones.data.TotalImpuestosRetenidos|number}</td>
                        </tr>
                        <tr class="border-bottom border-right center font-smallest">
                            <td class="border-top" width="20%"><strong>Tipo Deduccion</strong></td>
                            <td class="border-top" width="20%"><strong>Clave</strong></td>
                            <td class="border-top" width="40%"><strong>Concepto</strong></td>
                            <td class="border-top" width="20%"><strong>Importe</strong></td>
                        </tr>
                        {foreach from=$xmlData.nomina.deducciones.deduccion item=deduccion}
                            <tr class="border-right border-bottom">
                                <td class="left">{$deduccion.TipoDeduccion}</td>
                                <td class="left">{$deduccion.Clave}</td>
                                <td class="left">{$deduccion.Concepto}</td>
                                <td class="left">{$deduccion.Importe|number}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </td>
            </tr>
        {/if}
        {if $xmlData.nomina.otrosPagos.otroPago|count > 0}
            <tr class="border-right border-bottom">
                <td colspan="8" class="left">
                    Otros pagos
                </td>
            </tr>
            <tr class="border-right border-bottom">
                <td colspan="8" class="left">
                    <table width="100%" class="outline-table">
                        <tbody>
                        <tr class="border-bottom border-right center font-smallest">
                            <td class="border-top" width="20%"><strong>Tipo Otro Pago</strong></td>
                            <td class="border-top" width="20%"><strong>Clave</strong></td>
                            <td class="border-top" width="40%"><strong>Concepto</strong></td>
                            <td class="border-top" width="20%"><strong>Importe</strong></td>
                        </tr>
                        {foreach from=$xmlData.nomina.otrosPagos.otroPago item=otroPago}
                            <tr class="border-right border-bottom">
                                <td class="left">{$otroPago.TipoOtroPago}</td>
                                <td class="left">{$otroPago.Clave}</td>
                                <td class="left">{$otroPago.Concepto}</td>
                                <td class="left">{$otroPago.Importe|number}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </td>
            </tr>
        {/if}
        {if $xmlData.nomina.incapacidades.incapacidad|count > 0}
            <tr class="border-right border-bottom">
                <td colspan="8" class="left">
                    Incapacidades
                </td>
            </tr>
            <tr class="border-right border-bottom">
                <td colspan="8" class="left">
                    <table width="100%" class="outline-table">
                        <tbody>
                        <tr class="border-bottom border-right center font-smallest">
                            <td class="border-top" width="30%"><strong>Dias Incapacidad</strong></td>
                            <td class="border-top" width="30%"><strong>Tipo Incapacidad</strong></td>
                            <td class="border-top" width="40%"><strong>Importe Monetario</strong></td>
                        </tr>
                        {foreach from=$xmlData.nomina.incapacidades.incapacidad item=incapacidad}
                            <tr class="border-right border-bottom">
                                <td class="left">{$incapacidad.DiasIncapacidad}</td>
                                <td class="left">{$incapacidad.TipoIncapacidad}</td>
                                <td class="left">{$incapacidad.ImporteMonetario|number}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </td>
            </tr>
        {/if}


        </tbody>
    </table>
    <p class="small-height">&nbsp;</p>
{/if}
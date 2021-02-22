<div id="tabs">
     <div class="container">
      {if $User.roleId!=4}
       <ul>
        	{if $mainMnu == "archivos"}
          	    {foreach from=$resDepartamentos item=item}
                    {if (in_array($item.permId,$permissions))|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/archivos/id/{$item.departamentoId}" {if $page == "archivos" && $item.departamentoId == $id}class="current"{/if}>
                    <span>{$item.departamento}</span></a></li>
                    {/if}
               {/foreach}
               {if (in_array(148,$permissions) || $User.isRoot) && $isSameDepartament}
                <li><a href="#" onclick="NuevoArchivo({$id})">
                <span>Nuevo Archivo</span></a></li>
                {/if}
           {/if}
     	</ul>
        <ul>
        	{if $mainMnu == "catalogos"}
                {if in_array(8,$permissions) || $User.isRoot}
                <li><a href="{$WEB_ROOT}/personal" {if $page == "personal"}class="current"{/if} target="_blank">
                <span>Empleados</span></a></li>
                {/if}
                {if in_array(127,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/rol" {if $page == "rol"}class="current"{/if} target="_blank">
                     <span>Roles de usuario</span></a></li>
                {/if}
                {if in_array(14,$permissions)|| $User.isRoot}
                <li><a href="{$WEB_ROOT}/regimen" {if $page == "regimen" || $page == "contract-subcategory"}class="current"{/if} target="_blank">
                <span>Regimenes</span></a></li>
                {/if}
                {if in_array(272,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/activity" {if $page == "activity"}class="current"{/if} target="_blank">
                    <span>Actividades comerciales</span></a></li>
                {/if}
                {if in_array(19,$permissions)|| $User.isRoot}
                <li><a href="{$WEB_ROOT}/sociedad" {if $page == "sociedad"}class="current"{/if} target="_blank">
                <span>Tipos de Sociedad</span></a></li>
                {/if}
                {if in_array(24,$permissions)|| $User.isRoot}
                <li><a href="{$WEB_ROOT}/tipoServicio" {if $page == "tipoServicio"}class="current"{/if} target="_blank">
                <span>Tipo de Servicio</span></a></li>
                {/if}
                {if in_array(37,$permissions)|| $User.isRoot}
                <li><a href="{$WEB_ROOT}/tipoDocumento" {if $page == "tipoDocumento"}class="current"{/if} target="_blank">
                <span>Tipo de Documento</span></a></li>
                {/if}
                {if in_array(42,$permissions)|| $User.isRoot}
                <li><a href="{$WEB_ROOT}/tipoRequerimiento" {if $page == "tipoRequerimiento"}class="current"{/if} target="_blank">
                <span>Tipo de Requerimiento</span></a></li>
                {/if}
                {if in_array(47,$permissions)|| $User.isRoot}
                <li><a href="{$WEB_ROOT}/tipoArchivo" {if $page == "tipoArchivo"}class="current"{/if} target="_blank">
                <span>Tipo de Archivo</span></a></li>
                {/if}
                {if in_array(52,$permissions)|| $User.isRoot}
				<li><a href="{$WEB_ROOT}/departamentos" {if $page == "departamentos"}class="current"{/if} target="_blank">
                <span>Departamentos</span></a></li>
                {/if}
                {if in_array(182,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/expediente" {if $page == "expediente"}class="current"{/if} target="_blank">
                    <span>Expedientes</span></a></li>
                {/if}
                {if in_array(251,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/resource-office" {if $page == "resource-office"}class="current"{/if} target="_blank">
                    <span>Inventario de recursos</span></a></li>
                {/if}
            {/if}

            {if $mainMnu == "servicios"}
                {if in_array(94,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-servicio" {if $page == "report-servicio"}class="current"{/if} target="_blank">
                    <span>Servicio Anual</span></a></li>
                {/if}
                {if in_array(95,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-servicio-mensual" {if $page == "report-servicio-mensual"}class="current"{/if} target="_blank">
                    <span>Servicio Mensual</span></a></li>
                {/if}
                {if in_array(96,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-servicio-auditoria" {if $page == "report-servicio-auditoria"}class="current"{/if} target="_blank">
                    <span>Servicio Auditoria</span></a></li>
                {/if}
                {if in_array(97,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-servicio-drill" {if $page == "report-servicio-drill"}class="current"{/if} target="_blank">
                    <span>Administrador de archivos</span></a></li>
                {/if}
            {/if}
            {if $mainMnu == "contratos"}
                {if in_array(91,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/customer/tipo/Activos" {if ($page == "customer" && $tipo == "Activos")}class="current"{/if} target="_blank">
                    <span>Listado Activos</span></a></li>
                {/if}
                {if in_array(92,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/customer/tipo/Inactivos" {if ($page == "customer" && $tipo == "Inactivos")}class="current"{/if} target="_blank">
                    <span>Listado Inactivos</span></a></li>
				{/if}
                {if in_array(270,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/customer/tipo/Temporal" {if ($page == "customer" && $tipo == "Temporal")}class="current"{/if} target="_blank">
                    <span>Listado Temporal</span></a></li>
                {/if}
                {if in_array(271,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/prospect" {if $page == "prospect" || $page == "company"}class="current"{/if} target="_blank">
                    <span>Prospectos</span></a></li>
                {/if}
                {if in_array(181,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/exp-imp-data" {if ($page == "exp-imp-data")}class="current"{/if} target="_blank">
                    <span>Importar desde archivo</span></a></li>
                {/if}
            {/if}

            {if $mainMnu == "reportes"}
                {if in_array(153,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-invoice" {if $page == "report-invoice"}class="current"{/if} target="_blank">
                    <span>Facturas</span></a></li>
                {/if}
                {if in_array(155,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-cxc" {if $page == "report-cxc"}class="current"{/if} target="_blank">
                    <span>Reporte CxC</span></a></li>
                {/if}
                {if in_array(157,$permissions)|| $User.isRoot}
                <li><a href="{$WEB_ROOT}/report-documentacion-permanente" {if $page == "report-documentacion-permanente"}class="current"{/if} target="_blank">
                    <span>Documentos Permanentes</span></a></li>
                {/if}
                {if in_array(159,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-archivos-permanente" {if $page == "report-archivos-permanente"}class="current"{/if} target="_blank">
                    <span>Archivos Permanentes</span></a></li>
                {/if}
                {if in_array(161,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-ingresos" {if $page == "report-ingresos"}class="current"{/if} target="_blank">
                    <span>Ingresos</span></a></li>
                {/if}
				{*if in_array(163,$permissions)|| $User.isRoot}
                     <li><a href="{$WEB_ROOT}/report-servicio-bono" {if $page == "report-servicio-bono"}class="current"{/if}>
                     <span>Bonos contabilidad</span></a></li>
                {/if*}
                {if in_array(163,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-bonos" {if $page == "report-bonos"}class="current"{/if} target="_blank">
                      <span>Bonos trimestral</span></a></li>
                {/if}
                {if in_array(215,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-cobranza" {if $page == "report-cobranza"}class="current"{/if} target="_blank">
                      <span>Bonos juridico</span></a></li>
                {/if}
                {if in_array(225,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/edo-result" {if $includedTpl == "edo-result"} class="current"{/if} target="_blank"><span>Estado de resultado</span></a></li>
                {/if}
                {if in_array(165,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-cobranza-new" {if $page == "report-cobranza-new"}class="current"{/if} target="_blank">
                    <span>Cobranza anual</span></a></li>
                {/if}
                {if in_array(276,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-account-manager" {if $page == "report-account-manager"}class="current"{/if} target="_blank">
                            <span>Cuentas por gerente</span></a></li>
                {/if}
                {if in_array(208,$permissions)|| $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-cobranza-mensual" {if $page == "report-cobranza-mensual"}class="current"{/if} target="_blank">
                    <span>Cobranza mensual</span></a></li>
                {/if}
                {if in_array(167,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-razon-social" title="Exportar razones sociales" {if $page == "report-razon-social"}class="current"{/if} target="_blank">
                    <span>Razones Sociales</span></a></li>
                {/if}
                {if in_array(168,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/bitacora" {if $page == "bitacora"}class="current"{/if} target="_blank">
                    <span>Bitacoras</span></a></li>
                {/if}
                {if in_array(178,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/tree-subordinate" {if $page == "tree-subordinate"}class="current"{/if} title="Reporte de Organigrama" target="_blank">
                    <span>Reporte Organigrama</span></a></li>
                {/if}
                {if in_array(249,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-exp-employe" {if $page == "report-exp-employe"}class="current"{/if} title="Reporte de Expedientes de Colaboradores" target="_blank">
                     <span>Reporte de Expedientes</span></a></li>
                {/if}
                {if in_array(277,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/chart" {if $page == "chart"}class="current"{/if} title="Graficas" target="_blank">
                    <span>Graficas</span></a></li>
                {/if}
                {if in_array(281,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-ab-all" {if $page == "report-ab-all"}class="current"{/if} title="Reportes de altas y bajas de servicios" target="_blank">
                            <span>Reporte de altas y bajas</span></a></li>
                {/if}
                {if in_array(282,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-company-activity" {if $page == "report-company-activity"}class="current"{/if} title="Reporte de empresas-actividades" target="_blank">
                            <span>Reporte de empresa-actividades</span></a></li>
                {/if}

            {/if}

            {if $mainMnu == "cxc"}
                {if in_array(120,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/cxc" {if $page == "cxc"}class="current"{/if} target="_blank">
                    <span>Cuentas por Cobrar</span></a></li>
                {/if}
                {if in_array(121,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/balance" {if $page == "balance"}class="current"{/if} target="_blank">
                    <span>Estado de cuenta saldos pendientes</span></a></li>
                {/if}
            {/if}
            {if $page == "sistema" || $page == "reporte-sat" || $page == "cfdi33-generate" || $page == "comp-from-xml"}
            <ul>
              {if in_array(131,$permissions) || $User.isRoot}
                <li><a href="{$WEB_ROOT}/cfdi33-generate" {if $includedTpl == "cfdi33-generate"} class="current"{/if} target="_blank"><span>Nuevo CFDi 3.3</span></a></li>
              {/if}
              {if in_array(132,$permissions) || $User.isRoot}
                <li><a href="{$WEB_ROOT}/sistema/consultar-facturas" {if $includedTpl == "sistema_consultar-facturas"} class="current"{/if} target="_blank"><span>Consultar Comprobantes</span></a></li>
              {/if}
              {if in_array(120,$permissions) || $User.isRoot}
              <li><a href="{$WEB_ROOT}/comp-from-xml" {if $page == "comp-from-xml"}class="current"{/if} target="_blank">
                    <span>Complemento de pago desde xml</span></a></li>
              {/if}
            </ul>
            {/if}
            {if $mainMnu == "cfdi"}
                {if in_array(8888,$permissions) || $User.isRoot}
                <li><a href="{$WEB_ROOT}/cfdi" {if $page == "cfdi"}class="current"{/if} target="_blank">
                <span>Cuentas del Sistema</span></a></li>

                <li><a href="{$WEB_ROOT}/cfdiPagos" {if $page == "cfdiPagos"}class="current"{/if} target="_blank">
                <span>Pagos y Activaciones</span></a></li>
                {/if}
            {/if}
            {if $mainMnu == "coffe"}
                {if in_array(214,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/coffe" {if $page == "coffe"}class="current"{/if} target="_blank">
                    <span>Menus del dia</span></a></li>
                {/if}
            {/if}
            {if $mainMnu == "configuracion"}
                {if $info.version != "auto"}
                    {if in_array(140,$permissions) || $User.isRoot}
                        <li><a href="{$WEB_ROOT}/admin-folios/emisores" {if $includedTpl == "admin-folios_emisores" || $includedTpl == "admin-folios_nuevos-folios"} class="current"{/if} target="_blank"><span>Emisores</span></a></li>
                    {/if}
                {/if}
                {*if in_array(139,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/admin-folios/nuevos-folios" {if $includedTpl == "admin-folios_nuevos-folios"} class="current"{/if}><span>Lista de Folios</span></a></li>
                {/if*}
                {if in_array(218,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/backup_system" {if $includedTpl == "backup_system"} class="current"{/if} target="_blank"><span>Bases de datos</span></a></li>
                {/if}
                {if in_array(220,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-up-down" {if $page == "report-up-down"}class="current"{/if} target="_blank">
                            <span>Reporte altas y bajas</span></a></li>
                {/if}
                {if in_array(221,$permissions) || $User.isRoot}
                    <li><a href="{$WEB_ROOT}/report-pending" {if $includedTpl == "report-pending"} class="current"{/if} target="_blank"><span>Cambios en plataforma</span></a></li>
                {/if}
                {if $User.isRoot}
                    <li><a href="{$WEB_ROOT}/utileria" {if $includedTpl == "utileria"} class="current"{/if} target="_blank"><span>Herramientas y utilerias</span></a></li>
                {/if}
            {/if}
       </ul>
      {/if}
    </div>
</div>

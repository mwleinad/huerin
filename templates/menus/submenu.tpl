<div id="tabs">
     <div class="container">
       <ul>
        	{if $mainMnu == "archivos"}
          	{foreach from=$resDepartamentos item=item}

                <li><a href="{$WEB_ROOT}/archivos/id/{$item.departamentoId}" {if $page == "archivos" && $item.departamentoId == $id}class="current"{/if}>
                <span>{$item.departamento}</span></a></li>
               {/foreach}
                <li><a href="#" onclick="NuevoArchivo({$id})">
                <span>Nuevo Archivo</span></a></li>
           {/if}     
     	</ul>     
        <ul>
        	{if $mainMnu == "catalogos"}

                <li><a href="{$WEB_ROOT}/personal" {if $page == "personal"}class="current"{/if}>
                <span>Empleados</span></a></li>

                <li><a href="{$WEB_ROOT}/regimen" {if $page == "regimen" || $page == "contract-subcategory"}class="current"{/if}>
                <span>Regimenes</span></a></li>

                <li><a href="{$WEB_ROOT}/sociedad" {if $page == "sociedad"}class="current"{/if}>
                <span>Tipos de Sociedad</span></a></li>

                <li><a href="{$WEB_ROOT}/tipoServicio" {if $page == "tipoServicio"}class="current"{/if}>
                <span>Tipo de Servicio</span></a></li>

                <li><a href="{$WEB_ROOT}/tipoDocumento" {if $page == "tipoDocumento"}class="current"{/if}>
                <span>Tipo de Documento</span></a></li>

                <li><a href="{$WEB_ROOT}/tipoRequerimiento" {if $page == "tipoRequerimiento"}class="current"{/if}>
                <span>Tipo de Requerimiento</span></a></li>

                <li><a href="{$WEB_ROOT}/tipoArchivo" {if $page == "tipoArchivo"}class="current"{/if}>
                <span>Tipo de Archivo</span></a></li>

               {*<li><a href="{$WEB_ROOT}/impuesto" {if $page == "impuesto"}class="current"{/if}>
                <span>Impuestos</span></a></li>

                <li><a href="{$WEB_ROOT}/obligacion" {if $page == "obligacion"}class="current"{/if}>
                <span>Obligaciones</span></a></li>*}

				<li><a href="{$WEB_ROOT}/departamentos" {if $page == "departamentos"}class="current"{/if}>
                <span>Departamentos</span></a></li>

				<li><a href="{$WEB_ROOT}/mantenimiento" {if $page == "mantenimiento"}class="current"{/if}>
                <span>Mantenimiento</span></a></li>


            {/if}

            {if $mainMnu == "servicios"}
            {if $User.roleId < 4}

{*}               <li><a href="{$WEB_ROOT}/servicios" {if $page == "servicios"}class="current"{/if}>
                <span>Servicios</span></a></li>{*}
                <li><a href="{$WEB_ROOT}/report-servicio" {if $page == "report-servicio"}class="current"{/if}>
                <span>Servicio Anual</span></a></li>

								<li><a href="{$WEB_ROOT}/report-servicio-mensual" {if $page == "report-servicio-mensual"}class="current"{/if}>
                <span>Servicio Mensual</span></a></li>
								<li><a href="{$WEB_ROOT}/report-servicio-auditoria" {if $page == "report-servicio-auditoria"}class="current"{/if}>
                <span>Servicio Auditoria</span></a></li>
                <li><a href="{$WEB_ROOT}/report-servicio-drill" {if $page == "report-servicio-drill"}class="current"{/if}>
                        <span>Administrador de archivos</span></a></li>
            {/if}
            {/if}
            {if $mainMnu == "contratos"}

            {if $User.roleId < 4}
                <li><a href="{$WEB_ROOT}/customer/tipo/Activos" {if $page == "customer" && $tipo == "Activos"}class="current"{/if}>
                <span>Listado Activos</span></a></li>
                {if $User.roleId == 1 || $infoUser.tipoPersonal == "Gerente" || $User.userId == "149"  || ($User.tipoPers == "Supervisor" && $User.departamentoId == 25)}
				<li><a href="{$WEB_ROOT}/customer/tipo/Inactivos" {if $page == "customer" && $tipo == "Inactivos"}class="current"{/if}>
                <span>Listado Inactivos</span></a></li>
				{/if}
            {/if}
            {/if}

            {if $mainMnu == "reportes"}

                {if $User.roleId < 4}
{*}             <li><a href="{$WEB_ROOT}/report-obligaciones" {if $page == "report-obligaciones"}class="current"{/if}>
                <span>Reporte de Subordinados</span></a></li>

                 <li><a href="{$WEB_ROOT}/report-basica" {if $page == "report-basica"}class="current"{/if}>
                <span>Rep. Propio.</span></a></li>{*}


				{if $User.roleId == 1}
                <li><a href="{$WEB_ROOT}/report-invoice" {if $page == "report-invoice"}class="current"{/if}>
                <span>Facturas</span></a></li>
				{/if}


				{if $User.roleId == 1}
				<li><a href="{$WEB_ROOT}/report-cxc" {if $page == "report-cxc"}class="current"{/if}>
                <span>Reporte CxC</span></a></li>
				{/if}
				<li><a href="{$WEB_ROOT}/report-documentacion-permanente" {if $page == "report-documentacion-permanente"}class="current"{/if}>
                <span>Documentos Permanentes</span></a></li>

				<li><a href="{$WEB_ROOT}/report-archivos-permanente" {if $page == "report-archivos-permanente"}class="current"{/if}>
                <span>Archivos Permanentes</span></a></li>

				{/if}


                {if $User.tipoPers == "Socio" || $User.tipoPers == "Admin"}
                <li><a href="{$WEB_ROOT}/report-ingresos" {if $page == "report-ingresos"}class="current"{/if}>
                <span>Ingresos</span></a></li>
                {/if}
                <!--{if $User.tipoPers == "Socio" || $User.tipoPers == "Admin"}
                <li><a href="{$WEB_ROOT}/report-bonos" {if $page == "report-bonos"}class="current"{/if}>
                <span>Rep. Bonos</span></a></li>
                {/if}-->
				{if $User.tipoPers == "Socio" || $User.tipoPers == "Admin" || $User.tipoPers == "Gerente" || $User.userId == 71 || ($User.tipoPers == "Supervisor" && $User.departamentoId == 25)}
                <li><a href="{$WEB_ROOT}/report-servicio-bono" {if $page == "report-servicio-bono"}class="current"{/if}>
                <span>Reporte de Bonos</span></a></li>
                {/if}


                {if $User.tipoPers == "Socio" || $User.tipoPers == "Admin" || $User.tipoPers == "Asistente"}
                <li><a href="{$WEB_ROOT}/report-cobranza-new" {if $page == "report-cobranza-new"}class="current"{/if}>
                <span>Reporte Cobranza</span></a></li>
                {/if}

                <li><a href="{$WEB_ROOT}/export/rsocial.php" title="Exportar a Excel">
                <span>Razones Sociales</span></a></li>

                {if $User.roleId < 4}
                <li><a href="{$WEB_ROOT}/bitacora" {if $page == "bitacora"}class="current"{/if}>
                            <span>Bitacoras</span></a></li>
                {/if}


            {/if}

            {if $mainMnu == "cxc"}
                {if $User.roleId < 4}

                <li><a href="{$WEB_ROOT}/cxc" {if $page == "cxc"}class="current"{/if}>
                <span>Cuentas por Cobrar</span></a></li>

                 <li><a href="{$WEB_ROOT}/balance" {if $page == "balance"}class="current"{/if}>
                <span>Estado de cuenta saldos pendientes</span></a></li>

                {/if}

            {/if}


            {if $page == "sistema" || $page == "reporte-sat" || $page == "admin-folios" || $page == "cfdi33-generate"}
            <ul>
              {*<li><a href="{$WEB_ROOT}/sistema/nueva-factura" {if $includedTpl == "sistema_nueva-factura"} class="current"{/if}><span>Nuevo CFDi</span></a></li>*}
                <li><a href="{$WEB_ROOT}/cfdi33-generate" {if $includedTpl == "cfdi33-generate"} class="current"{/if}><span>Nuevo CFDi 3.3</span></a></li>
              <li><a href="{$WEB_ROOT}/sistema/consultar-facturas" {if $includedTpl == "sistema_consultar-facturas"} class="current"{/if}><span>Consultar Comprobantes</span></a></li>
              <li><a href="{$WEB_ROOT}/admin-folios/nuevos-folios" {if $includedTpl == "admin-folios_nuevos-folios"} class="current"{/if}><span>Lista de Folios</span></a></li>

              {if $info.version != "auto"}
              <li><a href="{$WEB_ROOT}/admin-folios/actualizar-certificado" {if $includedTpl == "admin-folios_actualizar-certificado"} class="current"{/if}><span>Actualizar Certificado</span></a></li>
              {/if}


            </ul>
            {/if}
            
            {if $mainMnu == "cfdi"}
                {if $User.roleId < 4}

                <li><a href="{$WEB_ROOT}/cfdi" {if $page == "cfdi"}class="current"{/if}>
                <span>Cuentas del Sistema</span></a></li>

                <li><a href="{$WEB_ROOT}/cfdiPagos" {if $page == "cfdiPagos"}class="current"{/if}>
                <span>Pagos y Activaciones</span></a></li>

                {/if}

            {/if}
            

       </ul>
    </div>
</div>
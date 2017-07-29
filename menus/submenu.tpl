<div id="tabs">
     <div class="container">
        <ul>
        	{if $mainMnu == "catalogos"}            	                    
                
                <li><a href="{$WEB_ROOT}/personal" {if $page == "personal"}class="current"{/if}>
                <span>Contadores</span></a></li>
                
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

                <li><a href="{$WEB_ROOT}/impuesto" {if $page == "impuesto"}class="current"{/if}>
                <span>Impuestos</span></a></li>

                <li><a href="{$WEB_ROOT}/obligacion" {if $page == "obligacion"}class="current"{/if}>
                <span>Obligaciones</span></a></li>                
				
				<li><a href="{$WEB_ROOT}/departamentos" {if $page == "departamentos"}class="current"{/if}>
                <span>Deptos</span></a></li>


            
            {/if}
            
            {if $mainMnu == "servicios"}            	                    
               <li><a href="{$WEB_ROOT}/servicios" {if $page == "servicios"}class="current"{/if}>
                <span>Servicios</span></a></li>    
            {/if}
            {if $mainMnu == "contratos"}            	                    
                  
                <li><a href="{$WEB_ROOT}/customer/tipo/Activos" {if $page == "customer" && $tipo == "Activos"}class="current"{/if}>
                <span>Listado Activos</span></a></li>                
                {if $User.roleId == 1 || $infoUser.tipoPersonal == "Gerente"}
				<li><a href="{$WEB_ROOT}/customer/tipo/Inactivos" {if $page == "customer" && $tipo == "Inactivos"}class="current"{/if}>
                <span>Listado Inactivos</span></a></li>                
				{/if}
            {/if}
            
            {if $mainMnu == "reportes"}            	                    
                {if $User.roleId < 4}
                
{*}                <li><a href="{$WEB_ROOT}/report-obligaciones" {if $page == "report-obligaciones"}class="current"{/if}>
                <span>Reporte de Subordinados</span></a></li>
                
                 <li><a href="{$WEB_ROOT}/report-basica" {if $page == "report-basica"}class="current"{/if}>
                <span>Rep. Propio.</span></a></li>{*}
                
                <li><a href="{$WEB_ROOT}/report-servicio" {if $page == "report-servicio"}class="current"{/if}>
                <span>Rep. de Servicio Anual</span></a></li>
				
				{if $User.roleId == 1}
                <li><a href="{$WEB_ROOT}/reporte_facturas.php" {if $page == "reporte_facturas"}class="current"{/if}>
                <span>Reporte de Facturas</span></a></li>
				{/if}
                
				<li><a href="{$WEB_ROOT}/report-servicio-mensual" {if $page == "report-servicio-mensual"}class="current"{/if}>
                <span>Rep. de Servicio Mensual</span></a></li>
                
				{if $User.roleId == 1}
				<li><a href="{$WEB_ROOT}/report-cxc" {if $page == "report-cxc"}class="current"{/if}>
                <span>Reporte CxC</span></a></li>
				{/if}
				<li><a href="{$WEB_ROOT}/report-documentacion-permanente" {if $page == "report-documentacion-permanente"}class="current"{/if}>
                <span>Rep. de documentacion perm.</span></a></li>
				<li><a href="{$WEB_ROOT}/report-archivos-permanente" {if $page == "report-archivos-permanente"}class="current"{/if}>
                <span>Rep. de archivos perm.</span></a></li>
        {if $User.roleId == 1}
        <!--<li><a href="{$WEB_ROOT}/log" {if $page == "log"}class="current"{/if}>
                <span>Log</span></a></li>-->
        {/if}
				{/if}
            
            {/if}

            {if $mainMnu == "cxc"}            	                    
                {if $User.roleId < 4}
                
                <li><a href="{$WEB_ROOT}/cxc" {if $page == "cxc"}class="current"{/if}>
                <span>Cuentas por Cobrar</span></a></li>
                
                 <li><a href="{$WEB_ROOT}/balance" {if $page == "balance"}class="current"{/if}>
                <span>Estados de Cuenta</span></a></li>
                
                {/if}
            
            {/if}
            
            
            {if $page == "sistema" || $page == "reporte-sat" || $page == "admin-folios"}
            <ul>
              <li><a href="{$WEB_ROOT}/sistema/nueva-factura" {if $includedTpl == "sistema_nueva-factura"} class="current"{/if}><span>Nuevo Comprobante</span></a></li>
              <li><a href="{$WEB_ROOT}/sistema/consultar-facturas" {if $includedTpl == "sistema_consultar-facturas"} class="current"{/if}><span>Consultar Comprobantes</span></a></li>
              <li><a href="{$WEB_ROOT}/admin-folios/nuevos-folios" {if $includedTpl == "admin-folios_nuevos-folios"} class="current"{/if}><span>Lista de Folios</span></a></li>
              
              {if $info.version != "auto"}
              <li><a href="{$WEB_ROOT}/admin-folios/actualizar-certificado" {if $includedTpl == "admin-folios_actualizar-certificado"} class="current"{/if}><span>Actualizar Certificado</span></a></li>
              {/if}

              
            </ul>
            {/if}                    
            
       </ul>
    </div>
</div>
delimiter //
CREATE OR REPLACE ALGORITHM = UNDEFINED
DEFINER = `root`@`localhost` SQL SECURITY DEFINER VIEW `vw_workflow_bonos_estado_resultado` AS SELECT
workflow.instanciaServicioId,
workflow.date,
workflow.status  estatus_workflow,
workflow.class,
workflow.costoWorkflow costo_workflow,
servicios.*
FROM
instanciaServicio workflow
 INNER JOIN (
 SELECT
     servicios.servicioId,
     servicios.inicioFactura,
     servicios.inicioOperaciones,
     tservicios.departamentoId,
     tservicios.periodicidad,
     tservicios.costo costo_catalogo,
     empresas.*
 FROM
     servicio servicios
         INNER JOIN tipoServicio tservicios ON servicios.tipoServicioId = tservicios.tipoServicioId
         INNER JOIN (
         SELECT
             empresas.contractId,
             empresas.`name` AS nombre_empresa,
             empresas.customerId,
             clientes.nameContact AS nombre_cliente,
             (
                 SELECT
                     CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 'departamentoId', departamentoId, 'personalId', personalId )), ']' )
                 FROM
                     contractPermiso
                 WHERE
                         contractPermiso.contractId = empresas.contractId
             ) AS responsables,
             clientes.active AS estatus_cliente,
             empresas.activo estatus_empresa
         FROM
             contract empresas
                 INNER JOIN customer clientes ON empresas.customerId = clientes.customerId
     ) empresas ON servicios.contractId = empresas.contractId
) servicios ON workflow.servicioId = servicios.servicioId ; //

delimiter ;

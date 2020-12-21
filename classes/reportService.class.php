<?php


class ReportService extends Servicio {

    function getAbServices () {
        global $monthsComplete;
        $personal = new Personal();
        $ftr = '';
        $str = "SELECT a.*, b.*, 
                CONCAT(
                '[',
                GROUP_CONCAT(
                    CONCAT(
                        '{\"fecha',
                        '\":\"',
                        c.fecha,
                        '\",\"',
                        'status',
                        '\":\"',
                        c.STATUS,
                         '\",\"',
                        'fechaFacturacion',
                        '\":\"',
                        c.inicioFactura,
                        '\"}'
                    )
                ),
                ']'      
                ) AS history FROM (SELECT servicio.servicioId,servicio.contractId, tipoServicio.nombreServicio, servicio.status, servicio.costo,servicio.inicioFactura,tipoServicio.departamentoId FROM servicio
                                   INNER JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId) a
                INNER JOIN (SELECT contract.contractId, contract.name, customer.nameContact FROM contract
                            INNER JOIN customer ON contract.customerId = customer.customerId) b
                ON a.contractId = b.contractId
                INNER JOIN historyChanges c ON a.servicioId = c.servicioId
                WHERE 1
                GROUP BY c.servicioId order by c.fecha desc 
                ";
        $this->Util()->DB()->setQuery($str);
        $services = $this->Util()->DB()->GetResult();

        $items = [];
        $filterMonth = $_POST['month'] ? (int)$_POST['month'] :'';
        $filterYear = $_POST['year'] ? (int)$_POST['year']:'';

        $statusBusqueda = ['baja', 'bajaParcial', 'activo'];
        $statusEquivalent = [
            'readonly' => 'Reactivacion solo lectura',
            'baja' => 'Baja',
            'activo' => 'Alta',
            'modificacion' => 'Modificacion',
            'bajaParcial' => 'Baja temporal',
            'reactivacion' => 'Reactivacion'
        ];

        $contracts = [];
        $id_contracts = [];
        foreach ($services as $key => $var) {
            $history = json_decode($var['history'], true);

            if(!is_array($history))
                continue;

            $contract_id = $var['contractId'];
            if(!in_array($contract_id, $id_contracts)) {
                $sql = ' SELECT a.departamentoId, a.departamento, b.personalId, c.name  FROM departamentos a
                         LEFT JOIN contractPermiso b ON a.departamentoId=b.departamentoId  
                         INNER JOIN personal c ON b.personalId = c.personalId
                         WHERE b.contractId = "'.$contract_id.'"
                         GROUP BY a.departamentoId';

                $this->Util()->DB()->setQuery($sql);
                $responsables = $this->Util()->DB()->GetResult();
                $responsables_lineal = [];
                foreach ($responsables as $resp) {
                    $responsables_lineal[$resp['departamentoId']] = $resp['personalId'];
                }
                $contracts[$contract_id]['responsables_lineal'] = $responsables_lineal;
                array_push($id_contracts, $contract_id);
            }

            $current_resposables =  $contracts[$contract_id]['responsables_lineal'];

            $var['supervisor']  = $personal->findSupervisor($current_resposables[$var['departamentoId']]);
            foreach ($history as $kh => $hist) {
               $flag =  true;
               $cad =  $var;
               $fecha = date('Y-m-d', strtotime($hist['fecha']));
               if ($_POST['statusSearch']) {
                   if ($_POST['statusSearch'] != $hist['status'])
                       continue;
               } else {
                   if ( !in_array($hist['status'], $statusBusqueda))
                       continue;
               }

               $dateMonth = date('m', strtotime($fecha));
               $dateYear = (int) date('Y', strtotime($fecha));

               if ($filterYear) {
                   if ($filterYear !== $dateYear)
                      $flag = false;
               }

               if ($filterMonth) {
                   if ($filterMonth !== (int)$dateMonth)
                      $flag = false;
               }

               $cad['month'] =  $monthsComplete[$dateMonth];
               $cad['typeMovimiento'] =  $statusEquivalent[$hist['status']];
               $cad['dateMovimiento'] =  $fecha;
               $cad['fechaFacturacion'] =  $hist['fechaFacturacion'];

                if ($flag) {
                    array_push($items, $cad);
                }
            }
        }
        $items = $this->Util()->orderMultiDimensionalArray($items, 'dateMovimiento');
        return $items;
    }
}

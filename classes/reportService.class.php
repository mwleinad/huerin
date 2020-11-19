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
                        '\"}'
                    )
                ),
                ']'      
                ) AS history FROM (SELECT servicio.servicioId,servicio.contractId, tipoServicio.nombreServicio, servicio.status, servicio.costo FROM servicio
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

        $statusBaja = ['baja', 'bajaParcial'];
        foreach ($services as $key => $var) {
            $history = json_decode($var['history'], true);
            $flag =  true;
            if(!is_array($history))
                continue;

            $services[$key]['historyArray'] = $history;
            $firstHistory =$history[key($history)];
            end($history);
            $lastHistory = $history[key($history)];

            $currentStatus = $var['status'];
            $dateAlta =  $firstHistory['fecha'];
            $dateBaja =  null;
            if (in_array($currentStatus, $statusBaja)) {
                $dateBaja = $this->Util()->isValidateDate($var['fechaBaja'], 'Y-m-d') ? $var['fechaBaja'] :  $lastHistory ['fecha'];
            }


            $dateAlta = date('Y-m-d', strtotime($dateAlta));
            $dateBaja = date('Y-m-d', strtotime($dateBaja));
            $monthDateAlta = date('m', strtotime($dateAlta));
            $var['month'] =  $monthsComplete[$monthDateAlta];
            $var['dateMovimiento'] =  $firstHistory['fecha'];
            $var['typeMovimiento'] =  'Alta';

            if($_POST['statusSearch'] == 'baja' || $_POST['statusSearch'] == 'alta') {

                if ($_POST['statusSearch'] == 'baja') {
                    $dateEval = $dateBaja;
                    $var['dateMovimiento'] =  $lastHistory['fecha'];
                    $var['typeMovimiento'] =  'Baja';
                } elseif ($_POST['statusSearch'] == 'alta') {
                    $dateEval = $dateAlta;
                    $var['dateMovimiento'] =  $firstHistory['fecha'];
                }

                $dateMonth = date('m', strtotime($dateEval));
                $dateYear = (int) date('Y', strtotime($dateEval));

                if ($filterYear) {
                    if ($filterYear !== $dateYear)
                        $flag = false;
                }

                if ($filterMonth) {
                    if ($filterMonth !== (int)$dateMonth)
                        $flag = false;
                }
                $var['month'] =  $monthsComplete[$dateMonth];
            }

            if ($flag) {
                $sql = ' SELECT a.departamentoId, a.departamento, b.personalId, c.name  FROM departamentos a
                         LEFT JOIN contractPermiso b ON a.departamentoId=b.departamentoId  
                         INNER JOIN personal c ON b.personalId = c.personalId
                         WHERE b.contractId = "'.$var['contractId'].'"
                         GROUP BY a.departamentoId';

                $this->Util()->DB()->setQuery($sql);
                $responsables = $this->Util()->DB()->GetResult();
                $key =  array_search($var['departamentoId'], array_column($responsables, 'departamentoId'));
                $var['supervisor']  = $personal->findSupervisor($responsables[$key]['personalId']);
                array_push($items, $var);
            }
        };
        return $items;
    }
}

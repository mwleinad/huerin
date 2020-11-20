<?php
class ContractActivity extends Contract {

    public  function getReport () {

        global $personal;
        $strFilter = "";
        if($_POST["responsableGerente"])
            $strFilter .= " and a.personalId = '".$_POST['responsableGerente']."' ";

        $sql = "select a.*, b.nivel,c.departamento, b.name as nameRol from personal a
                inner join roles b on a.roleId = b.rolId
                inner join departamentos c on a.departamentoId = c.departamentoId where b.nivel = 2 $strFilter order by c.departamento ASC,a.name ASC";
        $this->Util()->DB()->setQuery($sql);
        $gerentes = $this->Util()->DB()->GetResult();

        $strAct = "";
        if($_POST['sector'])
            $strAct .= " and c.id=".$_POST['sector'];
        if($_POST['subsector'])
            $strAct .= " and b.id=".$_POST['subsector'];
        if($_POST['actividad_comercial'])
            $strAct .= " and a.id=".$_POST['actividad_comercial'];


        $sql = "select a.id, a.name as actividad, b.id as subsector_id, b.name as subsector, c.id as sector_id, c.name as sector from actividad_comercial a
                inner join subsector b on a.subsector_id = b.id
                inner join sector c on b.sector_id = c.id where 1 " . $strAct;
        $this->Util()->DB()->setQuery($sql);
        $actividades =  $this->Util()->DB()->GetResult();
        $actividades = $this->Util()->changeKeyArray($actividades, 'id');
        $actividades_lineal = array_column($actividades, 'id');
        $subqueryFormat = "select contract.contractId, contract.name, contract.actividadComercialId,
                            CONCAT('[',
                                GROUP_CONCAT(
                                    CONCAT(
                                        '{\"departamentoId',
                                        '\":\"',
                                        contractPermiso.departamentoId,
                                        '\",\"',
                                        'personalId',
                                        '\":\"',
                                        contractPermiso.personalId,
                                        '\"}'
                                    )
                                ),
                                ']'      
                                ) AS encargados
                           from contract
                           inner join customer on contract.customerId=customer.customerId
                           inner join contractPermiso on contract.contractId = contractPermiso.contractId
                           where contractPermiso.personalId in(%s) and contract.activo = 'Si' and customer.active ='1' 
                           group by contractPermiso.contractId
                           ";
        $new_array = [];
        foreach($gerentes as $key => $value) {
            $personal->setPersonalId($value['personalId']);
            $subordinados = $personal->GetCascadeSubordinates();

            $subordinadosId = count($subordinados) > 0 ? array_column($subordinados, 'personalId') : [];
            $subString = "0".implode(',', $subordinadosId);
            $subquery = sprintf($subqueryFormat, $subString);

            $this->Util()->DB()->setQuery($subquery);
            $contracts = $this->Util()->DB()->GetResult();
            $currentDepartamentGerente = $value['departamentId'];
            $companies =  [];
            foreach($contracts as $con) {
                $encargados =  json_decode($con['encargados'], true);
                $keyId = array_search($currentDepartamentGerente, array_column($encargados, 'departamentoId'));
                $encar_dep_id =  $keyId >= 0  ? $encargados[$keyId]['personalId'] : $value['personalId'];


                $supervisor = $personal->findSupervisor($encar_dep_id, true);
                $supervisorId = $supervisor['personalId'];

                if($_POST['responsableSupervisor'] && $_POST['responsableSupervisor'] != $value['personalId']) {
                    if( $supervisorId != $_POST['responsableSupervisor'])
                        continue;
                }

                if(!in_array($con['actividadComercialId'], $actividades_lineal))
                    continue;
                $con['actividad'] = $actividades[$con['actividadComercialId']];
                $con['supervisor'] = $supervisor['name'];
                array_push($companies, $con);
            }
            if(!count($companies))
                continue;

            $companies = $this->Util()->orderMultiDimensionalArray($companies, 'supervisor');
            $cad = $value;
            $cad['companies'] = $companies;
            array_push($new_array, $cad);
        }
        $new_array = $this->Util()->orderMultiDimensionalArray($new_array, 'name');
        return $new_array;
    }
}

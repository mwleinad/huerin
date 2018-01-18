<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 17/01/2018
 * Time: 08:14 PM
 */
session_start();
class ContractRep extends Main
{
    private function findPermission($contrato, $respsCuenta){
        $split = split('-',$contrato['permisos']);
        foreach($split as $sp){
            $split2 = split(',',$sp);
            //Se agrego dep 25 que ya no existe
            if($split2[0] == 25) {
                continue;
            }
            if(in_array($split2[1],$respsCuenta)){
                return true;
            }
        }
        return false;
    }
    public function BuscarContractV2($formValues=array(),$activos=false)
    {
        $sqlFilter = "";
        global $personal;
        global $User;
        if($activos)
            $sqlFilter .= " AND customer.active = '1'";
        //Contratos Activos
        $sqlFilter .= ' AND contract.activo = "Si"';
        $sql = "SELECT contract.*, contract.name AS name, contract.encargadoCuenta AS encargadoCuenta,
				contract.responsableCuenta AS responsableCuenta, personal.jefeSocio, personal.jefeSupervisor,
				personal.jefeGerente, personal.jefeContador, customer.nameContact
				FROM contract
				LEFT JOIN customer ON customer.customerId = contract.customerId
				LEFT JOIN regimen ON regimen.regimenId = contract.regimenId
				LEFT JOIN sociedad ON sociedad.sociedadId = contract.sociedadId
				LEFT JOIN personal ON contract.responsableCuenta = personal.personalId
				WHERE 1 ".$sqlFilter."
				ORDER BY contract.name ASC";

        $this->Util()->DB()->setQuery($sql);
        $resContratos = $this->Util()->DB()->GetResult();

        $contratos = array();
        foreach($resContratos as $res){
            if($res['permisos']=="")
                continue;

            $encontrado = $this->findPermission($res, $formValues);
            if($encontrado == false) {
                continue;
            }
            //Checamos Servicios
            $sql = "SELECT * FROM servicio
					LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
					WHERE contractId = '".$res["contractId"]."'
					AND servicio.status = 'activo'
					".$sqlDepto."
					ORDER BY tipoServicio.nombreServicio ASC";
            $this->Util()->DB()->setQuery($sql);
            $res["servicios"] = $this->Util()->DB()->GetResult();
            $res["noServicios"] = count($res["servicios"]);

            //Si no tiene departamento asignado lo borro
            if ($res["servicios"][0]['departamentoId'] == "")
                continue;

            $contratos[] = $res;

        }

        return $contratos;
    }
}
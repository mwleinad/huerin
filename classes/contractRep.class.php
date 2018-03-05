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
    public function BuscarContract($formValues=array(),$activos=false , $deptos = array())
    {
        $sqlFilter = "";

        if($formValues['cliente'])
            $sqlFilter = " AND customer.nameContact LIKE '%".$formValues['cliente']."%'";

        if($formValues['razonSocial'])
            $sqlFilter = " AND contract.nombreComercial LIKE '%".$formValues['razonSocial']."%'";

        if($formValues['departamentoId'])
            $sqlDepto = " AND tipoServicio.departamentoId='".$formValues['departamentoId']."'";

        if($activos)
            $sqlFilter .= " AND customer.active = '1'";

        if($formValues['facturador'])
            $sqlFilter .= ' AND contract.facturador = "'.$formValues['facturador'].'"';

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

            $encontrado = $this->findPermission($res, $formValues['respCuenta']);
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
    public function BuscarContractV2($formValues=array(),$activos=false , $deptos = array())
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

        if(count($deptos)>0)
            $sqlDepto =" AND tipoServicio.departamentoId IN (".implode(',',$deptos).") ";

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
    public function FilesWhitoutDate($id){

        $sql = 'SELECT a.date,b.descripcion FROM archivo a LEFT JOIN tipoArchivo b ON a.tipoArchivoId=b.tipoArchivoId 
                WHERE a.date="0000-00-00" AND a.contractId='.$id.' GROUP BY a.tipoArchivoId ORDER BY a.date DESC';
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();

        return $result;
    }

    public function CheckExpirationFiel($item,$dep){
        $result2 = array();
        $nowAdd = strtotime('+1 month', strtotime(date('Y-m-d')));
        $addMonth = date('Y-m-d',$nowAdd);
        $sql ="SELECT MAX(archivoId) as archivoId,contractId,tipoArchivoId,MAX(date) as date from archivo where contractId=".$item['contractId']."  group by tipoArchivoId ORDER BY date DESC";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        $idArchivos = $this->Util()->ConvertToLineal($result,'archivoId');
        if(count($idArchivos)<=0)
            return $result2;

        $sqlf = 'SELECT
                    CASE 
                    WHEN DATE(NOW())>a.date THEN "Vencido"
                    WHEN DATE(NOW())<=a.date THEN "PorVencer"
                    END
                    AS typeExpirate,
                    a.date,b.descripcion,b.dptosId FROM archivo a LEFT JOIN tipoArchivo b ON a.tipoArchivoId=b.tipoArchivoId 
                    WHERE b.status="1" AND (date(now())>=a.date OR "'.$addMonth.'">=a.date) AND a.archivoId IN('.implode(",",$idArchivos).')';
        $this->Util()->DB()->setQuery($sqlf);
        $result2 = $this->Util()->DB()->GetResult();

        foreach($result2 as $key=>$value){
           $dptos =  explode(',',$value['dptosId']);
           if(!in_array($dep,$dptos))
               unset($result2[$key]);
        }

        return $result2;
    }
    public function SearchOnlyContract($formValues=array(),$activos=false){
        $sqlFilter = "";
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
				ORDER BY customer.name ASC";

        $this->Util()->DB()->setQuery($sql);
        $resContratos = $this->Util()->DB()->GetResult();
        $contratos = array();
        foreach($resContratos as $res) {
            if ($res['permisos'] == "")
                continue;

            $encontrado = $this->findPermission($res, $formValues);
            if ($encontrado == false) {
                continue;
            }
            $permisos = explode('-',$res['permisos']);
           /* $filesExp = $this->CheckExpirationFiel($res['contractId']);

            if(empty($filesExp))
                continue;
            $res['filesExpirate'] = $filesExp;*/
            foreach($permisos as $pk=>$vp){
                $dp = explode(',',$vp);
                switch($dp[0]){
                    case 1:$res['respContabilidad'] = $dp[1];break;
                    case 8:$res['respNominas'] = $dp[1];break;
                    case 31:$res['respAuditoria'] = $dp[1];break;
                    case 24:$res['respImss'] = $dp[1];break;
                    case 22:$res['respJuridico'] = $dp[1];break;
                    case 21:$res['respAdministracion'] = $dp[1];break;
                    case 26:$res['respMensajeria'] = $dp[1];break;
                }
            }

            $contratos[] =  $res;
        }
        return $contratos;
    }
}
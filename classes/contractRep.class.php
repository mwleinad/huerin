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
    public function findPermission($contrato, $respsCuenta){
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
				ORDER BY customer.nameContact ASC,contract.name ASC";

        $this->Util()->DB()->setQuery($sql);
        $resContratos = $this->Util()->DB()->GetResult();

        $contratos = array();
        $skip=false;
        //si el usuario que busca es cliente(roleId =4) debe ingresar por default dentro del array de contratos solo los de el.
        //rol cliente excluirles servicios en statua readonly.
        if($_SESSION['User']['roleId']==4)
        {
            $ftrServicio = " AND servicio.status IN('activo','bajaParcial') ";
            $skip=true;
        }
        else{
            $ftrServicio = " AND servicio.status IN('activo','bajaParcial','readonly') ";
        }


        //para el año 2018 en adelaten el servicio DIM no debe aparecer para nadie.
        $noInclude = "";
        if($formValues['year']>=2018)
            $noInclude = " AND lower(tipoServicio.nombreServicio) NOT LIKE '%dim%' ";

        foreach($resContratos as $res){
            if($res['permisos']=="")
                continue;

            $encontrado = $this->findPermission($res, $formValues['respCuenta']);
            if($encontrado == false &&!$skip) {
                continue;
            }
            //Checamos Servicios
           $sql = "SELECT *,servicio.status as servicioStatus FROM servicio
					LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
					WHERE contractId = '".$res["contractId"]."'
					$ftrServicio
                    AND tipoServicio.status='1'
					".$sqlDepto." $noInclude
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
					AND (servicio.status = 'activo' OR servicio.status='bajaParcial')
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
    public function SearchOnlyContract($formValues=array(),$activos=false,$isRepRazon=false,$personalId=0){
        global $personal,$contract;
        $sqlFilter = "";
        if($activos)
            $sqlFilter .= " AND customer.active = '1'";
        if($formValues['cliente'])
            $sqlFilter = " AND customer.nameContact LIKE '%".$formValues['cliente']."%'";

        if($formValues['razonSocial'])
            $sqlFilter = " AND contract.nombreComercial LIKE '%".$formValues['razonSocial']."%'";

        if($formValues['facturador'])
            $sqlFilter .= ' AND contract.facturador = "'.$formValues['facturador'].'"';

        //Contratos Activos
        $sqlFilter .= ' AND contract.activo = "Si"';

        $sql = "SELECT contract.*, contract.name AS name, contract.encargadoCuenta AS encargadoCuenta,
				contract.responsableCuenta AS responsableCuenta, personal.jefeSocio, personal.jefeSupervisor,
				personal.jefeGerente, personal.jefeContador, customer.nameContact,customer.phone as customerPhone,
				customer.email as customerEmail,customer.fechaAlta,customer.active as customerActive,regimen.nombreRegimen as nomRegimen
				FROM contract
				LEFT JOIN customer ON customer.customerId = contract.customerId
				LEFT JOIN regimen ON regimen.regimenId = contract.regimenId
				LEFT JOIN sociedad ON sociedad.sociedadId = contract.sociedadId
				LEFT JOIN personal ON contract.responsableCuenta = personal.personalId
				WHERE 1 ".$sqlFilter."
				ORDER BY customer.nameContact ASC";

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
            foreach($permisos as $pk=>$vp){
                $dp = explode(',',$vp);
                switch($dp[0]){
                    case 1:$res['respContabilidad'] = $dp[1];
                         if($isRepRazon)
                         {
                             $personal->setPersonalId($dp[1]);
                             $res['nameContabilidad']= $personal->GetNameById();
                         }
                    break;
                    case 8:$res['respNominas'] = $dp[1];
                          if($isRepRazon){
                              $personal->setPersonalId($dp[1]);
                              $res['nameNominas']= $personal->GetNameById();
                          }
                    break;
                    case 31:$res['respAuditoria'] = $dp[1];
                        if($isRepRazon){
                            $personal->setPersonalId($dp[1]);
                            $res['nameAuditoria']= $personal->GetNameById();
                        }
                    break;
                    case 24:$res['respImss'] = $dp[1];
                        if($isRepRazon){
                            $personal->setPersonalId($dp[1]);
                            $res['nameImss']= $personal->GetNameById();
                        }
                    break;
                    case 22:$res['respJuridico'] = $dp[1];
                        if($isRepRazon){
                            $personal->setPersonalId($dp[1]);
                            $res['nameJuridico']= $personal->GetNameById();
                        }
                    break;
                    case 21:$res['respAdministracion'] = $dp[1];
                        if($isRepRazon){
                            $personal->setPersonalId($dp[1]);
                            $res['nameAdministracion']= $personal->GetNameById();
                        }
                    break;
                    case 26:$res['respMensajeria'] = $dp[1];
                        if($isRepRazon){
                            $personal->setPersonalId($dp[1]);
                            $res['nameMensajeria']= $personal->GetNameById();
                        }
                    break;
                }
            }
            if($isRepRazon)
            {
                //encontrar total de iguala por cada contrato.
                $contract->setContractId($res['contractId']);
                $res['totalMensual'] =number_format($contract->getTotalIguala(),2,'.',',');
                $personal->setPersonalId($res['responsableCuenta']);
                $res['nameResponsableCuenta'] = $personal->GetNameById();
                $this->Util()->DB()->setQuery('select count(*) FROM contract WHERE customerId="'.$res['customerId'].'" AND activo="Si" ');
                $res['totalContracts'] = $this->Util()->DB()->GetSingle();
                if($personalId!=65&&$personalId!=$res['respAdministracion'])
                    continue;
            }
            $contratos[] =  $res;
        }
        return $contratos;
    }
    public function findContracts($filter=[]){
        $sqlFilter ="";
        $dpto = "";
        $fcustomer = "";
        if($filter['activos'])
        {
            $fcustomer .= " AND c.active = '1'";
            $sqlFilter .= ' AND a.activo = "Si"';
        }else{
            $sqlFilter .= " AND (c.active = '0' OR (c.active = '1' AND a.activo = 'No' ))";
        }

        if($filter['cliente'])
            $fcustomer .=" AND c.nameContact LIKE '%".$filter['cliente']."%'";
        if($filter['customerId'])
            $fcustomer .=" AND c.customerId  = '".$filter['customerId']."' ";

        if($filter['razonSocial'])
            $sqlFilter = " AND a.nombreComercial LIKE '%".$filter['razonSocial']."%'";

        if($filter['facturador'])
            $sqlFilter .= ' AND a.facturador = "'.$filter['facturador'].'"';

        if($filter['departamentoId'])
            $dpto .=" and b.departamentoId='".$filter['departamentoId']."' ";

        $sql = "SELECT a.*, a.name AS name, a.encargadoCuenta AS encargadoCuenta,c.nameContact,c.phone as customerPhone,c.email as customerEmail,
                c.fechaAlta,c.active as customerActive,r.nombreRegimen as nomRegimen
				FROM contract a
				INNER JOIN contractPermiso p ON a.contractId=p.contractId AND p.personalId IN(".implode(',',$filter["respCuenta"]).")
				INNER JOIN customer c ON a.customerId = c.customerId $fcustomer
				LEFT JOIN regimen r ON a.regimenId = r.regimenId
				WHERE 1 ".$sqlFilter."
				group by a.contractId
				ORDER BY  c.nameContact asc, a.name asc ";
        $this->Util()->DB()->setQuery($sql);
        $contracts = $this->Util()->DB()->GetResult();
        $noInclude = "";
        if($filter['year']>=2018)
            $noInclude = " AND lower(b.nombreServicio) NOT LIKE '%dim%' ";
        foreach ($contracts as $key =>$value){

                $this->Util()->DB()->setQuery("select a.*,b.nombreServicio from servicio a inner join tipoServicio b ON a.tipoServicioId = b.tipoServicioId $noInclude
					                             where a.contractId = '".$value["contractId"]."' and a.status IN ('activo','bajaParcial','readonly') and b.status='1' $dpto 
					                             order by b.nombreServicio asc");
                $contracts[$key]['servicios'] =  $this->Util()->DB()->GetResult();
            //si $filter['sinServicios'] es verdadero no se evalua esto
            if(!$filter['sinServicios']){
                //los que no tengan servicios se ignoran.
                if(empty($contracts[$key]['servicios'])){
                    unset($contracts[$key]);
                    continue;
                }
             }

             $nameEncargados = $this->encargadosArea($value['contractId']);
             foreach($nameEncargados as $val ){
                 $contracts[$key]['resp'.ucfirst(strtolower($val['departamento']))] = $val['personalId'];
                 $contracts[$key]['name'.ucfirst(strtolower($val['departamento']))] = $val['name'];
             }
        }
        return $contracts;
    }
    public function encargadosArea($contractId){
        $this->Util()->DB()->setQuery("select b.name,c.departamento,a.personalId  from contractPermiso a 
                                             inner join personal b on a.personalId=b.personalId
                                             inner join departamentos c on a.departamentoId=c.departamentoId
                                             where a.contractId='".$contractId."'
                                      ");
        return $this->Util()->DB()->GetResult();
    }
}
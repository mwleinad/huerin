<?php
class ContractRep extends Main
{
    private $contractId;
    public function setContractId($value){
        $this->contractId=$value;
    }
    public function findPermission($contrato, $respsCuenta){
        $split = explode('-',$contrato['permisos']);
        foreach($split as $sp){
            $split2 = explode(',',$sp);
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
            $sqlDepto = " AND b.departamentoId='".$formValues['departamentoId']."'";

        if($activos)
            $sqlFilter .= " AND customer.active = '1'";

        if($formValues['facturador'])
            $sqlFilter .= ' AND contract.facturador = "'.$formValues['facturador'].'"';
        if(isset($formValues["tipoSearch"])){
            if($formValues["tipoSearch"]=="contract")
            {
                switch($formValues["statusSearch"]){
                    case 'activo':
                        $sqlFilter .= ' AND contract.activo = "Si"';
                    break;
                    case 'baja':
                        $sqlFilter .= ' AND contract.activo = "No"';
                    break;
                }
            }
        }else{
            $sqlFilter .= ' AND contract.activo = "Si"';
        }
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
        if($_SESSION['User']['level']>50)
        {
            $ftrServicio = " AND a.status IN('activo','bajaParcial') ";
            $skip=true;
        }
        else{
            if(isset($formValues["statusSearch"]))
            {
                switch($formValues["statusSearch"]){
                    case 'activo':
                        $ftrServicio = " AND a.status IN('activo','readonly') ";
                    break;
                    case 'baja':
                        $ftrServicio = " AND a.status IN('baja','bajaParcial') ";
                    break;
                    default:
                        $ftrServicio = " AND a.status IN('activo','bajaParcial','readonly','baja') ";
                    break;
                }

            }else
                $ftrServicio = " AND a.status IN('activo','bajaParcial','readonly') ";
        }


        //para el año 2018 en adelaten el servicio DIM no debe aparecer para nadie.
        $noInclude = "";
        if(isset($formValues['year'])&&$formValues['year']>=2018)
            $noInclude = " AND lower(b.nombreServicio) NOT LIKE '%dim%' ";

        foreach($resContratos as $res){
            if($res['permisos']=="")
                continue;

            $encontrado = $this->findPermission($res, $formValues['respCuenta']);
            if($encontrado == false &&!$skip) {
                continue;
            }
            $res['resDepName'] =  $this->encargadosCustomKey('departamentoId', 'name', $res['contractId']);
            $res['resDepId'] =  $this->encargadosCustomKey('departamentoId', 'personalId', $res['contractId']);
            if(in_array($res["contractId"], explode(',', CONTRACTS_EXECPTION)))
                $noInclude="";
            //Checamos Servicios
            $sql = "SELECT a.status as servicioStatus,a.servicioId,a.contractId,a.tipoServicioId,a.costo,a.status,a.inicioOperaciones,a.inicioFactura,a.fechaBaja,a.lastDateWorkflow, 
                    b.nombreServicio,b.periodicidad,b.costoVisual,b.departamentoId
                    FROM servicio a
					LEFT JOIN tipoServicio b ON a.tipoServicioId = b.tipoServicioId
					WHERE a.contractId = '".$res["contractId"]."' $ftrServicio
                    AND b.status='1'
					".$sqlDepto." $noInclude
					ORDER BY b.nombreServicio ASC,a.servicioid ASC";
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
					LEFT JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
					WHERE contractId = '".$res["contractId"]."'
					AND servicio.status IN ('activo','bajaParcial')
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
                        a.date,b.descripcion,b.dptosId FROM archivo a   INNER JOIN tipoArchivo b ON a.tipoArchivoId=b.tipoArchivoId 
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
        if(!$filter['isCxc'])
        {
            if($filter['activos'])
            {
                $fcustomer .= " AND c.active = '1'";
                $sqlFilter .= " AND a.activo = 'Si'";
            }else{
                $sqlFilter .= " AND (c.active = '0' OR (c.active = '1' AND a.activo = 'No' ))";
            }
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
            $this->Util()->DB()->setQuery("select a.*,b.nombreServicio,b.departamentoId from servicio a inner join tipoServicio b ON a.tipoServicioId = b.tipoServicioId $noInclude
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
        $this->Util()->DB()->setQuery("select b.name,b.email,c.departamento,a.personalId,c.departamentoId from contractPermiso a 
                                             inner join personal b on a.personalId=b.personalId
                                             inner join departamentos c on a.departamentoId=c.departamentoId
                                             where a.contractId='".$contractId."'
                                      ");
        return $this->Util()->DB()->GetResult();
    }
    public function encargadosCustomKey($key1,$key2,$contractId){
        $encargados = $this->encargadosArea($contractId);
        if(!is_array($encargados))
            $encargados = [];
        $new = [];
        foreach($encargados as $val)
            $new[$val[$key1]] = $val[$key2];

        return $new;
    }
    public function getContracts($ftr=[],$whitService = false){
        global $personal,$rol;
        $ftrCustomer = "";
        $ftrContract = "";
        $limit = "";
        if($ftr['cliente'])
            $ftrCustomer =" AND a.customerId='".$ftr['cliente']."' ";
        if($ftr['contrato'])
            $ftrCustomer =" AND b.contractId='".$ftr['contrato']."' ";

        switch($ftr['tipo']) {
            case 'Inactivos':
                $ftrCustomer .= " AND (active = '0' OR (active = '1' AND contract.activo = 'No' ))";
                break;
            default:
                $ftrCustomer .= " AND a.active = '1' ";
                $ftrContract .= " AND b.activo = 'Si' ";
            break;
        }
        if(isset($ftr["limit"]))
            $limit = " LIMIT ".$ftr['limit'];
        if(isset($ftr['deep']) || isset($ftr['subordinados']))
            $fil['deep'] = 1;

        $fil['responsableCuenta'] = $ftr['responsableCuenta'];
        $encargados = $personal->GetIdResponsablesSubordinados($fil);
        $encargadosString =  implode(",",$encargados);

        $unlimited = $this->accessAnyContract();
        if($unlimited){
            if($ftr['responsableCuenta']>0)
                $join =  " INNER JOIN ";
            else
                $join =  " LEFT JOIN ";
        }
        else
            $join =  "  INNER JOIN ";

        $sql = "SELECT a.customerId,a.nameContact,b.contractId,b.name FROM customer a 
              $join contract b ON  a.customerId= b.customerId  
              $join contractPermiso c ON b.contractId=c.contractId AND c.personalId IN($encargadosString)
              WHERE 1 $ftrContract $ftrCustomer GROUP BY b.contractId $limit ORDER BY a.nameContact asc,b.name asc
               ";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
       if($whitService){
           if($ftr['departamentoId']>0)
               $ftrService = " and b.departamentoId ='".$ftr['departamentoId']."' ";

           foreach($result as $key=>$value){
               $idCon =  $value['contractId'];
               $sql="SELECT a.servicioId,a.inicioOperaciones,a.inicioFactura,a.status,a.costo,a.lastDateWorkflow,b.nombreServicio,b.departamentoId
                     FROM servicio a 
                     INNER JOIN tipoServicio b ON a.tipoServicioId=b.tipoServicioId and b.status='1' 
                     WHERE  a.contractId= '$idCon' and a.status NOT IN('baja','readonly') $ftrService ORDER BY b.nombreServicio ASC
                     ";
               $this->Util()->DB()->setQuery($sql);
               $result[$key]['servicios'] =  $this->Util()->DB()->GetResult();
           }
       }
       return $result;
    }
    public function getEmailsEncargadosLevel($filtro = []){
        global $personal;

        $strSociosMayoritarios = "";
        $filterMain = "";
        if(!$filtro["sendBraun"])
            $strSociosMayoritarios .= " and a.personalId!='".IDBRAUN."' ";
        if(!$filtro["sendHuerin"])
            $strSociosMayoritarios .= " and a.personalId!='".IDHUERIN."' ";
        if($filtro["departamentoId"]|| is_array($filtro["departamentoId"])){
            if(is_array($filtro["departamentoId"])){
                if(count($filtro["departamentoId"])>0)
                    $filterMain .="  and a.departamentoId in (".implode(",",$filtro["departamentoId"]).")";
            }
            else
                $filterMain .= " and a.departamentoId ='".$filtro["departamentoId"]."' ";
        }
        $sql = "select a.departamentoId,a.personalId from contractPermiso a 
                inner join personal b on a.personalId=b.personalId 
                where a.contractId = '".$this->contractId."' $filterMain  ";
        $this->Util()->DB()->setQuery($sql);
        $responsables = $this->Util()->DB()->GetResult();

        //no tiene responsables se obtienen los gerentes ,socios y coordinadores por default excepto braun y huerin si tiene el flag en false
        if(empty($responsables)){
            $sql = "select a.departamentoId,a.name,a.email,a.roleId,b.nivel from personal a inner join roles b on a.roleId=b.rolId where a.active='1' and a.rolId in (1,2) departamentoId NOT IN(32,33) $strSociosMayoritarios ";
            $this->Util()->DB()->setQuery($sql);
            $responsables = $this->Util()->DB()->GetResult();
        }else{
            $idResponsables  = [];
            foreach($responsables as $key=>$value){
                if(!in_array($value["personalId"],$idResponsables))
                    array_push($idResponsables,$value["personalId"]);

                if($filtro["incluirJefes"]){
                    $hisJefes= $personal->Jefes($value['personalId']);
                    $idResponsables = array_merge($idResponsables,$hisJefes);
                }
            }
            $strFilter = "";
            if($filtro["maxLevelRol"]||is_array($filtro["maxLevelRol"]))
            {
                if(is_array($filtro["maxLevelRol"])){
                    if(count($filtro["maxLevelRol"])>0)
                        $strFilter .="  and b.nivel in (".implode(",",$filtro["maxLevelRol"]).")";
                }
                else
                    $strFilter .= " and b.nivel <='".$filtro["maxLevelRol"]."' ";
            }


            $idResponsables = array_unique($idResponsables);

            $sql = "select a.departamentoId,a.name,a.email,a.roleId,b.nivel from personal a 
                    inner join roles b on a.roleId=b.rolId
                    where a.active='1' and a.personalId IN (".implode(",",$idResponsables).") $strFilter $strSociosMayoritarios
                    ";
            $this->Util()->DB()->setQuery($sql);
            $responsables = $this->Util()->DB()->GetResult();
        }
        if(!is_array($responsables))
            $responsables = [];

        $correos = [];
        foreach ($responsables as $key=>$value){
            if($this->Util()->ValidateEmail($value["email"]))
                $correos[$value["email"]] = $value["name"];
        }
        return $correos;
    }


}

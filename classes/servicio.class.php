<?php
use Dompdf\Dompdf;
class Servicio extends Contract
{
	private $servicioId;
	private $contratosActivos;

	private $tipoServicioId;
	public function setTipoServicioId($value)
	{
		$this->Util()->ValidateInteger($value);
		if($value == 0)
		{
			$this->Util()->setError(10055, 'error', 'Por favor, seleccionar un servicio' );
		}
		$this->tipoServicioId = $value;
	}

	public function getTipoServicioId()
	{
		return $this->tipoServicioId;
	}

	private $costo;
	public function setCosto($value)
	{
		$this->Util()->ValidateFloat($value, 2);
		$this->costo = $value;
	}

	private $inicioFactura;
	public function setInicioFactura($value)
	{
	    if($value!=''){
            $this->Util()->validateDateFormat($value,"Inicio factura");
            $value = $this->Util()->FormatDateMySql($value);
            $this->inicioFactura = $value;
        }
	}
	private $inicioOperaciones;
	public function setInicioOperaciones($value)
	{
        $this->Util()->validateDateFormat($value,"Inicio operaciones");
		$value = $this->Util()->FormatDateMySql($value);
		$this->validateFechaInicioOperaciones($value);
		$this->inicioOperaciones = $value;
	}
	function validateFechaInicioOperaciones($value){
	    $sql ="select nombreServicio from tipoServicio where tipoServicioId='".$this->tipoServicioId."' ";
	    $this->Util()->DB()->setQuery($sql);
	    $name = $this->Util()->DB()->GetSingle();
	    switch(strtoupper($name)){
            case 'PRIMA RIESGO DE TRABAJO':
                if(date('m',strtotime($value))!='02')
                    $this->Util()->setError(0,"error","Fecha de inicio de operación invalida para
                    el servicio Prima de Riesgo de Trabajo.");
            break;
            case 'ANUAL':
            case 'ANUAL AUDITADA':
            $sql = "select type from contract where contractId='".$this->getContractId()."' ";
            $this->Util()->DB()->setQuery($sql);
            $tipoPersona = $this->Util()->DB()->GetSingle();
                switch(strtoupper($tipoPersona)){
                    case 'PERSONA FISICA':
                        if(date('m',strtotime($value))!='04')
                            $this->Util()->setError(0,"error","Fecha inicio de operacion invalida para persona fisica");
                        break;
                    case 'PERSONA MORAL':
                        if(date('m',strtotime($value))!='03')
                            $this->Util()->setError(0,"error","Fecha inicio de operacion invalida para persona moral");
                        break;
                }
            break;
        }
    }
	public function setServicioId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->servicioId = $value;
	}
	public function getServicioId()
	{
		return $this->servicioId;
	}
	public function getInfoTipoServicio($field){
	    $this->Util()->DB()->setQuery("select $field from tipoServicio where tipoServicioId='$this->tipoServicioId'");
	    return $this->Util()->DB()->GetSingle();
    }
	private $_instanciaServicioId;
	public function setInstanciaServicioId($value) {
		$this->instanciaServicioId = $value;
	}

	private $_status;
	public function setStatus($value) {
		$this->status = $value;
	}
    private $_fechaDoc;
    public function setFechaDoc($value) {
        $this->fechaDoc = $value;
    }
	private $tipoBaja;
    public function setTipoBaja($value){
        $this->Util()->ValidateRequireField($value,'Tipo de baja');
        $this->tipoBaja=$value;
    }
    private $lastDateWorkflow;
    public function setLastDateWorkflow($value){
        if($this->Util()->ValidateRequireField($value,'Ultima fecha de workflow'))
            if($this->Util()->validateDateFormat($value,'Ultima fecha de workflow'))
                $this->lastDateWorkflow= $this->Util()->FormatDateMySql($value);
    }
	public function setContratosActivos($value){
		$this->contratosActivos = $value;
	}

  	public function OverwriteMonth($month){
	    $cad =array();
	    $add = '';
	    $monthNew="";

        switch($month){
            case 1:
                $add = "+5 month";
                $monthNew=6;
                break;
            case 2:
                $add = "+4 month";
                $monthNew=6;
                break;
            case 3:
                $add = "+3 month";
                $monthNew=6;
                break;
            case 4:
                $add = "+2 month";
                $monthNew=6;
                break;
            case 5:
                $add = "+1 month";
                $monthNew=6;
                break;
            case 6:
                $monthNew=6;
                break;
            case 7:
                $add = "+1 month";
                $monthNew=8;
                break;
            case 8:
                $monthNew=8;
                break;
            case 9:
                $add = "+1 month";
                $monthNew=10;
                break;
            case 10:
                $monthNew=10;
                break;
            case 11:
                $add = "+1 month";
                $monthNew=12;
                break;
            case 12:
                $monthNew=12;
                break;
        }
        if($monthNew>0 && $monthNew<10)
            $monthNew = "0".$monthNew;

        $cad['add']=$add;
        $cad['monthNew'] = $monthNew;
        return $cad;
    }
	public function Enumerate($id = '', $status = '')
	{
		global $months;

		$sql = "SELECT *,case 
                WHEN servicio.status = 'activo' THEN 'Activo'
                WHEN servicio.status = 'baja' THEN 'Baja'
                WHEN servicio.status = 'bajaParcial' THEN 'Baja temporal'
                WHEN servicio.status = 'readonly' THEN 'Activo / Solo lectura'
                END AS estado,servicio.status,servicio.costo AS costo, tipoServicio.costoVisual, tipoServicio.mostrarCostoVisual,
                case 
                WHEN servicio.status = 'activo' THEN 1
                WHEN servicio.status = 'baja' THEN 3
                WHEN servicio.status = 'bajaParcial' THEN 2
                WHEN servicio.status = 'readonly' THEN 4
                END AS prioridad
				FROM servicio 
				LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
				LEFT JOIN contract ON contract.contractId = servicio.contractId
				WHERE servicio.contractId = '".$this->getContractId()."'					
				ORDER BY servicio.status ASC, servicio.inicioOperaciones DESC";
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $value)
		{
			$fecha = explode("-", $value["inicioOperaciones"]);
			$result[$key]["formattedInicioOperaciones"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];

            $result[$key]["formattedInicioFactura"] =  null;
            if ($this->Util()->isValidateDate($value["inicioFactura"],"Y-m-d")) {

                $fecha = explode("-", $value["inicioFactura"]);
                $result[$key]["formattedInicioFactura"] = $fecha[2] . "/" . $months[$fecha[1]] . "/" . $fecha[0];
            }

            $result[$key]["formattedDateLastWorkflow"] = null;
            if ($this->Util()->isValidateDate($value["lastDateWorkflow"],"Y-m-d")) {
                $fecha = explode("-", $value["lastDateWorkflow"]);
                $result[$key]["formattedDateLastWorkflow"] = $fecha[2] . "/" . $months[$fecha[1]] . "/" . $fecha[0];
            }

            $result[$key]['dataJson'] =  json_encode($result[$key], ENT_QUOTES);
		}

		return $result;
	}

	public function EnumerateActive($type ="subordinado",$customer = 0, $contract = 0, $rfc = "", $departamentoId="", $respCta = 0)
	{
		global $months, $User;

		if($customer != 0)
			$sqlCustomer = " AND customer.customerId = '".$customer."'";

		if($contract != 0)
			$sqlContract = " AND contract.contractId = '".$contract."'";

		if(strlen($rfc) > 3 && $customer == 0 && $contract == 0)
			$sqlContract = " AND (customer.nameContact LIKE '%".$rfc."%' OR contract.name LIKE '%".$rfc."%')";

		if($User["subRoleId"] == "Nomina")
			$addNomina = " AND servicio.tipoServicioId IN (".SERVICIOS_NOMINA.")";

		if($respCta)
			$sqlRespCta = ' AND contract.responsableCuenta = '.$respCta;

		if($departamentoId!="")
		  $depto = " AND tipoServicio.departamentoId='".$departamentoId."'";

		$sql = "SELECT servicioId,  customer.nameContact AS clienteName, 
				contract.name AS razonSocialName, nombreServicio, servicio.costo, inicioOperaciones, periodicidad,
				servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta, 
				responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName,
				customer.customerId, customer.nameContact, contract.permisos, responsableCuenta.tipoPersonal,
				responsableCuenta.jefeContador, responsableCuenta.jefeSupervisor, responsableCuenta.jefeGerente, servicio.tipoServicioId, contract.activo
				FROM servicio 
				LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
				LEFT JOIN contract ON contract.contractId = servicio.contractId
				LEFT JOIN customer ON customer.customerId = contract.customerId
				LEFT JOIN personal AS responsableCuenta ON responsableCuenta.personalId = contract.responsableCuenta
				WHERE ".$debug." servicio.status IN ('activo','bajaParcial') AND tipoServicio.status='1' AND customer.active = '1'
				".$sqlCustomer.$sqlContract.$addNomina.$depto.$sqlRespCta." 					
				ORDER BY clienteName, razonSocialName, nombreServicio ASC";
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();
		//si el usuario es cliente convertir el userId y rolId a 1 para que funcione con todos los privilegios pero solo
        //sobre sus contratos.
		if(count($User['roleId']) == 4){
			$rolId= 1;
			$userId=0;
		}else{
            $rolId= $User['roleId'];
            $userId=$User['userId'];
        }

		//echo $User["roleId"];
		foreach($result as $key => $value){
			//echo $value["customerId"];
			$filtro = new Filtro;
			$contract = new Contract;
			$data["conPermiso"] = $filtro->UsuariosConPermiso($value['permisos'], $value["responsableCuenta"]);
			$data["subordinados"] = $filtro->Subordinados($userId);

			$data["subordinadosPermiso"] = $filtro->SubordinadosPermiso($type, $data["subordinados"], $userId);

			$data["withPermission"] = $filtro->WithPermission($rolId, $data["subordinadosPermiso"], $data["conPermiso"]);
			if($data["withPermission"] === false){
				unset($result[$key]);
				continue;
			}

			$result[$key]["responsableCuentaName"] = $result[$key]["responsableCuentaName"];
			$fecha = explode("-", $value["inicioOperaciones"]);
			$result[$key]["formattedInicioOperaciones"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];

			$this->Util()->DB()->setQuery("SELECT * FROM instanciaServicio
			WHERE servicioId = '".$value["servicioId"]."' AND status IN('activa','completa')	
			ORDER BY date DESC");
			$result[$key]["instancias"] = $this->Util()->DB()->GetResult();

			foreach($result[$key]["instancias"] as $keyInstancias => $valueInstancias)
			{
				$result[$key]["instancias"][$keyInstancias]["dateExploded"] = explode("-",$valueInstancias["date"]);
				$result[$key]["instancias"][$keyInstancias]["monthShow"] = $months[$result[$key]["instancias"][$keyInstancias]["dateExploded"][1]]." ".$result[$key]["instancias"][$keyInstancias]["dateExploded"][0];
			}

		}//foreach
		return $result;
	}
    /* funcion  EnumerateServiceForInstances
     * Esta funcion enumera todos los servicios que se crearan sus instancias
     * no debe filtrarse por encargados por que es una tarea automatica.
     * Solo deben sacar los servicios de las razones sociales de los clientes que se encuentren activos los que no no  debe sacar nada.
     * los contratos que tengan en su campo permisos vacio no debe sacarlos. con eso se podria dar por echo que solo se obtendra
     * contratos que al menos tenga un responsable. y asi evitar foreach
     * Para servicios con baja temporal se toman en cuenta , al crear la instancia se debe checar si el mes que esta ejecutandose
     * esta tarea sea <= la fecha del ultimo workflow
     */
    public function EnumerateServiceForInstances($customer = 0, $contract = 0, $rfc = "", $departamentoId="", $respCta = 0)
    {
        global $User;
        $sqlCustomer =  " AND customer.active = '1'";
        if($customer != 0)
            $sqlCustomer .= " AND customer.customerId = '".$customer."'";

        $sqlContract =  " AND contract.activo = 'Si' ";
        if($contract != 0)
            $sqlContract .= " AND contract.contractId = '".$contract."'";

        if(strlen($rfc) > 3 && $customer == 0 && $contract == 0)
            $sqlContract = " AND (customer.nameContact LIKE '%".$rfc."%' OR contract.name LIKE '%".$rfc."%')";

        if($respCta)
            $sqlRespCta = ' AND contract.responsableCuenta = '.$respCta;

        if($departamentoId!="")
            $depto = " AND tipoServicio.departamentoId='".$departamentoId."'";

        $sql = "SELECT servicioId,  customer.nameContact AS clienteName,servicio.status, IF(ISNULL(WEEK(servicio.lastDateWorkflow)), NULL, servicio.lastDateWorkflow) as lastDateWorkflow,
				contract.name AS razonSocialName, nombreServicio, servicio.costo, IF(ISNULL(WEEK(servicio.inicioOperaciones)), NULL, servicio.inicioOperaciones) as inicioOperaciones, periodicidad,
				servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta, IF(ISNULL(WEEK(servicio.inicioFactura)), NULL, servicio.inicioFactura) as inicioFactura,
				responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName,
				customer.customerId, customer.nameContact, contract.permisos, responsableCuenta.tipoPersonal,tipoServicio.departamento,
				responsableCuenta.jefeContador, responsableCuenta.jefeSupervisor, responsableCuenta.jefeGerente, servicio.tipoServicioId, contract.activo,tipoServicio.is_primary, tipoServicio.departamentoId,
				(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('departamento_id', contractPermiso.departamentoId, 'departamento',
                               departamentos.departamento, 'personal_id', contractPermiso.personalId, 'nombre', personal.name)), ']') 
                               FROM contractPermiso
                               INNER JOIN personal ON contractPermiso.personalId = personal.personalId  
                               INNER JOIN departamentos ON contractPermiso.departamentoId = departamentos.departamentoId
                               WHERE contractPermiso.contractId = contract.contractId 
                               GROUP BY contractPermiso.contractId) permiso_detallado
				FROM servicio 
				INNER JOIN (select a.*,b.departamento from tipoServicio a INNER JOIN departamentos b   ON a.departamentoId = b.departamentoId  where a.status='1') as tipoServicio ON servicio.tipoServicioId=tipoServicio.tipoServicioId
				INNER JOIN contract ON servicio.contractId = contract.contractId AND contract.permisos!=''
				INNER JOIN customer ON contract.customerId = customer.customerId
				LEFT JOIN personal AS responsableCuenta ON  contract.responsableCuenta =responsableCuenta.personalId
				WHERE (servicio.status = 'activo' OR servicio.status ='bajaParcial')
				".$sqlCustomer.$sqlContract.$depto.$sqlRespCta." ORDER BY contract.name ASC,tipoServicio.nombreServicio ASC ";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        return $result;
    }

    public function EnumerateServiceForRecotizacion($customer = 0, $contract = 0, $rfc = "", $departamentoId="", $respCta = 0)
    {
        global $User;
        $sqlCustomer =  " AND customer.active = '1'";
        if($customer != 0)
            $sqlCustomer .= " AND customer.customerId = '".$customer."'";

        $sqlContract =  " AND contract.activo = 'Si' ";
        if($contract != 0)
            $sqlContract .= " AND contract.contractId = '".$contract."'";

        if(strlen($rfc) > 3 && $customer == 0 && $contract == 0)
            $sqlContract = " AND (customer.nameContact LIKE '%".$rfc."%' OR contract.name LIKE '%".$rfc."%')";

        if($respCta)
            $sqlRespCta = ' AND contract.responsableCuenta = '.$respCta;

        if($departamentoId!="")
            $depto = " AND tipoServicio.departamentoId='".$departamentoId."'";

        $sql = "SELECT servicioId,  customer.nameContact AS clienteName,servicio.status, IF(ISNULL(WEEK(servicio.lastDateWorkflow)), NULL, servicio.lastDateWorkflow) as lastDateWorkflow,
				contract.name AS razonSocialName, nombreServicio, servicio.costo, IF(ISNULL(WEEK(servicio.inicioOperaciones)), NULL, servicio.inicioOperaciones) as inicioOperaciones, periodicidad,
				servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta, IF(ISNULL(WEEK(servicio.inicioFactura)), NULL, servicio.inicioFactura) as inicioFactura,
				responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName,
				customer.customerId, customer.nameContact, contract.permisos, responsableCuenta.tipoPersonal,tipoServicio.departamento,
				responsableCuenta.jefeContador, responsableCuenta.jefeSupervisor, responsableCuenta.jefeGerente, servicio.tipoServicioId, contract.activo,tipoServicio.is_primary, tipoServicio.departamentoId,
				(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('departamento_id', contractPermiso.departamentoId, 'departamento',
                               departamentos.departamento, 'personal_id', contractPermiso.personalId, 'nombre', personal.name)), ']') 
                               FROM contractPermiso
                               INNER JOIN personal ON contractPermiso.personalId = personal.personalId  
                               INNER JOIN departamentos ON contractPermiso.departamentoId = departamentos.departamentoId
                               WHERE contractPermiso.contractId = contract.contractId 
                               GROUP BY contractPermiso.contractId) permiso_detallado
				FROM servicio 
				INNER JOIN (select a.*,b.departamento from tipoServicio a INNER JOIN departamentos b   ON a.departamentoId = b.departamentoId  where a.status='1') as tipoServicio ON servicio.tipoServicioId=tipoServicio.tipoServicioId
				INNER JOIN contract ON servicio.contractId = contract.contractId AND contract.permisos!=''
				INNER JOIN customer ON contract.customerId = customer.customerId
				LEFT JOIN personal AS responsableCuenta ON  contract.responsableCuenta =responsableCuenta.personalId
				WHERE (servicio.status = 'activo' OR servicio.status ='bajaParcial') AND tipoServicio.periodicidad != 'Eventual'
				".$sqlCustomer.$sqlContract.$depto.$sqlRespCta." ORDER BY contract.name ASC,tipoServicio.nombreServicio ASC ";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        return $result;
    }
	public function EnumerateActiveSub($type ="subordinado",$customer = 0, $contract = 0, $rfc = "", $departamentoId="", $respCta = 0)
	{
		global $months, $User;

		$sqlContract = '';

		if($customer != 0)
			$sqlCustomer = " AND customer.customerId = '".$customer."'";

		if($contract != 0)
			$sqlContract .= " AND contract.contractId = '".$contract."'";

		if($this->contratosActivos)
			$sqlContract .= " AND contract.activo = '".$this->contratosActivos."'";

		if(strlen($rfc) > 3 && $customer == 0 && $contract == 0)
			$sqlContract .= " AND (customer.nameContact LIKE '%".$rfc."%' OR contract.name LIKE '%".$rfc."%')";

		if($User["subRoleId"] == "Nomina")
			$addNomina = " AND servicio.tipoServicioId IN (".SERVICIOS_NOMINA.")";

		if($respCta)
			$sqlRespCta = ' AND contract.responsableCuenta = '.$respCta;

		if($departamentoId!="")
		  $depto = " AND tipoServicio.departamentoId='".$departamentoId."'";

		$sql = "SELECT servicioId,  customer.nameContact AS clienteName, 
				contract.name AS razonSocialName, nombreServicio, servicio.costo, inicioOperaciones, periodicidad,
				servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta, 
				responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName,
				customer.customerId, customer.nameContact, contract.permisos, responsableCuenta.tipoPersonal,
				responsableCuenta.jefeContador, responsableCuenta.jefeSupervisor, responsableCuenta.jefeGerente
				FROM servicio 
				LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
				LEFT JOIN contract ON contract.contractId = servicio.contractId
				LEFT JOIN customer ON customer.customerId = contract.customerId
				LEFT JOIN personal AS responsableCuenta ON responsableCuenta.personalId = contract.responsableCuenta
				WHERE servicio.status = 'activo' AND customer.active = '1'
				".$sqlCustomer.$sqlContract.$addNomina.$depto.$sqlRespCta." 					
				ORDER BY clienteName, razonSocialName, nombreServicio ASC";
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		if(!$User)
			$User["roleId"] = 1;

		$servicios = array();
		foreach($result as $key => $value){

			$card = $value;

			$contract = new Contract;
			$conPermiso = $contract->UsuariosConPermiso($value['permisos'], $value["responsableCuenta"]);

			$personal = new Personal;
			$personal->setPersonalId($User["userId"]);
			$subordinados = $personal->Subordinados();

		    if($type == "propio"){
              	$subordinadosPermiso = array($User["userId"]);
            }else{

              $subordinadosPermiso = array();
              foreach ($subordinados as $sub) {
                array_push($subordinadosPermiso, $sub["personalId"]);
              }

              array_push($subordinadosPermiso, $User["userId"]);
            }//else

			$withPermission = false;

			if($User["roleId"] == 1 || $User["roleId"] == 4){
				$withPermission = true;
			}else{

				foreach($subordinadosPermiso as $usuarioPermiso){
					if(in_array($usuarioPermiso, $conPermiso)){
						$withPermission = true;
						break;
					}
				}
			}//else

			if($withPermission === false)
				continue;

			$fecha = explode("-", $value["inicioOperaciones"]);
			$card["formattedInicioOperaciones"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];

			$this->Util()->DB()->setQuery("SELECT * FROM instanciaServicio
			WHERE servicioId = '".$value["servicioId"]."'	
			ORDER BY date DESC");
			$resInstancias = $this->Util()->DB()->GetResult();

			$instancias = array();
			foreach($resInstancias as $val2){

				$card2 = $val2;

				$dateExploded = explode("-",$val2['date']);
				$card2["monthShow"] = $months[$dateExploded[1]]." ".$dateExploded[0];

				$instancias[] = $val2;
			}
			$card['instancias'] = $instancias;

			$servicios[] = $card;

		}//foreach

		if($type != 'subordinado')
			return $servicios;

		# ADD SUBORDINADOS #

		$personal = new Personal;
		$personal->setPersonalId($respCta);
		$subordinados = $personal->Subordinados();

		$result = array();
		foreach($subordinados as $res){

			$sqlRespCta = ' AND contract.responsableCuenta = '.$res['personalId'];

			$sql = "SELECT servicioId,  customer.nameContact AS clienteName, 
					contract.name AS razonSocialName, nombreServicio, servicio.costo, inicioOperaciones, periodicidad,
					servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta, 
					responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName,
					customer.customerId, customer.nameContact, contract.permisos, responsableCuenta.tipoPersonal,
					responsableCuenta.jefeContador, responsableCuenta.jefeSupervisor, responsableCuenta.jefeGerente
					FROM servicio 
					LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
					LEFT JOIN contract ON contract.contractId = servicio.contractId
					LEFT JOIN customer ON customer.customerId = contract.customerId
					LEFT JOIN personal AS responsableCuenta ON responsableCuenta.personalId = contract.responsableCuenta
					WHERE servicio.status = 'activo' AND customer.active = '1'
					".$sqlCustomer.$sqlContract.$addNomina.$depto.$sqlRespCta." 					
					ORDER BY clienteName, razonSocialName, nombreServicio ASC";
			$this->Util()->DB()->setQuery($sql);
			$result = $this->Util()->DB()->GetResult();

			foreach($result as $key => $value){

				$result[$key]["responsableCuentaName"] = $result[$key]["responsableCuentaName"];
				$fecha = explode("-", $value["inicioOperaciones"]);
				$result[$key]["formattedInicioOperaciones"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];

				$this->Util()->DB()->setQuery("SELECT * FROM instanciaServicio
				WHERE servicioId = '".$value["servicioId"]."'	
				ORDER BY date DESC");
				$result[$key]["instancias"] = $this->Util()->DB()->GetResult();

				foreach($result[$key]["instancias"] as $keyInstancias => $valueInstancias)
				{
					$result[$key]["instancias"][$keyInstancias]["dateExploded"] = explode("-",$valueInstancias["date"]);
					$result[$key]["instancias"][$keyInstancias]["monthShow"] = $months[$result[$key]["instancias"][$keyInstancias]["dateExploded"][1]]." ".$result[$key]["instancias"][$keyInstancias]["dateExploded"][0];
				}

				$servicios[] = $result[$key];

			}//foreach

		}//foreach

		return $servicios;

	}//EnumerateActiveSub


  	public function CancelWorkFlow()
	{
		$this->Util()->DB()->setQuery("
			UPDATE
				instanciaServicio
			SET
				`status` = '".$this->status."'
			WHERE 
      instanciaServicioId = '".$this->instanciaServicioId."'");
		$this->Util()->DB()->UpdateData();
	}
    public function ChangeDateWorkFlow()
    {
        if($this->Util()->PrintErrors()){
         return false;
        }
       $sql = "
			UPDATE
				instanciaServicio
			SET
				`date` = '".$this->fechaDoc."'
			WHERE 
      instanciaServicioId = '".$this->instanciaServicioId."'";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();

        $this->Util()->setError(1, "complete");
        $this->Util()->PrintErrors();
        return true;
    }

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT *,servicio.status, servicio.costo AS costo FROM servicio 
		LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
		LEFT JOIN contract ON contract.contractId = servicio.contractId
		WHERE servicioId = '".$this->servicioId."'");
		$row = $this->Util()->DB()->GetRow();

		$row["inicioOperacionesMysql"] = $this->Util()->FormatDateMySql($row["inicioOperaciones"]);
		$row["inicioFacturaMysql"] = $this->Util()->FormatDateMySql($row["inicioFactura"]);

		return $row;
	}
	public function InfoLog(){
        $this->Util()->DB()->setQuery("SELECT a.servicioId,a.status,a.costo,a.tipoServicioId,a.inicioOperaciones,a.inicioFactura,c.name,c.permisos FROM servicio a
		LEFT JOIN tipoServicio b ON b.tipoServicioId = a.tipoServicioId
		LEFT JOIN contract c ON c.contractId = a.contractId
		WHERE a.servicioId = '".$this->servicioId."'");
        $row = $this->Util()->DB()->GetRow();
        return $row;
    }

	public function Historial()
	{
		$this->Util()->DB()->setQuery("SELECT a.*, 
        CASE a.personalId
        WHEN 999990000 THEN 'Administrador'
        ELSE
         IF(b.name='',a.namePerson,b.name)
        END AS name, 
        CASE 
         WHEN a.status='activo' THEN 'Alta'
         WHEN a.status='baja' THEN 'Baja'
         WHEN a.status='bajaParcial' THEN 'Baja temporal'
         WHEN a.status='reactivacion' THEN 'Reactivacion'
         WHEN a.status='readonly' THEN 'Reactivacion/Solo lectura'
         WHEN a.status='modificacion' THEN 'Modificacion'
        END AS  movimiento 
        FROM historyChanges a
		LEFT JOIN personal b ON b.personalId = a.personalId
		WHERE a.servicioId = '".$this->servicioId."' ");
		$result = $this->Util()->DB()->GetResult();

		return $result;
	}
	public function Edit()
	{
		global $User,$log;
		if($this->Util()->PrintErrors()){ return false; }
		$infoServicio = $this->Info();
		$this->Util()->DB()->setQuery("
			UPDATE
				servicio
			SET
				`costo` = '".$this->costo."',
				`inicioFactura` = '".$this->inicioFactura."',
				`tipoServicioId` = '".$this->tipoServicioId."',
				`lastDateCreateWorkflow` = '0000-00-00',
				`inicioOperaciones` = '".$this->inicioOperaciones."'
			WHERE servicioId = '".$this->servicioId."'");
		$this->Util()->DB()->UpdateData();

        $this->updatePriceInCurrentWorkflow($this->servicioId, $this->costo);
		//actualizar historial
        $log->saveHistoryChangesServicios($this->servicioId,$this->inicioFactura,"modificacion",$this->costo,$_SESSION['User']['userId'],$this->inicioOperaciones,'');
        $this->resetDateLastProcessInvoice($infoServicio['contractId']);
        $this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Save()
	{
		global $User;
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			INSERT INTO
				servicio
			(
				`contractId`,
				`tipoServicioId`,
				`inicioFactura`,
				`inicioOperaciones`,
				`costo`
		)
		VALUES
		(
				'".$this->getContractId()."',
				'".$this->tipoServicioId."',
				'".$this->inicioFactura."',
				'".$this->inicioOperaciones."',
				'".$this->costo."'
		);");

		$servicioId = $this->Util()->DB()->InsertData();
		$this->resetDateLastProcessInvoice($this->getContractId());

		//actualizar historial
		$this->Util()->DB()->setQuery("
			INSERT INTO
				historyChanges
			(
				`servicioId`,
				`inicioFactura`,
				`costo`,
				`personalId`,
				`inicioOperaciones`
		)
		VALUES
		(
				'".$servicioId."',
				'".$this->inicioFactura."',
				'".$this->costo."',
				'".$User["userId"]."',
				'".$this->inicioOperaciones."'
		);");

		$this->Util()->DB()->InsertData();
		$this->Util()->setError(2, "complete");
		$this->Util()->PrintErrors();

		return $servicioId;
	}
    /*
     * funcion ActivateService reactiva un servicio que se encuentra en status baja o baja temporal.
     * Si se encuentra en status baja la reactivacion pasa a ser de solo lectura no creara workflows utilizar el status readonly
     * Si se encuentra en status bajaParcial la reactivacion hara que el servicio pase a status activo
     */
	public function ActivateService()
	{
		global $User,$log;

		if($this->Util()->PrintErrors()){ return false; }

		$info = $this->InfoLog();
        $addSql ="";
        switch($info['status']){
            case 'readonly':
            case 'activo':
                $active = 'baja';
                $addSql .= ", fechaBaja=DATE(NOW())";
                $action =  "Baja";
                $complete = "El servicio fue dado de baja correctamente.";
            break;
            case 'bajaParcial':
                $active = 'activo';
                $addSql .= ", lastDateCreateWorkflow='0000-00-00'";
                $action =  "Reactivacion";
                $complete = "El servicio ha sido reactivado correctamente.";
            break;
            case 'baja':
                $active = 'readonly';
                $action =  "readonly";
                $complete = "El servicio ha sido reactivado para solo lectura.";
            break;
        }
        $this->Util()->DB()->setQuery("UPDATE servicio
			                                 SET status = '".$active."'
			                                 $addSql
			                                 WHERE servicioId = '".$this->servicioId."' ");
        $this->Util()->DB()->UpdateData();
        $servicio = $this->InfoLog();

        //Guardamos el Log
        $log->setPersonalId($User['userId']);
        $log->setFecha(date('Y-m-d H:i:s'));
        $log->setTabla('servicio');
        $log->setTablaId($this->servicioId);
        $log->setAction($action);
        $log->setOldValue(serialize($info));
        $log->setNewValue(serialize($servicio));
        $log->Save();

		//actualizar historial del servicio
        $log->saveHistoryChangesServicios($servicio['servicioId'],$servicio['inicioFactura'],lcfirst($action),$servicio['costo'],$User['userId'],$servicio['inicioOperaciones']);

		$this->Util()->setError(3, "complete", $complete);
		$this->Util()->PrintErrors();
		return true;
	}

	public function UpdateComentario($comentario)
	{
		global $User;

		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				servicio
			SET
				`comentario` = '".$comentario."'
			WHERE servicioId = '".$this->servicioId."'");
		$this->Util()->DB()->UpdateData();


		$this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function HistorialAll()
	{
		$this->Util()->DB()->setQuery("
			SELECT historyChanges.*, personal.name AS personalName, servicio.tipoServicioId, servicio.contractId, tipoServicio.nombreServicio, contract.customerId, contract.name AS contractName, customer.customerId, customer.nameContact FROM historyChanges
			JOIN personal ON personal.personalId = historyChanges.personalId
			JOIN servicio ON servicio.servicioId = historyChanges.servicioId
			JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
			JOIN contract ON contract.contractId = servicio.contractId
			JOIN customer ON customer.customerId = contract.customerId
			WHERE historyChangesId > 1512 ORDER BY historyChangesId DESC");
		$data =$this->Util()->DB()->GetResult();

		return $data;
	}

	public function resetDateLastProcessInvoice ($id) {
	    $sql = "update contract set lastProcessInvoice= '0000-00-00' where  contractId='".$id."'";
	    $this->Util()->DB()->setQuery($sql);
	    $this->Util()->DB()->UpdateData();
    }

	/*
	 * funcion DownServicio()
	 * Dar de baja los servicios de forma complete o partial
	 * Forma = complete pasa a desactivarse al momento
	 * Forma  = partial se espera una fecha para el ultimo workflow que se creara.
	 */
	public function DownServicio(){
        global $User,$log;
        if($this->Util()->PrintErrors())
            return false;

        $before = $this->InfoLog();
        $setDate = "";
        $action = "Baja";
        switch($this->tipoBaja){
            case 'complete':
                $active = 'baja';
                $action = "Baja";
            break;
            case 'partial':
                $active ='bajaParcial';
                $setDate = ",lastDateWorkflow='".$this->lastDateWorkflow."' ";
                $message ="EL servicio se ha dado de baja temporalmente.";
                $action = "bajaParcial";
            break;
        }

        $this->Util()->DB()->setQuery("UPDATE servicio SET status = '".$active."' ".$setDate." WHERE servicioId = '".$this->servicioId."'");
        $this->Util()->DB()->UpdateData();

        $after = $this->InfoLog();

        //Guardamos el Log
        $log->setPersonalId($User['userId']);
        $log->setFecha(date('Y-m-d H:i:s'));
        $log->setTabla('servicio');
        $log->setTablaId($this->servicioId);
        $log->setAction($action);
        $log->setOldValue(serialize($before));
        $log->setNewValue(serialize($after));
        $log->Save();
        //actualizar historial
        $this->Util()->DB()->setQuery("
			INSERT INTO
				historyChanges
			(
				`servicioId`,
				`inicioFactura`,
				`status`,
				`costo`,
				`personalId`,
				`inicioOperaciones`
            )
            VALUES
            (
                    '".$after["servicioId"]."',
                    '".$after["inicioFactura"]."',
                    '".$after["status"]."',
                    '".$after["costo"]."',
                    '".$User["userId"]."',
                    '".$after["inicioOperaciones"]."'
            );");
        $this->Util()->DB()->InsertData();
        $this->Util()->setError(3, "complete", $message);
        $this->Util()->PrintErrors();

       return true;
    }
    public function doBajaTemporalMultiple($initialState,$endState){
        $contratos = [];
        $listContracts = $this->getIdContracts();
        if(!is_array($listContracts))
            $listContracts = [];

        switch($endState){
            case 'bajaParcial':
                foreach ($listContracts as $conId) {
                    $cad = [];
                    if($_POST['dateWorkflow'.$conId]==""||!$this->Util()->isValidateDate($_POST['dateWorkflow'.$conId])){
                        $this->Util()->setError(0, "error", 'Si selecciona una razon social, compruebe que el campo ultimo workflow de la fila sea una fecha valida o  no se encuentre vacia.');
                        break;
                    }
                    else{
                        $cad['contractId']=$conId;
                        $cad['dateWorkflow'] = $this->Util()->FormatDateMySql($_POST['dateWorkflow'.$conId]);
                        $contratos[] = $cad;
                    }
                }
                $message = 'Baja temporal realizado correctamente.';
            break;
            case 'activo':
                foreach ($listContracts as $conId){
                    $cad['contractId']=$conId;
                    $cad['dateWorkflow'] =null;
                    $contratos[] = $cad;
                }
                $message = 'Se han reactivado correctamente todos los servicios.';
            break;
        }

        if($this->Util()->PrintErrors())
            return false;
        foreach ($contratos as $value){
            $this->doBajaTemporalServicesByContrato($value['contractId'],$value['dateWorkflow'],$initialState,$endState);
        }
        $this->Util()->setError(0, "complete", $message);
        $this->Util()->PrintErrors();
        return true;

    }
    public function doBajaTemporalServicesByContrato($conId,$fechaWorkflow,$initialState,$endState){
	    global $log,$User,$smarty,$contractRep;
        //Hay que iterar servicio por servicio para guardar su historial.
        $sql ="select a.servicioId,b.nombreServicio,a.inicioFactura,a.inicioOperaciones,a.costo,b.departamentoId 
              from servicio a 
              inner join tipoServicio b on a.tipoServicioId=b.tipoServicioId and b.status='1' where a.contractId='".$conId."' and a.status='".$initialState."' ";
        $this->Util()->DB()->setQuery($sql);
        $servicios = $this->Util()->DB()->GetResult();
        switch ($endState){
            case 'bajaParcial':
                $dateWorflow = " ,lastDateWorkflow='" .$fechaWorkflow . "' ";
                $action ="bajaParcial";
            break;
            case 'activo':
                $dateWorflow =", lastDateCreateWorkflow='0000-00-00'";
                $action ="Reactivacion";
            break;
        }
        $this->Util()->DB()->setQuery('SELECT name FROM personal WHERE personalId="'.$User['userId'].'" ');
        $who = $this->Util()->DB()->GetSingle();

        if($_SESSION['User']['tipoPers']=='Admin')
            $who="Administrador de sistema";

        $serviciosAfectados =  [];
        $departamentos = [];
        foreach($servicios as $key=>$value) {
            $servicioAfectado = [];
            $this->setServicioId($value['servicioId']);
            $before = $this->InfoLog();
            $this->Util()->DB()->setQuery("UPDATE servicio SET status = '".$endState."' $dateWorflow  WHERE servicioId = '" . $value['servicioId'] . "'");
            $this->Util()->DB()->UpdateData();
            $after = $this->InfoLog();

            //Guardamos el log sin enviar eso lo haremos pero de manera global por cada razon
            $log->setPersonalId($User['userId']);
            $log->setFecha(date('Y-m-d H:i:s'));
            $log->setTabla('servicio');
            $log->setTablaId($value['servicioId']);
            $log->setAction($action);
            $log->setOldValue(serialize($before));
            $log->setNewValue(serialize($after));
            $log->SaveOnly();
            //actualizar historial del servicio
            $log->saveHistoryChangesServicios($value['servicioId'],$value['inicioFactura'],lcfirst($action),$value['costo'],$User['userId'],$value['inicioOperaciones'],$who);
            $servicioAfectado= $value;
            $servicioAfectado['ultimoWorkflow'] = $fechaWorkflow;
            $serviciosAfectados[]=$servicioAfectado;
            $departamentos[]=$value['departamentoId'];
        }
        if(!empty($serviciosAfectados)) {
            $filtros['sendBraun'] = false;
            $filtros['sendHuerin'] = true;
            $filtros['incluirJefes'] = true;
            $filtros['level'] = 5;
            $filtros['departamentos'] = $departamentos;
            $this->setContractId($conId);
            $data = $this->findEmailEncargadosJefesByContractId($filtros);
            $encargados = $contractRep->encargadosArea($conId);

            $smarty->assign('encargados', $encargados);
            $smarty->assign('serviciosAfectados', $serviciosAfectados);
            $smarty->assign('razon', $data['razon']);
            $smarty->assign('endState', $endState);
            $smarty->assign('who', $who);
            $body = $smarty->fetch(DOC_ROOT . '/templates/molds/body-email-baja-parcial-reactivacion.tpl');
            $html = $smarty->fetch(DOC_ROOT . '/templates/molds/pdf-log-baja-parcial-reactivacion.tpl');
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
            $dompdf =  new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('letter', 'portrait');
            $dompdf->render();
            $fileName  = "down_".$_SESSION['User']['userId']."_log.pdf";
            $output =  $dompdf->output();
            file_put_contents(DOC_ROOT."/sendFiles/$fileName", $output);
            if(file_exists( DOC_ROOT."/sendFiles/$fileName")){
                $file = DOC_ROOT."/sendFiles/$fileName";
            }
            else{
                $file="";
                $fileName="";
            }
            if(!SEND_LOG_MOD)
                $data['encargados'] = [];

            $mail = new SendMail();
            $subject = 'NOTIFICACION DE CAMBIOS EN PLATAFORMA';
            $mail->PrepareMultipleNotice($subject, $body, $data['encargados'], '', $file, $fileName, "", "", 'noreply@braunhuerin.com.mx', 'Administrador de plataforma', true);
            if(file_exists( $file)){
                unlink($file);
            }
        }
        return true;
    }
    public function validateArrayServices()
    {
        if (!isset($_POST['servsMod']))
            $this->Util()->setError(0, 'error', 'Es necesario seleccionar al menos un servicio. ');
        elseif (empty($_POST['servsMod']))
            $this->Util()->setError(0, 'error', 'Es necesario seleccionar al menos un servicio. ');
        foreach ($_POST['servsMod'] as $servId) {
            $sql = "";
            $status = $_POST["status_$servId"];
            $flw = $status=='bajaParcial' ? ($this->Util()->isValidateDate($_POST["lastDateWorkflow_$servId"],'d-m-Y') ? $_POST["lastDateWorkflow_$servId"]: false) : true;
            $costo = $_POST["costo_$servId"];
            $io = $this->Util()->isValidateDate($_POST["io_$servId"],'d-m-Y')?$_POST["io_$servId"]:false;

            $if = $_POST["if_$servId"]!=''?$this->Util()->isValidateDate($_POST["if_$servId"],'d-m-Y')?$_POST["if_$servId"]:false:true;
            if (!$flw) {
                $this->Util()->setError(0, 'error', 'Fecha invalida en ultimo workflow. ');
                break;
            }
            if(!$io){
                $this->Util()->setError(0, 'error', 'Fecha invalida en inicio de operaciones. ');
                break;
            }else{
                $this->Util()->DB()->setQuery("select tipoServicioId from servicio where servicioId ='$servId' ");
                $tipServId = $this->Util()->DB()->GetSingle();
                $this->setTipoServicioId($tipServId);
                $this->validateFechaInicioOperaciones($io);
            }
            if(!$if)
            {
                $this->Util()->setError(0, 'error', 'Fecha invalida en inicio de factura. ');
                break;
            }
        }
        if($this->Util()->PrintErrors())
            return false;

        return true;
    }
    public function executeMultipleOperation()
    {
        global $log;
        if(!$this->validateArrayServices())
            return false;

        $actualizados = 0;
        $servs = [];
        $contratoId = $_POST['contractId'];
        foreach ($_POST['servsMod'] as $servId) {
            $status = $_POST["status_$servId"];
            $flw = $this->Util()->isValidateDate($_POST["lastDateWorkflow_$servId"],'d-m-Y')&&$status=='bajaParcial'?$this->Util()->FormatDateMySql($_POST["lastDateWorkflow_$servId"]):'0000-00-00';
            $costo = $_POST["costo_$servId"];
            $io = $this->Util()->isValidateDate($_POST["io_$servId"],'d-m-Y')?$this->Util()->FormatDateMySql($_POST["io_$servId"]):false;
            $if = $_POST["if_$servId"]!=''?$this->Util()->FormatDateMySql($_POST["if_$servId"]):'0000-00-00';
            $setFechabaja ="";
            if($status=='baja')
                $setFechabaja = "fechaBaja=DATE(NOW()), ";



            $sql = "UPDATE servicio SET
                    costo ='$costo',
                    inicioOperaciones = '$io',
                    inicioFactura = '$if',
                    lastDateWorkflow = '$flw',
                    $setFechabaja
                    status = '$status',
                    lastDateCreateWorkflow='0000-00-00'
                    WHERE servicioId ='$servId' and contractId='$contratoId'
                   ";
            $this->Util()->DB()->setQuery($sql);
            $affect = $this->Util()->DB()->UpdateData();
            if($affect>0){
                $servs[] = $servId;
                $actualizados++;
                $this->updatePriceInCurrentWorkflow($servId, $costo);
                if($_POST["beforeStatus_$servId"]!=$_POST["status_$servId"]){
                    switch($_POST["status_$servId"]){
                        case 'activo':
                            $evento = "reactivacion";
                        break;
                        default:
                            $evento = $_POST["status_$servId"];
                        break;
                    }
                }else{
                    $evento = "modificacion";
                }
                $log->saveHistoryChangesServicios($servId,$if,$evento,$costo,$_SESSION['User']['userId'],$io,'',$flw);
            }
        }
        $this->resetDateLastProcessInvoice($contratoId);
        $log->sendLogMultipleOperation($servs,$contratoId);
        $this->Util()->setError(0, 'complete', 'Se han modificado los servicios correctamente. ');
        $this->Util()->PrintErrors();
        return true;
    }
    public function cleanItemsServices(){
	    if(isset($_SESSION['itemsServices']));
	       unset($_SESSION['itemsServices']);
    }
    public function validateItemsServices(){
        if(isset($_SESSION['itemsServices']))
        {
            if(empty($_SESSION['itemsServices']))
                $this->Util()->setError(0,'error','Es necesario agregar al menos un servicio.');
        }else
          $this->Util()->setError(0,'error','Es necesario agregar al menos un servicio.');
    }
    public function saveItemInSession(){
	    if($this->Util()->PrintErrors())
	        return false;

	    if(!isset($_SESSION['itemsServices']))
	        $_SESSION['itemsServices'] = [];

	    end($_SESSION['itemsServices']);
	    $llave = key($_SESSION['itemsServices'])+1;
	    $_SESSION['itemsServices'][$llave]['tipoServicioId']=$this->tipoServicioId;
        $_SESSION['itemsServices'][$llave]['nombreServicio']=$this->getInfoTipoServicio('nombreServicio');
        $_SESSION['itemsServices'][$llave]['inicioOperaciones']=$this->inicioOperaciones;
        $_SESSION['itemsServices'][$llave]['inicioFactura']=$this->inicioFactura;
        $_SESSION['itemsServices'][$llave]['costo']=$this->costo;
        $this->Util()->setError(0,'complete',"Servicio agregado correctamente");
        $this->Util()->PrintErrors();
        return true;
    }
    public function deleteItemInSession($key){
	    unset($_SESSION["itemsServices"][$key]);
	    $this->Util()->setError(0,'complete',"Servicio eliminado correctamente");
        $this->Util()->PrintErrors();
        return true;
	}
	public function saveMultipleServicio(){
	    global $customer;
	    $this->validateItemsServices();
	   if($this->Util()->PrintErrors())
	       return false;
	   global $log;
       $id_services = [];
        $conId =  $this->getContractId();
       $actuales = $customer->GetServicesByContract($conId);
	   foreach($_SESSION['itemsServices'] as $key=>$value){
	       $tpServId = $value['tipoServicioId'];
           $io = $value['inicioOperaciones'];
           $if = $value['inicioFactura'];
           $cst= $value['costo'];
           $sql = "INSERT INTO servicio(
                 contractId,
                 tipoServicioId,
                 inicioOperaciones,
                 inicioFactura,
                 costo,
                 status                        
                )VALUES(
                '$conId',
                '$tpServId',
                '$io',
                '$if',
                '$cst',
                'activo'
                )
               ";
	     $this->Util()->DB()->setQuery($sql);
	     $lastId = $this->Util()->DB()->InsertData();
	     $id_services[] = $lastId;
	     $log->saveHistoryChangesServicios($lastId,$if,'activo',$cst,0,$io);

	     $this->setServicioId($lastId);
         $newServicio = $this->InfoLog();

         $log->setPersonalId($_SESSION['User']['userId']);
         $log->setFecha(date('Y-m-d H:i:s'));
         $log->setTabla('servicio');
         $log->setTablaId($lastId);
         $log->setAction('Insert');
         $log->setOldValue('');
         $log->setNewValue(serialize($newServicio));
         $log->SaveOnly();
       }
       $this->resetDateLastProcessInvoice($conId);
       $log->sendLogMultipleOperation($id_services,$conId,'new',$actuales);
	   $this->cleanItemsServices();
       $this->Util()->setError(0,'complete',"Se han guardado correctamente los servicios.");
       $this->Util()->PrintErrors();
	   return true;
    }
    /*
     * Funcion
     * Dar de baja definitiva los servicios en estado activo o baja temporal de un contrato
     * @conId es el identificador del contrato
     */
    public function downServicesByContract($conId){
        global $log;
        $serviciosAfectados = [];
        $sql ="select * from servicio where contractId='$conId' and status IN('activo','bajaParcial','readonly')";
        $this->Util()->DB()->setQuery($sql);
        $servicios = $this->Util()->DB()->GetResult();
        foreach ($servicios as $key=>$servicio) {
            $this->Util()->DB()->setQuery("update servicio set status='baja' where servicioId ='" . $servicio["servicioId"] . "' ");
            $up = $this->Util()->DB()->UpdateData();
            if ($up > 0) {
                $log->saveHistoryChangesServicios($servicio["servicioId"], $servicio['inicioFactura'], "baja", $servicio["costo"], $_SESSION["User"]["personalId"], $servicio["inicioOperaciones"], "", $this->inicioOperaciones["lastDateWorkflow"]);

                $this->Util()->DB()->setQuery("select a.*,b.nombreServicio,b.periodicidad from servicio a 
                                                     inner join tipoServicio b on a.tipoServicioId=b.tipoServicioId where servicioId ='" . $servicio["servicioId"] . "' ");
                $afterDetail = $this->Util()->DB()->GetRow();
                $this->Util()->DB()->setQuery("select * from servicio where servicioId ='" . $servicio["servicioId"] . "' ");
                $after = $this->Util()->DB()->GetRow();

                $log->setFecha(date('Y-m-d H:i:s'));
                $log->setTabla('servicio');
                $log->setTablaId($servicio["servicioId"]);
                $log->setAction('Baja');
                $log->setOldValue(serialize($servicio));
                $log->setNewValue(serialize($after));
                $log->SaveOnly();
                array_push($serviciosAfectados,$afterDetail);
            }
        }
        return $serviciosAfectados;
    }

    /*
     * Funcion
     * Reactivar servicios de un contrato de status baja a status readonly
     * @conId es el identificador del contrato
     */
    public function reactiveServicesByContract($conId){
        global $log;
        $serviciosAfectados = [];
        $sql ="select * from servicio where contractId='$conId' and status IN('baja')";
        $this->Util()->DB()->setQuery($sql);
        $servicios = $this->Util()->DB()->GetResult();
        foreach ($servicios as $key=>$servicio) {
            $this->Util()->DB()->setQuery("update servicio set status='readonly' where servicioId ='" . $servicio["servicioId"] . "' ");
            $up = $this->Util()->DB()->UpdateData();
            if ($up > 0) {
                $log->saveHistoryChangesServicios($servicio["servicioId"], $servicio['inicioFactura'], "readonly", $servicio["costo"], $_SESSION["User"]["personalId"], $servicio["inicioOperaciones"], "", $this->inicioOperaciones["lastDateWorkflow"]);

                $this->Util()->DB()->setQuery("select a.*,b.nombreServicio,b.periodicidad from servicio a 
                                                     inner join tipoServicio b on a.tipoServicioId=b.tipoServicioId where servicioId ='" . $servicio["servicioId"] . "' ");
                $afterDetail = $this->Util()->DB()->GetRow();
                $this->Util()->DB()->setQuery("select * from servicio where servicioId ='" . $servicio["servicioId"] . "' ");
                $after = $this->Util()->DB()->GetRow();

                $log->setFecha(date('Y-m-d H:i:s'));
                $log->setTabla('servicio');
                $log->setTablaId($servicio["servicioId"]);
                $log->setAction('readonly');
                $log->setOldValue(serialize($servicio));
                $log->setNewValue(serialize($after));
                $log->SaveOnly();
                array_push($serviciosAfectados,$afterDetail);
            }
        }
        $this->resetDateLastProcessInvoice($conId);
        return $serviciosAfectados;
    }


    /*
     * Atualiza el costo del workflow del mes en que se edita(unicamente)
     * No actualiza el costo en la factura
     *
     */
    private function updatePriceInCurrentWorkflow($id, $costo) {
        $currentDate =  $this->Util()->getFirstDate(date('Y-m-d'));

        $query = "update instanciaServicio set costoWorkflow = '".$costo."'
                  where instanciaServicioId in(select instanciaServicioId from instanciaServicio 
                  where servicioId = '".$id."' and date = '".$currentDate."')";
        $this->Util()->DB()->setQuery($query);
        $this->Util()->DB()->UpdateData();
    }
}

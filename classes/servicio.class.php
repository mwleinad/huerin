<?php
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
			$this->Util()->setError(10055, 'error', '', 'Por favor, escoge un servicio');
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
		//$value = $this->Util()->FormatDateMySql($value);
		$this->inicioFactura = $value;
	}

	private $inicioOperaciones;
	public function setInicioOperaciones($value)
	{
		//$this->Util()->ValidateString($value, $max_chars=60, $minChars = 1, "Inicio Operaciones");
		//$value = $this->Util()->FormatDateMySql($value);
		$this->inicioOperaciones = $value;
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
	
	public function setContratosActivos($value){
		$this->contratosActivos = $value;
	}
  	public function OverwriteMonth($month){
	    $cad =array();
	    $add = '';
	    $monthNew="";

        switch($month){
            case 1:
                $add = "+6 month";
                $monthNew=7;
                break;
            case 2:
                $add = "+5 month";
                $monthNew=7;
                break;
            case 3:
                $add = "+4 month";
                $monthNew=7;
                break;
            case 4:
                $add = "+3 month";
                $monthNew=7;
                break;
            case 5:
                $add = "+2 month";
                $monthNew=7;
                break;
            case 6:
                $add = "+1 month";
                $monthNew=7;
                break;
            case 7:
                $monthNew=7;
                break;
            case 12://si ponen esta fecha pasa a ser del año siguiente
                $add = "+7 month";
                $monthNew=7;
                break;
            case 8:
                $add = "+1 month";
                $monthNew=9;
                break;
            case 9:
                $monthNew=9;
                break;
            case 10:
                $add = "+1 month";
                $monthNew=11;
                break;
            case 11:
                $monthNew=11;
                break;
        }
        if($monthNew>0 && $monthNew<10)
            $monthNew = "0".$monthNew;

        $cad['add']=$add;
        $cad['monthNew'] = $monthNew;
        return $cad;
    }
	public function CreateServiceInstances()
	{
	    //se usa EnumerateServiceForInstances por que no nos importa el usuario en este caos, todos los servicios saldra.
        //eso evitara foreachs en los permisos.
        $strLog="";
        $timeStart = date("d-m-Y").' a las '.date('H:i:s');
		$result = $this->EnumerateServiceForInstances();
		$totInstCreate=0;
		$excluidos =0;
		foreach($result as $key => $value)
		{
            $strLog .="----- INICIO DEL SERVICIO ".$value['nombreServicio']."=>".$value['servicioId']." DEL CLIENTE ".$value['razonSocialName']." -------".chr(13).chr(10);
			$strLog .=' Creacion de instancia del servicioId = '.$value['servicioId'].' con periodicidad '.$value['periodicidad'].chr(13).chr(10);
			$dateExploded = explode("-", $value["inicioOperaciones"]);
            $fechaOperacion = $dateExploded[0]."-".$dateExploded[1]."-01";
            //comprobar instancias
            $this->Util()->DB()->setQuery("SELECT instanciaServicioId FROM instanciaServicio
			WHERE servicioId = '".$value["servicioId"]."' AND status IN('activa','completa')  LIMIT 5");
            $instancias = $this->Util()->DB()->GetResult();
            $currentMonth = date('Y')."-".date("m")."-01";
            //si tiene instancias creadas comprobar que ya exista uno del mes en que se este ejecutando para excluirlo
            //ya no tiene por que iterarse si tiene una instancia en el mes actual
            if(!empty($instancias)){
                $this->Util()->DB()->setQuery("SELECT instanciaServicioId FROM instanciaServicio WHERE servicioId = '".$value["servicioId"]."' AND  status IN('activa','completa') AND date='".$currentMonth."' LIMIT 1");
                $exist = $this->Util()->DB()->GetRow();
                //excluir servicio si ya existe instancia en el mes actual
                if(!empty($exist))
                {
                    $excluidos++;
                    $strLog .=" SE EXCLUYE SERVICIO POR TENER INSTANCIA CREADA EN EL MES ACTUAL".chr(13).chr(10);
                    $strLog .="----- FIN DEL SERVICIO ".$value['servicioId']." -------".chr(13).chr(10).chr(13).chr(10);
                    continue;
                }
            }
			//Check if first instance
			if(empty($instancias))
			{	
				//si es precierre  debe abrir solo en los meses 7 9 11
			    if($value["tipoServicioId"]==PRECIERRE || $value["tipoServicioId"]==PRECIERREAUDITADO){
                    $strLog .= 'es PRECIERRE'.chr(13).chr(10);
			        $mesPre = (int)$dateExploded[1];
			        $monthMod = $this->OverwriteMonth($mesPre);
                    $dateExploded[1] =$monthMod['monthNew'];
                }
                //si es RIF y por alguna razon pusieron fecha de inicio operacion un mes impar se crea la primera instancia al primer mes par
                //posterior a la fecha de inicio de operaciones
                if($value["tipoServicioId"] == RIF || $value["tipoServicioId"] == RIFAUDITADO)
                {
                    $strLog .= 'es RIF';
                    if($dateExploded[1] % 2 == 1)
                    {
                        $dateExploded[1] = $dateExploded[1] + 1;
                        if($dateExploded[1]>0&&$dateExploded[1]<10)
                            $dateExploded[1] = "0".$dateExploded[1];
                        $strLog .= ' fecha impar cambiar ha:'.$dateExploded[0].'-'.$dateExploded[1].'-01';
                    }
                    $strLog .=chr(13).chr(10);

                }
                //crear primera instancia si ya es hora, lo define la fecha de inicio de operaciones.
                $initOp = $dateExploded[0].'-'.$dateExploded[1].'-01';
			    $dateNow=date('Y-m-d');
                if($dateNow>=$initOp){
                    $strLog .='Se crea la primera instancia del servicio = '.$value['servicioId'].' con fecha de inicio operacion='.$value["inicioOperaciones"].chr(13).chr(10);
                    $sql = "INSERT INTO  `instanciaServicio` (`servicioId`,`date`,`status`)
				VALUES ('".$value["servicioId"]."','".$initOp."','activa');";
                    $this->Util()->DB()->setQuery($sql);
                    $this->Util()->DB()->InsertData();
                    $strLog .=" ".trim($sql).chr(13).chr(10);
                    $totInstCreate++;
                }else{
                    $strLog .='No se crea la primera instancia del servicio = '.$value['servicioId'].' por  que '.date('Y-m-d').">=".$initOp." no se cumple ".chr(13).chr(10);
                    $strLog .="----- FIN DEL SERVICIO ".$value['servicioId']." -------".chr(13).chr(10).chr(13).chr(10);
                    continue;
                }
			}else{
				//Checamos si ya es tiempo de crear otra instancia
				//Checar ultima fecha de instancia
				$this->Util()->DB()->setQuery("
					SELECT date FROM instanciaServicio WHERE servicioId = '".$value["servicioId"]."' AND status IN ('activa','completa') ORDER BY date DESC LIMIT 1");
				$ultimoServicio = $this->Util()->DB()->GetSingle();
				switch($value["periodicidad"])
				{
					case "Mensual": $substract = "-1 month"; break;
					case "Bimestral": $substract = "-2 month"; break;
					case "Trimestral": $substract = "-3 month"; break;
					case "Semestral": $substract = "-6 month"; break;
					case "Anual": $substract = "-12 month"; break;
					case "Eventual":
                        // si es eventual, comprobar que en la fecha de inicioOperaciones tenga instancia creada
                        $strLog .="EL SERVICIO =".$value['servicioId']." ES EVENTUAL  con fecha de inicio operacion =".$value['inicioOperaciones'].chr(13).chr(10);
                       /* $this->Util()->DB()->setQuery("SELECT instanciaServicioId FROM instanciaServicio WHERE servicioId = '".$value["servicioId"]."'
                                                        AND status IN ('activa','completa')  AND date='".$fechaOperacion."' ");
                        $existInstancia = $this->Util()->DB()->GetSingle();
                        if($existInstancia)
                            $strLog .="Tiene instancia creada en la fecha =".$fechaOperacion.chr(13).chr(10);

					    //si no tiene instancia en la fecha de IO quiere decir que se modifico y que se requiere una instancia en la nueva fecha de IO
                        if(!$existInstancia&&$this->Util()->isValidateDate($fechaOperacion,'Y-m-d')) {
                            //si se va crear comprobar si ya es hora si la fecha actual sea mayor o igual a la fecha de  IO
                            $currDate =date('Y')."-".date('m')."-01";
                            if($currDate>=$fechaOperacion){
                                $strLog .= "Se creara instancia para el servicio eventual=" . $value['servicioId'] . chr(13) . chr(10);
                                $sql = "INSERT INTO  `instanciaServicio` (`servicioId`,`date`,`status`)VALUES ('" . $value["servicioId"] . "','".$fechaOperacion."','activa');";
                                $this->Util()->DB()->setQuery($sql);
                                $this->Util()->DB()->InsertData();
                                $strLog .= " " .trim($sql).chr(13).chr(10);
                                $totInstCreate++;
                            }else{
                                $strLog .='No se crea instancia del servicio = '.$value['servicioId'].' por que '.$currDate.">=".$fechaOperacion." no se cumple".chr(13).chr(10);
                            }
                        }*/
                        $strLog .="----- FIN DEL SERVICIO ".$value['servicioId']." -------".chr(13).chr(10).chr(13).chr(10);
					    continue 2;
					break;
				}
				$currentDate = date("Y-m-d");
				$newdate = strtotime ( $substract , strtotime ( $currentDate ) ) ;
				$newdate = date ( 'Y-m-d' , $newdate );
				//--------------------------------------------------------------------------
				$this->Util()->DB()->setQuery("
				SELECT date FROM instanciaServicio WHERE servicioId = '".$value["servicioId"]."' AND status IN ('activa','completa') ORDER BY date ASC LIMIT 1");
				$primerServicio = $this->Util()->DB()->GetSingle();
				$startdate=$dateExploded[0]."-".$dateExploded[1]."-01";

                $strLog .='Fecha inicio operacion(se toma el primer dia del mes)= '.$startdate.chr(13).chr(10);
				$strLog .='Primera instancia creada con fecha  = '.$primerServicio.chr(13).chr(10);
                $strLog .='Ultima instancia creada con fecha  = '.$ultimoServicio.chr(13).chr(10);
				if($primerServicio > $startdate)
				{
					switch($value["periodicidad"])
					{
						case "Mensual": $add = "+1 month"; break;
						case "Bimestral": $add = "+2 month"; break;
						case "Trimestral": $add = "+3 month"; break;
						case "Semestral": $add = "+6 month"; break;
						case "Anual": $add = "+12 month"; break;
					}
					$cont=1;
					//crea los workflows atrasados sucede si se cambia la fecha de inicio de operaciones.
                    $strLog .='Se crearan instancias atrasadas por cambio en la fecha de inicio de operaciones.'.chr(13).chr(10);
					while($primerServicio > $startdate)
					{
                        $strLog .=' Vuelta '.$cont."   ".$startdate.chr(13).chr(10);
						$dateExploded = explode("-",$startdate);
						if($value["tipoServicioId"] == RIF || $value["tipoServicioId"] == RIFAUDITADO )
						{    $strLog .= 'es RIF';
							if($dateExploded[1] % 2 == 1)
							{
								$dateExploded[1] = $dateExploded[1] + 1;
                                if($dateExploded[1]>0&&$dateExploded[1]<10)
                                 $dateExploded[1] = "0".$dateExploded[1];
                                $strLog .= ' feha impar cambiar ha:'.$dateExploded[0].'-'.$dateExploded[1].'-01';
							}
                            $strLog .=chr(13).chr(10);
						}
                        $addTemp =  $add;
                        if($value["tipoServicioId"]==PRECIERRE || $value["tipoServicioId"]==PRECIERREAUDITADO){
                            $mesPre = (int)$dateExploded[1];
                            $strLog .= 'es PRECIERRE'.chr(13).chr(10);
                            $dateMod = $this->OverwriteMonth($mesPre);
                            if($dateMod['add']!="")
                                $add =$dateMod['add'];
                            if($dateMod['monthNew']>0)
                                $dateExploded[1]=$dateMod['monthNew'];
                        }
                        $nextDate = $dateExploded[0]."-".$dateExploded[1]."-01";
						 $sql = "SELECT COUNT(*) FROM instanciaServicio WHERE servicioId = ".$value["servicioId"]." AND status IN ('activa','completa')
						 	AND date ='".$nextDate."' ";
						$this->Util()->DB()->setQuery($sql);
						$count = $this->Util()->DB()->GetSingle();

						if($count == 0) {
							$sql = "
								INSERT INTO  `instanciaServicio` (
									`servicioId` ,
									`date` ,
									`status`
								) VALUES (
									'".$value["servicioId"]."',
									'".$nextDate."',
								'activa')";
							$this->Util()->DB()->setQuery($sql);
							$this->Util()->DB()->InsertData();
                            $strLog .=' Instancia creada:'.chr(13).chr(10);
                            $strLog .='     '.trim($sql);
                            $strLog .=chr(13).chr(10);
                            $totInstCreate++;
						}else{
                            $strLog .=' Instancia no creada por tener existencia en la fecha : '.$nextDate.chr(13).chr(10);
                        }
						$startdate = strtotime ( $add , strtotime ( $startdate ) ) ;
						$startdate = date ( 'Y-m-d' , $startdate );
						$add=$addTemp;
						$cont++;
					}
				}
				//--------------------------------------------------------------------------
                $strLog .='Comprobar si se crearan instancias normales: '.$newdate.'>='.$ultimoServicio.chr(13).chr(10);
				if($newdate >= $ultimoServicio)
				{
				    $strLog .=" Si se creara".chr(13).chr(10);
					switch($value["periodicidad"])
					{
						case "Mensual": $add = "+1 month"; break;
						case "Bimestral": $add = "+2 month"; break;
						case "Trimestral": $add = "+3 month"; break;
						case "Semestral": $add = "+6 month"; break;
						case "Anual": $add = "+12 month"; break;
					}
					$addedDate = strtotime ( $add , strtotime ( $ultimoServicio ) ) ;
					$addedDate = date ( 'Y-m-d' , $addedDate );
					$explodedAddedDate = explode("-", $addedDate);

                    if($value["tipoServicioId"]==PRECIERRE || $value["tipoServicioId"]==PRECIERREAUDITADO){
                        $mesPreAdd = (int)$explodedAddedDate[1];
                        $strLog .= 'es PRECIERRE'.chr(13).chr(10);
                        $monthMod2 = $this->OverwriteMonth($mesPreAdd);
                        $explodedAddedDate[1] = $monthMod2['monthNew'];
                        //comprobar si ya debe crearse la instancia de precierre con su nueva fecha.
                        $datePrecierre = $explodedAddedDate[0]."-".$explodedAddedDate[1]."-01";
                        if(date("Y-m-d")<$datePrecierre){
                            $strLog.="No se crea instancia por que no se cumpple ".date('Y-m-d')."<".$datePrecierre.chr(13).chr(10);
                            $strLog .="----- FIN DEL SERVICIO ".$value['servicioId']." -------".chr(13).chr(10).chr(13).chr(10);
                            continue;
                        }
                    }
                    //comprobar RIF
                    if($value["tipoServicioId"] == RIF || $value["tipoServicioId"] == RIFAUDITADO )
                    {
                        $strLog .= 'es RIF';
                        if($explodedAddedDate[1] % 2 == 1)//este caso no deberia suceder debido a que se supone que desde la primera instancia se tomo en cuenta que empieze por un mes par
                        {
                            $explodedAddedDate[1] = $explodedAddedDate[1] + 1;
                            if($explodedAddedDate[1]>0&&$explodedAddedDate[1]<10)
                                $explodedAddedDate[1]="0".$explodedAddedDate[1];

                            $strLog .= ' fecha impar cambiar ha:'.$explodedAddedDate[0].'-'.$explodedAddedDate[1].'-01';
                        }
                        $strLog .=chr(13).chr(10);
                        $dateRif = $explodedAddedDate[0]."-".$explodedAddedDate[1]."-01";
                        if(date("Y-m-d")<$dateRif){
                            $strLog.="No se crea instancia por que no se cumpple ".date('Y-m-d')."<".$dateRif.chr(13).chr(10);
                            $strLog .="----- FIN DEL SERVICIO ".$value['servicioId']." -------".chr(13).chr(10).chr(13).chr(10);
                            continue;
                        }
                    }
                    $nextDate = $explodedAddedDate[0]."-".$explodedAddedDate[1]."-01";
                    $sql = "SELECT count(instanciaServicioId) FROM instanciaServicio WHERE
							servicioId = ".$value["servicioId"]." AND status IN ('activa','completa')
						 	AND date = '".$nextDate."'";
					$this->Util()->DB()->setQuery($sql);
					$count = $this->Util()->DB()->GetSingle();
					if($count == 0) {
						$sql = "
								INSERT INTO  `instanciaServicio` (
									`servicioId` ,
									`date` ,
									`status`
								) VALUES (
									'".$value["servicioId"]."',
									'".$nextDate."',
								'activa')";
						$this->Util()->DB()->setQuery($sql);
						$this->Util()->DB()->InsertData();
						$strLog .=' Instancia creada:'.chr(13).chr(10);
						$strLog .=' '.trim($sql).chr(13).chr(10);
                        $totInstCreate++;
					}else{
                        $strLog .=' Instancia no creada por tener existencia en la fecha'.$nextDate.chr(13).chr(10);
                    }
				}else{
                    $strLog .=" No se creara instancias consecutivas(normales)".chr(13).chr(10);
                }

			}
            $strLog .="----- FIN DEL SERVICIO ".$value['servicioId']." -------".chr(13).chr(10).chr(13).chr(10);
		}//foreach

        $time = date("d-m-Y").' a las '.date('H:i:s');
		//guardar el log en  sendFiles
        $strLog .=" TOTAL SERVICIOS EXCLUIDOS= ".$excluidos;
        $strLog .="TOTAL INSTANCIAS CREADAS =".$totInstCreate.chr(13).chr(10).chr(13).chr(10);
        $strLog .= "Cron ejecutado desde ".$timeStart." Hasta $time Hrs.";
        $file = DOC_ROOT."/sendFiles/logInstances.txt";
        $open = fopen($file,"w");
        if ( $open ) {
            fwrite($open,$strLog);
            fclose($open);
            //enviar por correo el log
            $sendmail = new SendMail;
            $sendmail->Prepare('LOG INSTANCES','Logs','isc061990@outlook.com','HBKRUZPE',$file,'logInstances.txt','','',FROM_MAIL);
        }
	}//CreateServiceInstances
	public function Enumerate()
	{
		global $months;
		
		$sql = "SELECT *,servicio.status,servicio.costo AS costo, tipoServicio.costoVisual, tipoServicio.mostrarCostoVisual 
				FROM servicio 
				LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
				LEFT JOIN contract ON contract.contractId = servicio.contractId
				WHERE servicio.contractId = '".$this->getContractId()."'					
				ORDER BY tipoServicio.nombreServicio ASC";
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();
		
		foreach($result as $key => $value)
		{
			$fecha = explode("-", $value["inicioOperaciones"]);
			$result[$key]["formattedInicioOperaciones"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];

			$fecha = explode("-", $value["inicioFactura"]);
			$result[$key]["formattedInicioFactura"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];
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
		
		//$debug = "servicioId = 5307 AND ";
		//$debug = '';
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
				WHERE ".$debug." servicio.status = 'activo' AND tipoServicio.status='1' AND customer.active = '1'
				".$sqlCustomer.$sqlContract.$addNomina.$depto.$sqlRespCta." 					
				ORDER BY clienteName, razonSocialName, nombreServicio ASC";						
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		if(count($User) == 1){
			$User["roleId"] = 1;
		}
		//echo $User["roleId"];
		
		foreach($result as $key => $value){
			//echo $value["customerId"];
			$filtro = new Filtro;
			$contract = new Contract;
			$data["conPermiso"] = $filtro->UsuariosConPermiso($value['permisos'], $value["responsableCuenta"]);
			$data["subordinados"] = $filtro->Subordinados($User["userId"]);

			$data["subordinadosPermiso"] = $filtro->SubordinadosPermiso($type, $data["subordinados"], $User["userId"]);
			
			$data["withPermission"] = $filtro->WithPermission($User["roleId"], $data["subordinadosPermiso"], $data["conPermiso"]);
			if($data["withPermission"] === false){
				unset($result[$key]);
				continue;
			}

			$result[$key]["responsableCuentaName"] = $result[$key]["responsableCuentaName"];
			$fecha = explode("-", $value["inicioOperaciones"]);
			$result[$key]["formattedInicioOperaciones"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];

			$this->Util()->DB()->setQuery("SELECT * FROM instanciaServicio
			WHERE servicioId = '".$value["servicioId"]."' AND status='activa' 	
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
     * no deberia filtrarse por que es una tarea automatica.
     * Solo deben sacar los servicios de las razones sociales de los clientes que se encuentren activos los que no no  debe sacar nada.
     * los contratos que tengan en su campo permisos vacio no debe sacarlos.
     */
    public function EnumerateServiceForInstances($customer = 0, $contract = 0, $rfc = "", $departamentoId="", $respCta = 0)
    {
        global $User;
        if($customer != 0)
            $sqlCustomer = " AND customer.customerId = '".$customer."'";

        if($contract != 0)
            $sqlContract = " AND contract.contractId = '".$contract."'";

        if(strlen($rfc) > 3 && $customer == 0 && $contract == 0)
            $sqlContract = " AND (customer.nameContact LIKE '%".$rfc."%' OR contract.name LIKE '%".$rfc."%')";

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
				INNER JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId  AND tipoServicio.status='1'
				INNER JOIN contract ON servicio.contractId = contract.contractId  AND contract.activo ='Si' AND contract.permisos!=''
				INNER JOIN customer ON contract.customerId = customer.customerId AND customer.active = '1'
				LEFT JOIN personal AS responsableCuenta ON  contract.responsableCuenta =responsableCuenta.personalId
				WHERE servicio.status = 'activo' 
				".$sqlCustomer.$sqlContract.$depto.$sqlRespCta." ";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        //se realizara esto para evitar que se creen instancias para contratos
        $User["userId"] = 0;
        $User["roleId"] = 1;
      /*
        foreach($result as $key => $value){
            $this->Util()->DB()->setQuery("SELECT instanciaServicioId FROM instanciaServicio
			WHERE servicioId = '".$value["servicioId"]."' AND status IN('activa','completa')  LIMIT 5");
            $instancias = $this->Util()->DB()->GetResult();
            $currentMonth = date('Y')."-".date("m")."-01";
           //si tiene instancias creadas comprobar que ya exista uno del mes en que se este ejecutando para excluirlo
           //ya no tiene por que iterarse si tiene una instancia en el mes actual
           if(!empty($instancias)){
                $this->Util()->DB()->setQuery("SELECT instanciaServicioId FROM instanciaServicio
			                                   WHERE servicioId = '".$value["servicioId"]."'
			                                   AND  status IN('activa','completa') AND date='".$currentMonth."' LIMIT 1");
              $exist = $this->Util()->DB()->GetRow();
              //excluir servicio si ya existe instancia en el mes actual
                if(!empty($exist))
                {
                   unset($result[$key]);
                   continue;
                }
            }
            $result[$key]["instancias"] =$instancias;
        }//foreach*/
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
	
	public function Historial()
	{
		$this->Util()->DB()->setQuery("SELECT historyChanges.*, personal.name FROM historyChanges 
		LEFT JOIN personal ON personal.personalId = historyChanges.personalId
		WHERE servicioId = '".$this->servicioId."'");
		$result = $this->Util()->DB()->GetResult();
		
		return $result;
	}	
	

  
	public function Edit()
	{
		global $User;
		
		if($this->Util()->PrintErrors()){ return false; }

		$infoServicio = $this->Info();
		
		$this->Util()->DB()->setQuery("
			UPDATE
				servicio
			SET
				`costo` = '".$this->costo."',
				`inicioFactura` = '".$this->inicioFactura."',
				`tipoServicioId` = '".$this->tipoServicioId."',
				`inicioOperaciones` = '".$this->inicioOperaciones."'
			WHERE servicioId = '".$this->servicioId."'");
		$this->Util()->DB()->UpdateData();
	
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
				'".$this->servicioId."',
				'".$this->inicioFactura."',
				'".$this->costo."',
				'".$User["userId"]."',
				'".$this->inicioOperaciones."'
		);");
		
		$this->Util()->DB()->InsertData();

		
		$subject = "El servicio de ".$infoServicio["nombreServicio"]." del la razon social ".$infoServicio["name"]." ha sido actualizado";

		$this->Util()->DB()->setQuery("SELECT * FROM personal WHERE personalId = 66 OR personalId = '".IDHUERIN."' OR (tipoPersonal = 'Gerente' && departamentoId = '1')");
		$personal = $this->Util()->DB()->GetResult();
		$sendmail = new SendMail();
		
		if($infoServicio["costo"] == $this->costo)
		{
			$this->Util()->setError(1, "complete");
			$this->Util()->PrintErrors();
			return true;
		}
		
		foreach($personal as $key => $personal)
		{
			$to = $personal["email"];
//			$to = "comprobantefiscal@braunhuerin.com.mx";
			$toName = $personal["name"];
			$body = "El servicio de ".$infoServicio["nombreServicio"]." del la razon social ".$infoServicio["name"]." ha sido actualizado<br>La actualization fue hecha por:".$_SESSION["User"]["username"]."<br>El costo era de ".$infoServicio["costo"]." el nuevo costo es de ".$this->costo;
			$sendmail->Prepare($subject, $body, $to, $toName, $destino, "", "", "");
		//	break;
		} 

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

	public function Delete()
	{
		global $User,$log;

		if($this->Util()->PrintErrors()){ return false; }
		
		$info = $this->Info();
		dd($info);
		
		if($info["status"] == 'activo')
		{
			$active = 'baja';
			$complete = "El servicio fue dado de baja correctamente";
		}
		else
		{
			$active = 'activo';
			$complete = "El servicio fue dado de alta correctamente";
		}
		
		$this->Util()->DB()->setQuery("
			UPDATE
				servicio
			SET status = '".$active."'
			WHERE
				servicioId = '".$this->servicioId."'");
		$this->Util()->DB()->UpdateData();


		$this->Util()->DB()->setQuery("
			SELECT * FROM
				servicio
			WHERE
				servicioId = '".$this->servicioId."'");
		$servicio = $this->Util()->DB()->GetRow();

        //Guardamos el Log
        $log->setPersonalId($User['userId']);
        $log->setFecha(date('Y-m-d H:i:s'));
        $log->setTabla('servicio');
        $log->setTablaId($this->servicioId);
        if($active=="activo")
            $log->setAction('Reactivacion');
        elseif($active=='baja')
            $log->setAction('Baja');

        $log->setOldValue(serialize($info));
        $log->setNewValue(serialize($servicio));
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
				'".$servicio["servicioId"]."',
				'".$servicio["inicioFactura"]."',
				'".$servicio["status"]."',
				'".$servicio["costo"]."',
				'".$User["userId"]."',
				'".$servicio["inicioFactura"]."'
		);");
		$this->Util()->DB()->InsertData();
		$this->Util()->setError(3, "complete", $complete);
		$this->Util()->PrintErrors();

		return true;
	}

	public function EnumerateActiveOnlyNames($contract = 0)
	{
		global $months, $User;

		if($contract != 0)
		{
			$sqlContract = " AND contract.contractId = '".$contract."'";
		}
		
		$this->Util()->DB()->setQuery("SELECT servicioId,  customer.nameContact AS clienteName, contract.name AS razonSocialName, nombreServicio, servicio.costo, inicioOperaciones, periodicidad, servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta, responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName, customer.customerId, customer.nameContact FROM servicio 
			LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
			LEFT JOIN contract ON contract.contractId = servicio.contractId
			LEFT JOIN customer ON customer.customerId = contract.customerId
			LEFT JOIN personal AS responsableCuenta ON responsableCuenta.personalId = contract.responsableCuenta
			WHERE servicio.status = 'activo' AND customer.active = '1'
			".$sqlCustomer.$sqlContract.$addNomina."					
			ORDER BY nombreServicio ASC");
		//$this->Util()->DB()->query;
		$result = $this->Util()->DB()->GetResult();
		foreach($result as $key => $value)
		{
			$user = new User;
			$user->setUserId($value["responsableCuenta"]);
			$userInfo = $user->Info();
			if(
				(in_array($User['roleId'],explode(',',ROLES_LIMITADOS))) &&
				($User["userId"] != $value["responsableCuenta"] && 
				$userInfo["jefeContador"] != $User["userId"] && 
				$userInfo["jefeSupervisor"] != $User["userId"] && 
				$userInfo["jefeGerente"] != $User["userId"] && 
				$userInfo["jefeSocio"] != $User["userId"])
			)
			{
				unset($result[$key]);
				continue;
			}
			
			$result[$key]["responsableCuentaName"] = $result[$key]["responsableCuentaName"];
//			echo $value["responsableCuenta"];
			$fecha = explode("-", $value["inicioOperaciones"]);
			$result[$key]["formattedInicioOperaciones"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];

		}
		
		return $result;
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

}

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
    /*
     * funcion CreateServiceInstances
     * Crear instancias para los servicios existentes
     * al comprobar instancia se toma en cuenta el status baja para casos en que se dieron de baja y no se requiera trabajar
     * de esta manera no se duplica o vuelve a crear la instancia correspondiente
     * si es precierre  debe abrir solo en los meses 6,8,10,12
     * si el servicio tiene bajaParcial se debe comprobar que la instancia a crear sea <= lastDateWorkflow
     * si es la primera instancia a crear no puede haber bajaParcial y si la hay se crea normal.
     */
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
		    $doCreate = true;
            $strLog .="----- INICIO DEL SERVICIO ".$value['nombreServicio']."=>".$value['servicioId']." DEL CLIENTE ".$value['razonSocialName']." -------".chr(13).chr(10);
			$strLog .=' Creacion de instancia del servicioId = '.$value['servicioId'].' con periodicidad '.$value['periodicidad'].chr(13).chr(10);
			//comprobar si la fecha de inicio de operaciones es valida de lo contrario no debe crear instancia.
            if(!$this->Util()->isValidateDate($value['inicioOperaciones'],'Y-m-d')) {
                $strLog .= "----- Fecha  no valida inicio operaciones =".$value['inicioOperaciones'].chr(13) . chr(10) . chr(13) . chr(10);
                $strLog .= "----- FIN DEL SERVICIO " . $value['servicioId'] . " -------" . chr(13) . chr(10) . chr(13) . chr(10);
                continue;
            }

			$dateExploded = explode("-", $value["inicioOperaciones"]);
            $fechaOperacion = $dateExploded[0]."-".$dateExploded[1]."-01";
            //comprobar instancias
            $this->Util()->DB()->setQuery("SELECT instanciaServicioId FROM instanciaServicio
			WHERE servicioId = '".$value["servicioId"]."' AND status IN('activa','completa','baja')  LIMIT 5");
            $instancias = $this->Util()->DB()->GetResult();
            $currentMonth = date('Y')."-".date("m")."-01";
            //si tiene instancias creadas comprobar que ya exista uno del mes en que se este ejecutando para excluirlo
            //ya no tiene por que iterarse si tiene una instancia en el mes actual
            //si la fecha de inicio de operaciones cambia aunque tenga instancia creada en el mes actual se tiene que iterar.
            if(!empty($instancias)){
                $this->Util()->DB()->setQuery("SELECT instanciaServicioId FROM instanciaServicio WHERE servicioId = '".$value["servicioId"]."' AND  status IN('activa','completa','baja') AND date='".$currentMonth."' LIMIT 1");
                $exist = $this->Util()->DB()->GetRow();

                //--------------------------------------------------------------------------
                $this->Util()->DB()->setQuery("
				SELECT date FROM instanciaServicio WHERE servicioId = '".$value["servicioId"]."' AND status IN ('activa','completa','baja') ORDER BY date ASC LIMIT 1");
                $firstWorkflow= $this->Util()->DB()->GetSingle();

                //excluir servicio si ya existe instancia en el mes actual y que tambien la condicion el primer workflow sea > que la de inicio de operaciones no se cumpla
                if(!empty($exist)&&!($firstWorkflow>$fechaOperacion))
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
			    if($value["tipoServicioId"]==PRECIERRE || $value["tipoServicioId"]==PRECIERREAUDITADO){
                    $strLog .= ' es PRECIERRE'.chr(13).chr(10);
                    $strLog .= ' FECHA SISTEMA : '.$dateExploded[0].'-'.$dateExploded[1].'-01'.chr(13).chr(10);
			        $mesPre = (int)$dateExploded[1];
			        $monthMod = $this->OverwriteMonth($mesPre);
                    $dateExploded[1] =$monthMod['monthNew'];
                    $strLog .= ' FECHA MODIFICADA : '.$dateExploded[0].'-'.$dateExploded[1].'-01'.chr(13).chr(10);
                }
                //si es RIF y por alguna razon pusieron fecha de inicio operacion un mes impar se crea la primera instancia al primer mes par
                //posterior a la fecha de inicio de operaciones
                if($value["tipoServicioId"] == RIF || $value["tipoServicioId"] == RIFAUDITADO)
                {
                    $strLog .= ' es RIF'.chr(13).chr(10);
                    $strLog .= ' FECHA SISTEMA : '.$dateExploded[0].'-'.$dateExploded[1].'-01'.chr(13).chr(10);
                    if($dateExploded[1] % 2 == 1)
                    {
                        $dateExploded[1] = $dateExploded[1] + 1;
                        if($dateExploded[1]>0&&$dateExploded[1]<10)
                            $dateExploded[1] = "0".$dateExploded[1];
                        $strLog .= ' FECHA MODIFICADA A :'.$dateExploded[0].'-'.$dateExploded[1].'-01';
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
				//Checar ultima fecha de instancia tomar en cuenta status= baja para que no se creen instancias de mas cuando no debe
				$this->Util()->DB()->setQuery("
					SELECT date FROM instanciaServicio WHERE servicioId = '".$value["servicioId"]."' AND status IN ('activa','completa','baja') ORDER BY date DESC LIMIT 1");
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
                                                        AND status IN ('activa','completa','baja')  AND date='".$fechaOperacion."' ");
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
				SELECT date FROM instanciaServicio WHERE servicioId = '".$value["servicioId"]."' AND status IN ('activa','completa','baja') ORDER BY date ASC LIMIT 1");
				$primerServicio = $this->Util()->DB()->GetSingle();
				$startdate=$dateExploded[0]."-".$dateExploded[1]."-01";

                $strLog .='Fecha inicio operacion(se toma el primer dia del mes)= '.$startdate.chr(13).chr(10);
				$strLog .='Primera instancia creada con fecha  = '.$primerServicio.chr(13).chr(10);
                $strLog .='Ultima instancia creada con fecha  = '.$ultimoServicio.chr(13).chr(10);
				if($primerServicio > $startdate)//solo sucede si se cambia la fecha de inicio de operaciones a una fecha inferior al primer workflow
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
						{
						    $strLog .='es RIF'.chr(13).chr(10);
                            $strLog .= ' FECHA SISTEMA : '.$dateExploded[0].'-'.$dateExploded[1].'-01'.chr(13).chr(10);
							if($dateExploded[1] % 2 == 1)
							{
								$dateExploded[1] = $dateExploded[1] + 1;
                                if($dateExploded[1]>0&&$dateExploded[1]<10)
                                 $dateExploded[1] = "0".$dateExploded[1];
                                $strLog .= ' feha impar cambiar ha:'.$dateExploded[0].'-'.$dateExploded[1].'-01';
							}
                            $strLog .= ' FECHA MODIFICADA A : '.$dateExploded[0].'-'.$dateExploded[1].'-01'.chr(13).chr(10);
                            $strLog .=chr(13).chr(10);
						}
                        $addTemp =  $add;
                        if($value["tipoServicioId"]==PRECIERRE || $value["tipoServicioId"]==PRECIERREAUDITADO){
                            $mesPre = (int)$dateExploded[1];
                            $strLog .= 'es PRECIERRE'.chr(13).chr(10);
                            $strLog .= ' FECHA SISTEMA : '.$dateExploded[0].'-'.$dateExploded[1].'-01'.chr(13).chr(10);
                            $dateMod = $this->OverwriteMonth($mesPre);
                            if($dateMod['add']!="")
                                $add =$dateMod['add'];
                            if($dateMod['monthNew']>0)
                                $dateExploded[1]=$dateMod['monthNew'];

                            $strLog .= ' FECHA MODIFICADA A : '.$dateExploded[0].'-'.$dateExploded[1].'-01'.chr(13).chr(10);
                        }
                        $nextDate = $dateExploded[0]."-".$dateExploded[1]."-01";
						 $sql = "SELECT COUNT(*) FROM instanciaServicio WHERE servicioId = ".$value["servicioId"]." AND status IN ('activa','completa','baja')
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
                    //comprobar precierres
                    if($value["tipoServicioId"]==PRECIERRE || $value["tipoServicioId"]==PRECIERREAUDITADO){
                        $mesPreAdd = (int)$explodedAddedDate[1];
                        $strLog .= 'es PRECIERRE'.chr(13).chr(10);
                        $strLog .= ' FECHA SISTEMA : '.$explodedAddedDate[0].'-'.$explodedAddedDate[1].'-01'.chr(13).chr(10);
                        $monthMod2 = $this->OverwriteMonth($mesPreAdd);
                        $explodedAddedDate[1] = $monthMod2['monthNew'];
                        $strLog .= ' FECHA MODIFICADA A : '.$explodedAddedDate[0].'-'.$explodedAddedDate[1].'-01'.chr(13).chr(10);
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
                        $strLog .= 'es RIF'.chr(13).chr(10);
                        $strLog .= ' FECHA SISTEMA : '.$explodedAddedDate[0].'-'.$explodedAddedDate[1].'-01'.chr(13).chr(10);
                        if($explodedAddedDate[1] % 2 == 1)//este caso no deberia suceder debido a que se supone que desde la primera instancia se tomo en cuenta que empieze por un mes par
                        {
                            $explodedAddedDate[1] = $explodedAddedDate[1] + 1;
                            if($explodedAddedDate[1]>0&&$explodedAddedDate[1]<10)
                                $explodedAddedDate[1]="0".$explodedAddedDate[1];

                            $strLog .= ' fecha impar cambiar ha:'.$explodedAddedDate[0].'-'.$explodedAddedDate[1].'-01';
                        }
                        $strLog .=chr(13).chr(10);
                        $strLog .= ' FECHA MODIFICADA A : '.$explodedAddedDate[0].'-'.$explodedAddedDate[1].'-01'.chr(13).chr(10);
                        $dateRif = $explodedAddedDate[0]."-".$explodedAddedDate[1]."-01";
                        if(date("Y-m-d")<$dateRif){
                            $strLog.="No se crea instancia por que no se cumpple ".date('Y-m-d')."<".$dateRif.chr(13).chr(10);
                            $strLog .="----- FIN DEL SERVICIO ".$value['servicioId']." -------".chr(13).chr(10).chr(13).chr(10);
                            continue;
                        }
                    }
                    // tomar en cuenta las intancias en status baja para no crear de nuevo si por alguna razon se dio de baja
                    $nextDate = $explodedAddedDate[0]."-".$explodedAddedDate[1]."-01";
                    $sql = "SELECT count(instanciaServicioId) FROM instanciaServicio WHERE
							servicioId = ".$value["servicioId"]." AND status IN ('activa','completa','baja')
						 	AND date = '".$nextDate."' ";
					$this->Util()->DB()->setQuery($sql);
					$count = $this->Util()->DB()->GetSingle();
                    // si tiene bajaParcial es un hecho que lastDateWorkflow es una fecha valida.
                    if($value['status']=='bajaParcial'){
                        $dateParcial = $this->Util()->getFirstDate($value['lastDateWorkflow']);
                        if($nextDate>$dateParcial){
                            $doCreate = false;
                            $strLog .="Servicio con baja temporal, ultima fecha valida ".$value['lastDateWorkflow']." => ".$dateParcial.chr(13).chr(10);
                        }
                    }
					if($count == 0&&$doCreate) {
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
                        $strLog .=' Instancia no creada por tener existencia en la fecha '.$nextDate." o es un servicio con baja temporal".chr(13).chr(10);
                    }
				}else{
                    $strLog .=" No se creara instancias consecutivas(normales)".chr(13).chr(10);
                }

			}
            $strLog .="----- FIN DEL SERVICIO ".$value['servicioId']." -------".chr(13).chr(10).chr(13).chr(10);
		}//foreach

        $time = date("d-m-Y").' a las '.date('H:i:s');
		//guardar el log en  sendFiles
        $strLog .="TOTAL SERVICIOS EXCLUIDOS= ".$excluidos;
        $strLog .="TOTAL INSTANCIAS CREADAS =".$totInstCreate.chr(13).chr(10).chr(13).chr(10);
        $strLog .= "Cron ejecutado desde ".$timeStart." Hasta $time Hrs.";
        $file = DOC_ROOT."/sendFiles/logInstances.txt";
        $open = fopen($file,"w");
        if ( $open ) {
            fwrite($open,$strLog);
            fclose($open);
            //enviar por correo el log solo si se crearon instancias.
            if($totInstCreate>0) {
                $sendmail = new SendMail;
                $sendmail->Prepare('LOG INSTANCES', 'Logs', 'isc061990@outlook.com', 'HBKRUZPE', $file, 'logInstances.txt', '', '', FROM_MAIL);
            }
        }
	}//CreateServiceInstances
	public function Enumerate()
	{
		global $months;
		
		$sql = "SELECT *,case 
                WHEN servicio.status = 'activo' THEN 'Activo'
                WHEN servicio.status = 'baja' THEN 'Baja'
                WHEN servicio.status = 'bajaParcial' THEN 'Baja temporal'
                WHEN servicio.status = 'readonly' THEN 'Activo / Solo lectura'
                END AS estado,servicio.status,servicio.costo AS costo, tipoServicio.costoVisual, tipoServicio.mostrarCostoVisual 
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
            $fecha = explode("-", $value["lastDateWorkflow"]);
            $result[$key]["formattedDateLastWorkflow"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];

            $result[$key]['dataJson'] =  json_encode($result[$key]);
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

        $sql = "SELECT servicioId,  customer.nameContact AS clienteName,servicio.status,servicio.lastDateWorkflow,
				contract.name AS razonSocialName, nombreServicio, servicio.costo, servicio.inicioOperaciones, periodicidad,
				servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta,servicio.inicioFactura ,
				responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName,
				customer.customerId, customer.nameContact, contract.permisos, responsableCuenta.tipoPersonal,tipoServicio.departamento,
				responsableCuenta.jefeContador, responsableCuenta.jefeSupervisor, responsableCuenta.jefeGerente, servicio.tipoServicioId, contract.activo
				FROM servicio 
				INNER JOIN (select a.*,b.departamento from tipoServicio a INNER JOIN departamentos b   ON a.departamentoId = b.departamentoId  where a.status='1') as tipoServicio ON servicio.tipoServicioId=tipoServicio.tipoServicioId
				INNER JOIN contract ON servicio.contractId = contract.contractId  AND contract.activo ='Si' AND contract.permisos!=''
				INNER JOIN customer ON contract.customerId = customer.customerId AND customer.active = '1'
				LEFT JOIN personal AS responsableCuenta ON  contract.responsableCuenta =responsableCuenta.personalId
				WHERE (servicio.status = 'activo' OR servicio.status ='bajaParcial')
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
				`lastDateCreateWorkflow` = '0000-00-00',
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
        global $log,$User;
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
        $sql ="select a.servicioId,b.nombreServicio,a.inicioFactura,a.inicioOperaciones,a.costo 
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
            //actualizar historial
            //actualizar historial del servicio
            $log->saveHistoryChangesServicios($value['servicioId'],$value['inicioFactura'],lcfirst($action),$value['costo'],$User['userId'],$value['inicioOperaciones'],$who);
            $servicioAfectado= $value;
            $servicioAfectado['ultimoWorkflow'] = $fechaWorkflow;
            $serviciosAfectados[]=$servicioAfectado;
        }
        if(!empty($serviciosAfectados)) {
            $filtros['sendBraun'] = false;
            $filtros['sendHuerin'] = true;
            $filtros['incluirJefes'] = true;
            $filtros['level'] = 3;
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
            $flw = $status=='bajaParcial'?$this->Util()->isValidateDate($_POST["lastDateWorkflow_$servId"],'d-m-Y')?$_POST["lastDateWorkflow_$servId"]:false:true;
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
                if($_POST["beforeStatus_$servId"]!=$_POST["status_$servId"]){
                    switch($_POST["status_$servId"]){
                        case 'activo':
                            $evento = "reactivacion";
                        break;
                        default:
                            $evento = $_POST["status_$servId"];
                        break;
                    }
                }else
                    $evento =  "modificacion";

                $log->saveHistoryChangesServicios($servId,$if,$evento,$costo,$_SESSION['User']['userId'],$io,'',$flw);
            }
        }
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
       $log->sendLogMultipleOperation($id_services,$conId,'new',$actuales);
	   $this->cleanItemsServices();
       $this->Util()->setError(0,'complete',"Se han guardado correctamente los servicios.");
       $this->Util()->PrintErrors();
	   return true;
    }
}

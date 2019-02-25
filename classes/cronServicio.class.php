<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 28/12/2018
 * Time: 11:44 AM
 */

class CronServicio extends Contract
{
    public function getListServices($customerId=0,$contractId=0){
        $fechaCorriente=  $this->Util()->getFirstDate(date('Y-m-d'));
        if($customerId != 0)
            $sqlCustomer = " AND d.customerId = '".$customerId."'";

        if($contractId != 0)
            $sqlContract = " AND c.contractId = '".$contractId."'";

        $sql =  "select a.servicioId,a.status,a.lastDateWorkflow,a.costo,a.inicioOperaciones,a.inicioFactura,a.contractId,b.periodicidad,b.nombreServicio,b.tipoServicioId from servicio a
                 inner join tipoServicio b on a.tipoServicioId=b.tipoServicioId  and b.status='1'
                 inner join contract c on a.contractId=c.contractId and c.activo='Si' $sqlContract
                 inner join customer d on c.customerId=d.customerId and d.active='1' $sqlCustomer
                 where a.status in('activo','bajaParcial') and a.inicioOperaciones!='0000-00-00' and a.lastDateCreateWorkflow<'".$fechaCorriente."' limit 300";
         $this->Util()->DB()->setQuery($sql);
         return $this->Util()->DB()->GetResult();
    }
    public function CreateStockDates($serv){
        global $servicio;
        $fechas_workflow = [];
        $fechaCorriente=  $this->Util()->getFirstDate(date('Y-m-d'));

        $sqlLast= "select max(date) from instanciaServicio where servicioId='".$serv['servicioId']."' ";
        $this->Util()->DB()->setQuery($sqlLast);
        $ultimoWorkflowCreado = $this->Util()->DB()->GetSingle();

        $isWorkflowInicial =  false;
        //si no tiene ultimo workflow se usa como ultimo la fecha de inicio de operaciones y se activa flag que indica que es el primer workflow que se creara
        if(!$ultimoWorkflowCreado)
        {
            $isWorkflowInicial = true;
            $siguienteWorkflow = $this->Util()->getFirstDate( $serv['inicioOperaciones']);
        }
        if(!$isWorkflowInicial) {
            $inicioOperaciones = $this->Util()->getFirstDate($serv['inicioOperaciones']);
            //si ya tiene una instancia y es eventual ya no debe existir meses a crear, se regresa vacio
            if($serv["periodicidad"]=="Eventual"){
                //si es eventual asegurar siempre que el unico workflow la fecha sea = que la fecha de IO
                $this->Util()->DB()->setQuery("select instanciaServicioId from instanciaServicio where servicioId='".$serv['servicioId']."' ");
                $eventualId = $this->Util()->DB()->GetSingle();
                $this->Util()->DB()->setQuery("update instanciaServicio set date='".$inicioOperaciones."' where instanciaServicioId='".$eventualId."' ");
                $this->Util()->DB()->UpdateData();
                return $fechas_workflow;
            }
            $isChangeDateIo =  false;
            $sqlFirst= "select min(date) from instanciaServicio where servicioId='".$serv['servicioId']."' ";
            $this->Util()->DB()->setQuery($sqlFirst);
            $primerWorkflowCreado = $this->Util()->DB()->GetSingle();
            if($this->Util()->isValidateDate($primerWorkflowCreado,'Y-m-d'))
            {
                $primerWorkflowCreado = $this->Util()->getFirstDate( $primerWorkflowCreado);
                //si primer workflow es mayor que  fecha inicio operaciones, $siguienteWorkflow=Fecha inicio operaciones
                if($primerWorkflowCreado>$inicioOperaciones)
                    $isChangeDateIo =  true;
                //limitar que la siguiente condicion aplique a solo anual
                if($serv['periodicidad']=='Anual'){
                    if($primerWorkflowCreado!=$inicioOperaciones)
                    $isChangeDateIo =  true;
                }
            }
            if(!$isChangeDateIo){
                //utilizando la periodicidad , encontrar fecha del proximo. solo si no es un workflow inicial y ademas la fecha inicial
                switch ($serv['periodicidad']) {
                    case "Mensual":
                        $add = "+1 month";
                        break;
                    case "Bimestral":
                        $add = "+2 month";
                        break;
                    case "Trimestral":
                        $add = "+3 month";
                        break;
                    case "Semestral":
                        $add = "+6 month";
                        break;
                    case "Anual":
                        $add = "+12 month";
                        break;
                }
                $siguienteWorkflow = strtotime($add, strtotime($ultimoWorkflowCreado));
                $siguienteWorkflow = date('Y-m-d', $siguienteWorkflow);
            }else{
                $siguienteWorkflow = $inicioOperaciones;
            }
        }
        //precierres y rifs deben evaluarse que se abran en el mes que se debe, precierres abre junio,agosto,octubre,diciembre
        //rif abre en meses pares
        if($serv["tipoServicioId"]==PRECIERRE || $serv["tipoServicioId"]==PRECIERREAUDITADO || $serv['tipoServicioId']==PRECIERREREVMENSUAL){
            $mesPre = (int)date('m',strtotime($siguienteWorkflow));
            $monthMod = $servicio->OverwriteMonth($mesPre);
            $fexplode = explode('-',$siguienteWorkflow);
            $siguienteWorkflow =$fexplode[0]."-".$monthMod['monthNew']."-01";
        }
        if($serv["tipoServicioId"] == RIF || $serv["tipoServicioId"] == RIFAUDITADO)
        {
            $fexplode = explode('-',$siguienteWorkflow);
            if((int)$fexplode[1] % 2 == 1)
            {
                $fexplode[1] = $fexplode[1] + 1;
                if($fexplode[1]>0&&$fexplode[1]<10)
                    $fexplode[1] = "0".$fexplode[1];
                $siguienteWorkflow =$fexplode[0]."-".$fexplode[1]."-01";
            }
        }
        //una ves encontrado los extremos, encontrar las fechas que se van a dar de alta,esto puede variar desde cero a muchos
        //los eventuales son por una sola vez, no tiene caso pasar por una busqueda, solo entraran al arreglo las fechas menores o iguales a $fechaCorriente
        if($serv['periodicidad']!='Eventual') {
            //si servicio tiene baja temporal , fecha de ultimo workflow pasa a ser $fechaCorriente
            if($serv['status']=='bajaParcial')
            {
                //si la fecha de lastDateWorkflow no es valida se debe por seguridad de no crear workflows, retornar array vacio
                if(!$this->Util()->isValidateDate($serv['lastDateWorkflow'],"Y-m-d"))
                    return $fechas_workflow;
                else{
                    $fechaLastWorkflow = $this->Util()->getFirstDate($serv['lastDateWorkflow']);
                    if($fechaLastWorkflow<=$fechaCorriente)
                        $fechaCorriente = $fechaLastWorkflow;
                }
            }
            //fomar array de fechas
            while ($siguienteWorkflow <= $fechaCorriente){
                //comprobar que no exista workflow si existe no tomarlo en cuenta en el array
                $strNext="select instanciaServicioId from instanciaServicio where servicioId='".$serv['servicioId']."' and date='".$siguienteWorkflow."' ";
                $this->Util()->DB()->setQuery($strNext);
                $existWorkwlow =  $this->Util()->DB()->GetSingle();
                if(!$existWorkwlow)
                    array_push($fechas_workflow, $siguienteWorkflow);

                switch ($serv['periodicidad']) {
                    case "Mensual":
                        $add = "+1 month";
                        break;
                    case "Bimestral":
                        $add = "+2 month";
                        break;
                    case "Trimestral":
                        $add = "+3 month";
                        break;
                    case "Semestral":
                        $add = "+6 month";
                        break;
                    case "Anual":
                        $add = "+12 month";
                        break;
                }
                $siguienteWorkflow = strtotime($add, strtotime($siguienteWorkflow));
                $siguienteWorkflow = date('Y-m-d', $siguienteWorkflow);
                if ($serv["tipoServicioId"] == PRECIERRE || $serv["tipoServicioId"] == PRECIERREAUDITADO || $serv['tipoServicioId']==PRECIERREREVMENSUAL) {
                    $mesPre = (int)date('m', strtotime($siguienteWorkflow));
                    $monthMod = $servicio->OverwriteMonth($mesPre);
                    $fexplode = explode('-', $siguienteWorkflow);
                    $siguienteWorkflow = $fexplode[0] . "-" . $monthMod['monthNew'] . "-01";
                }
                if ($serv["tipoServicioId"] == RIF || $serv["tipoServicioId"] == RIFAUDITADO) {
                    $fexplode = explode('-', $siguienteWorkflow);
                    if ((int)$fexplode[1] % 2 == 1) {
                        $fexplode[1] = $fexplode[1] + 1;
                        if ($fexplode[1] > 0 && $fexplode[1] < 10)
                            $fexplode[1] = "0" . $fexplode[1];
                        $siguienteWorkflow = $fexplode[0] . "-" . $fexplode[1] . "-01";
                    }
                }
            }//end while
        }else{
            array_push($fechas_workflow, $siguienteWorkflow);
        }
        return $fechas_workflow;
    }
    public function CreateWorkflow(){
        //encontrar los servicios sin necesidad de filtros
       $fechaCorriente=  $this->Util()->getFirstDate(date('Y-m-d'));

       $servicios = $this->getListServices();
       foreach($servicios as $key=>$servicio){
           $costoWorkflow = $servicio['costo'];
          $fechas_workflow =  $this->CreateStockDates($servicio);
          if(count($fechas_workflow)>0){
              foreach($fechas_workflow as $fecha){
                  $sql ="select instanciaServicioId from instanciaServicio where servicioId='".$servicio['servicioId']."' and date='".$fecha."' ";
                  $this->Util()->DB()->setQuery($sql);
                  $find =  $this->Util()->DB()->GetSingle();
                  if(!$find){
                     $sqlinser = "INSERT INTO  `instanciaServicio` (
									servicioId,
									date,
									status,
									costoWorkflow
								) VALUES (
									'".$servicio["servicioId"]."',
									'".$fecha."',
								'activa',
								'$costoWorkflow')";
                      $this->Util()->DB()->setQuery($sqlinser);
                      $id = $this->Util()->DB()->InsertData();
                      //guardar log de creacion de workflow
                      if($servicio['inicioFactura']!="0000-00-00"&&$this->Util()->isValidateDate($servicio['inicioFactura'],'Y-m-d'))
                          $seFactura = 'Si';
                      else
                          $seFactura = 'No';

                      $sqlLog= "insert into bitacora_create_workflow(
                                          contractId,
                                          servicioId,
                                          workflow_id,
                                          nombre_servicio,
                                          fecha_workflow,
                                          se_factura
                                      )values(
                                        '".$servicio['contractId']."',
                                        '".$servicio['servicioId']."',
                                        '".$id."',
                                        '".$servicio['nombreServicio']."',
                                        '".$fecha."',
                                        '".$seFactura."'
                                      )";

                    $this->Util()->DB()->setQuery($sqlLog);
                    $this->Util()->DB()->InsertData();
                  }

              }
          }
          //si se itera el servicio se debe actualizar el flag
          $sqlUpdate = "update servicio set lastDateCreateWorkflow='".$fechaCorriente."' where servicioId='".$servicio['servicioId']."' ";
          $this->Util()->DB()->setQuery($sqlUpdate);
          $this->Util()->DB()->UpdateData();
       }
    }

}
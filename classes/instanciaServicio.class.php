<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 17/01/2018
 * Time: 08:03 PM
 */
/*
     * los servicios domicilio fiscal(16),facturacion(17) y representacion legal fiel(24) saldran en reporte pero sin color
     * estos servicios no tienen seguimiento de sus workflows
     * Comprobar la fecha de inicio de operaciones para llevar control de que se mostrara.
     *  - Si la fecha de inicio de operaciones se modifico y este servicio ya tiene workflows creados en fechas anteriores al nuevo inicio de operaciones
     *        se debe ignorar las anteriores
     */
class InstanciaServicio extends  Servicio
{

    function getInstanciaByServicio($servicioId, $year,$foperaciones="0000-00-00")
    {

        $base = array(1=>array(),2=>array(),3=>array(),4=>array(),5=>array(),6=>array(),7=>array(),8=>array(),9=>array(),
            10=>array(),11=>array(),12=>array());
        $sinceMonth ="";
        if($foperaciones=="0000-00-00")
            return $base;

        $fecha =  explode('-',$foperaciones);
        //si año de IO es superior al año solicitado se ignora aunque tengan workflows creados.
        if(!($year>=$fecha[0]))
            return $base;
        //los meses que debe obtener debe ser apartir del mes de inicio de operaciones. esto es solo para el año de IO lo demas no debe evaluar esto
        if($year==$fecha[0])
            $sinceMonth = " and MONTH(instanciaServicio.date)>=".(int)$fecha[1];

        $sql = "SELECT 
                CASE tipoServicioId 
                WHEN 16 THEN ''
                WHEN 17 THEN ''
                WHEN 24 THEN ''
                ELSE
                class
                END 
                AS class
                ,MONTH(instanciaServicio.date) as mes,instanciaServicio.date as finstancia,instanciaServicioId, instanciaServicio.status, servicio.tipoServicioId
				FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE (MONTH(instanciaServicio.date) IN (1,2,3,4,5,6,7,8,9,10,11,12)  $sinceMonth)
				AND YEAR(instanciaServicio.date) = '".$year."'
				AND (servicio.status != 'baja'
      			OR servicio.status != 'inactiva')
				AND instanciaServicio.status != 'baja'		
				AND servicio.servicioId = '".$servicioId."'  ORDER BY instanciaServicio.instanciaServicioId DESC" ;
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();

        foreach($data as $key => $value)
        {
            $base[$value['mes']] =  $value;
        }
        return $base;
    }
    function getInstanciaByServicio12($servicioId, $year)
    {
        $base = array(1=>array(),2=>array(),3=>array(),4=>array(),5=>array(),6=>array(),7=>array(),8=>array(),9=>array(),
            10=>array(),11=>array(),12=>array());

        $sql = "CALL getInstancias(".$year.", ".$servicioId.",'all'); ";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResultPdo();
        foreach($data as $key => $value)
        {
            $base[$value['mes']] =  $value;
        }
        return $base;
    }
    function getInstanciaAtrasado12($servicioId,$year){

        $sql = "CALL getInstancias(".$year.", ".$servicioId.",'atrasados'); ";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();
        return $data;
    }
    function getInstanciaAtrasado($servicioId,$year){
        $sql = "SELECT 
                CASE tipoServicioId 
                    WHEN 16 THEN ''
                    WHEN 17 THEN ''
                    WHEN 24 THEN ''
                    ELSE
                    class
                END 
                AS class,
                MONTH(instanciaServicio.date) as mes,instanciaServicioId, instanciaServicio.status, servicio.tipoServicioId
				FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE MONTH(instanciaServicio.date) < MONTH(NOW())
				AND YEAR(instanciaServicio.date) = '".$year."'
				AND instanciaServicio.class IN ('PorIniciar','PorCompletar','Iniciado')
				AND (servicio.status != 'baja'
      			OR servicio.status != 'inactiva')
				AND instanciaServicio.status != 'baja'		
				AND servicio.servicioId = '".$servicioId."' GROUP BY MONTH(instanciaServicio.date) ORDER BY instanciaServicio.date";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();

        return $data;
    }
    function getOnlyAtrasados($servicioId){
        $sql = "SELECT 
                class,
                YEAR(instanciaServicio.date) as anio,
                MONTH(instanciaServicio.date) as mes,instanciaServicioId, instanciaServicio.status, servicio.tipoServicioId
				FROM instanciaServicio 
				INNER JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE (instanciaServicio.date >='2017-01-01' AND instanciaServicio.date<DATE(NOW()))
				AND instanciaServicio.class IN ('PorIniciar','PorCompletar','Iniciado')
				AND (servicio.status != 'baja'
      			OR servicio.status != 'inactiva')
				AND instanciaServicio.status != 'baja'		
				AND servicio.servicioId = '".$servicioId."' group by instanciaServicio.date ORDER BY YEAR(instanciaServicio.date) DESC, MONTH(instanciaServicio.date) ASC";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();

        return $data;
    }
    function getSumaBonoTrimestre($servicioId,$year,$meses=array()){
        $sql = "SELECT 
                sum(servicio.costo) as costoTotal
				FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE MONTH(instanciaServicio.date) IN (".implode(',',$meses).") AND YEAR(instanciaServicio.date)='".$year."'
				AND instanciaServicio.class IN ('CompletoTardio','Completo')
				AND (servicio.status != 'baja'
      			OR servicio.status != 'inactiva')
				AND instanciaServicio.status != 'baja'		
				AND servicio.servicioId = '".$servicioId."' GROUP BY servicio.servicioId";
        $this->Util()->DB()->setQuery($sql);
        $total =  $this->Util()->DB()->GetSingle();
        return $total;
    }
    function getBonoTrimestre($servicioId,$year,$meses=array()){
         $sql = "SELECT 
                class,
                servicio.costo,
                YEAR(instanciaServicio.date) as anio,
                MONTH(instanciaServicio.date) as mes,instanciaServicioId, instanciaServicio.status, servicio.tipoServicioId
				FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE MONTH(instanciaServicio.date) IN (".implode(',',$meses).") AND YEAR(instanciaServicio.date)='".$year."'
				AND (servicio.status != 'baja'
      			OR servicio.status != 'inactiva')
				AND instanciaServicio.status != 'baja'		
				AND servicio.servicioId = '".$servicioId."'  ORDER BY  instanciaServicio.instanciaServicioId DESC";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();

        $new = array();
        foreach($data as $key => $value)
        {
            switch($value['mes']){
                case 1:
                case 4:
                case 7:
                case 10:
                    $llave = 0; break;
                case 2:
                case 5:
                case 8:
                case 11:
                    $llave = 1; break;
                case 3:
                case 6:
                case 9:
                case 12:
                $llave = 2; break;
            }
            $new[$llave] =  $value;
        }
        return $new;
    }
}
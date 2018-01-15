<?php
/**
 * Created by PhpStorm.
 * User: HECTOR CRUZ
 * Date: 10/01/2018
 * Time: 12:30 PM
 */

class InstanciaServicio extends  Servicio
{
    function getInstanciaByServicio($servicioId, $year)
    {
        $sql = "SELECT 
                CASE tipoServicioId 
                WHEN 16 THEN ''
                WHEN 34 THEN ''
                WHEN 24 THEN ''
                WHEN 27 THEN ''
                ELSE
                class
                END 
                AS class
                ,MONTH(instanciaServicio.date) as mes,instanciaServicioId, instanciaServicio.status, servicio.tipoServicioId
				FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE MONTH(instanciaServicio.date) IN (1,2,3,4,5,6,7,8,9,10,11,12) 
				AND YEAR(instanciaServicio.date) = '".$year."'
				AND (servicio.status != 'baja'
      			OR servicio.status != 'inactiva')
				AND instanciaServicio.status != 'baja'		
				AND servicio.servicioId = '".$servicioId."'";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();

        $new = array();
        foreach($data as $key => $value)
        {
            $new[$value['mes']] =  $value;
        }
        return $new;
    }
    function getInstanciaAtrasado($servicioId,$year){
        $sql = "SELECT 
                class,
                MONTH(instanciaServicio.date) as mes,instanciaServicioId, instanciaServicio.status, servicio.tipoServicioId
				FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE MONTH(instanciaServicio.date) <= 12
				AND YEAR(instanciaServicio.date) = '".$year."'
				AND instanciaServicio.class IN ('PorIniciar','PorCompletar')
				AND (servicio.status != 'baja'
      			OR servicio.status != 'inactiva')
				AND instanciaServicio.status != 'baja'		
				AND servicio.servicioId = '".$servicioId."'";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();

        return $data;
    }
    function getOnlyAtrasados($servicioId){
        $sql = "SELECT 
                CASE tipoServicioId 
                WHEN 16 THEN ''
                WHEN 34 THEN ''
                WHEN 24 THEN ''
                WHEN 27 THEN ''
                ELSE
                class
                END 
                AS class,
                YEAR(instanciaServicio.date) as anio,
                MONTH(instanciaServicio.date) as mes,instanciaServicioId, instanciaServicio.status, servicio.tipoServicioId
				FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE (instanciaServicio.date >='2015-01-01' AND instanciaServicio.date<DATE(NOW()))
				AND instanciaServicio.class IN ('PorIniciar','PorCompletar')
				AND (servicio.status != 'baja'
      			OR servicio.status != 'inactiva')
				AND instanciaServicio.status != 'baja'		
				AND servicio.servicioId = '".$servicioId."' ORDER BY YEAR(instanciaServicio.date) DESC, MONTH(instanciaServicio.date) ASC";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();

        return $data;
    }
}
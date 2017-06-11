<?php

class Archivos extends Servicio {

    private $FILES_ROOT = '/var/www/sistema/';

    function creaEstructura() {
        
        echo "AHORA SI LA WA CREAR"; exit(0);
        $result = $this->GetActiveMio();

        foreach ($result as $contract) {
            if (!$this->createDir($this->FILES_ROOT . $contract['rfc'])) {
                echo "Error al crear directorio " . $contract['rfc'] . " o directorio ya existente.<br>";
            }

            foreach ($contract['instancias'] as $instancia) {
                
                $instancia['dateExploded'][1] = $instancia['dateExploded'][1]+0;
                
                if (!$this->createDir($this->FILES_ROOT . $contract['rfc'] . "/" . $instancia['dateExploded'][0])) {
                    echo "Error al crear directorio año o directorio ya existente<br>";
                }
                
                if ($this->checkDir($this->FILES_ROOT . $contract['rfc'] . "/" . $instancia['dateExploded'][0] . "/" . $instancia['dateExploded'][1] )) {
                    if (!$this->createDir($this->FILES_ROOT . $contract['rfc'] . "/" . $instancia['dateExploded'][0] . "/" . $instancia['dateExploded'][1] . "/" . $instancia['instanciaServicioId'] . "_" . $instancia['nombreServicio'])) {
                        echo "Error al crear la carpeta del servicio " . $instancia['instanciaServicioId'] . "_" . $instancia['nombreServicio']."<br>";
                    }
                } else {
                    if (!$this->createDir($this->FILES_ROOT . $contract['rfc'] . "/" . $instancia['dateExploded'][0] . "/" . $instancia['dateExploded'][1])) {
                        echo "Error al crear directorio del mes " . $instancia['dateExploded'][1] . " o directorio ya existente<br>";
                    }
                    if (!$this->createDir($this->FILES_ROOT . $contract['rfc'] . "/" . $instancia['dateExploded'][0] . "/" . $instancia['dateExploded'][1] . "/" . $instancia['instanciaServicioId'] . "_" . $instancia['nombreServicio'])) {
                        echo "Error al crear la carpeta del servicio " . $instancia['instanciaServicioId'] . "_" . $instancia['nombreServicio']."<br>";
                    }
                }
                
                $query = "SELECT * FROM step WHERE servicioId = ".$instancia['tipoServicioId'];
                $this->Util()->DB()->setQuery($query);
                $pasos = $this->Util()->DB()->GetResult();
                
                foreach($pasos as $paso){
                    if (!$this->createDir($this->FILES_ROOT . $contract['rfc'] . "/" . $instancia['dateExploded'][0] . "/" . $instancia['dateExploded'][1] . "/" . $instancia['instanciaServicioId'] . "_" . $instancia['nombreServicio']."/".$paso['stepId']."_".$paso['nombreStep'])) {
                        echo "Error al crear la carpeta del paso<br>";
                    }
                    
                    $query = "SELECT * FROM task WHERE stepId = " . $paso['stepId'];
                    $this->Util()->DB()->setQuery($query);
                    $tasks = $this->Util()->DB()->GetResult();

                    foreach ($tasks as $task) {
                        if (!$this->createDir($this->FILES_ROOT . $contract['rfc'] . "/" . $instancia['dateExploded'][0] . "/" . $instancia['dateExploded'][1] . "/" . $instancia['instanciaServicioId'] . "_" . $instancia['nombreServicio']."/".$paso['stepId']."_".$paso['nombreStep'] . "/" . $task['taskId'] . "_" . $task['nombreTask'])) {
                            echo "Error al crear directorio de la tarea o directorio ya creado<br>";
                        }
                    }
                }
                if($this->checkDir($this->FILES_ROOT . $contract['rfc'] . "/" . $instancia['dateExploded'][0] . "/" . $instancia['dateExploded'][1] . "/" . $instancia['instanciaServicioId'] . "_" . $instancia['nombreServicio'])){
                    $query = "UPDATE instanciaServicio SET carpeta = 1 WHERE instanciaServicioId = " . $instancia['instanciaServicioId'];
                    $this->Util()->DB()->setQuery($query);
                }
            }
        }

        return $result;
    }

    public function createDir($value) {
        $validate = false;

        if (!file_exists($value)) {
            mkdir($value);
            $validate = true;
        }

        return $validate;
    }

    public function checkDir($value) {

        if (file_exists($value)) {
            return true;
        }

        return false;
    }

    public function GetActiveMio($customer = 0, $contract = 0, $rfc = "") {
        global $months, $User;

        if ($customer != 0) {
            $sqlCustomer = " AND customer.customerId = '" . $customer . "'";
        }

        if ($contract != 0) {
            $sqlContract = " AND contract.contractId = '" . $contract . "'";
        }

        if (strlen($rfc) > 3 && $customer == 0 && $contract == 0) {
            $sqlContract = " AND (customer.nameContact LIKE '%" . $rfc . "%' OR contract.name LIKE '%" . $rfc . "%')";
        }

        if ($User["subRoleId"] == "Nomina") {
            $addNomina = " AND servicio.tipoServicioId IN (" . SERVICIOS_NOMINA . ")";
        }


        $this->Util()->DB()->setQuery("SELECT rfc, servicioId,  customer.nameContact AS clienteName, contract.name AS razonSocialName, nombreServicio, servicio.costo, inicioOperaciones, periodicidad, servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta, responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName, customer.customerId, customer.nameContact FROM servicio 
			LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
			LEFT JOIN contract ON contract.contractId = servicio.contractId
			LEFT JOIN customer ON customer.customerId = contract.customerId
			LEFT JOIN personal AS responsableCuenta ON responsableCuenta.personalId = contract.responsableCuenta
			WHERE servicio.status = 'activo' AND customer.active = '1'
			" . $sqlCustomer . $sqlContract . $addNomina . "					
			ORDER BY clienteName, razonSocialName, nombreServicio ASC");
        //$this->Util()->DB()->query;
        $result = $this->Util()->DB()->GetResult();
        foreach ($result as $key => $value) {
            $user = new User;
            $user->setUserId($value["responsableCuenta"]);
            $userInfo = $user->Info();
            if (
                    ($User["roleId"] > 2 && $User["roleId"] < 4) &&
                    ($User["userId"] != $value["responsableCuenta"] &&
                    $userInfo["jefeContador"] != $User["userId"] &&
                    $userInfo["jefeSupervisor"] != $User["userId"] &&
                    $userInfo["jefeGerente"] != $User["userId"] &&
                    $userInfo["jefeSocio"] != $User["userId"])
            ) {
                unset($result[$key]);
                continue;
            }

            $result[$key]["responsableCuentaName"] = $result[$key]["responsableCuentaName"];
//			echo $value["responsableCuenta"];
            $fecha = explode("-", $value["inicioOperaciones"]);
            $result[$key]["formattedInicioOperaciones"] = $fecha[2] . "/" . $months[$fecha[1]] . "/" . $fecha[0];

            $query = "SELECT instanciaServicioId, instanciaServicio.servicioId, date, servicio.tipoServicioId, tipoServicio.nombreServicio FROM instanciaServicio
                        LEFT JOIN servicio ON(servicio.servicioId = instanciaServicio.servicioId)
                        LEFT JOIN tipoServicio ON(tipoServicio.tipoServicioId = servicio.tipoServicioId)
			WHERE date > '2017-12-31' AND carpeta = 0 AND instanciaServicio.servicioId = '" . $value["servicioId"] . "'	
			ORDER BY date DESC";

            $this->Util()->DB()->setQuery($query);
            $result[$key]["instancias"] = $this->Util()->DB()->GetResult();


            foreach ($result[$key]["instancias"] as $keyInstancias => $valueInstancias) {
                $result[$key]["instancias"][$keyInstancias]["dateExploded"] = explode("-", $valueInstancias["date"]);
                $result[$key]["instancias"][$keyInstancias]["monthShow"] = $months[$result[$key]["instancias"][$keyInstancias]["dateExploded"][1]] . " " . $result[$key]["instancias"][$keyInstancias]["dateExploded"][0];
            }

            //$fecha = explode("-", $value["inicioFactura"]);
            //$result[$key]["formattedInicioFactura"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];
        }

        return $result;
    }

}

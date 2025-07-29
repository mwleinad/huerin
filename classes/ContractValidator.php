<?php
class ContractValidator extends Util {
    private $errors = [];
    private $contract;
    
    public function __construct($contract) {
        $this->contract = $contract;
    }

    public function validate() {
        $permisos = $_POST['permisos'] ?? [];  // Obtener directamente del POST
        if(empty($permisos)) {
            // si no hay permisos, no se puede validar,es valido por defecto
            return true;
        }

        // Crear un mapa de departamento => personal para fácil acceso
        $departamentoPersonalMap = [];
        foreach($permisos as $permiso) {
            list($departamentoId, $personalId) = explode(',', $permiso);
            $departamentoPersonalMap[$departamentoId] = $personalId;
        }

        // Validar Contabilidad/Fiscal
        $contabilidadId = $this->getDepartamentoId('Contabilidad e Impuestos');
        $fiscalId = $this->getDepartamentoId('Fiscal');

        
        if(isset($departamentoPersonalMap[$contabilidadId]) && 
           !isset($departamentoPersonalMap[$fiscalId])) {
            $this->errors[] = "El responsable del departamento de Fiscal es requerido si el de Contabilidad e Impuestos está asignado";
        }

        // Validar Nóminas/Gestoría
        $nominasId = $this->getDepartamentoId('Nominas y Seguridad Social');
        $gestoriaId = $this->getDepartamentoId('Gestoria');
        if(isset($departamentoPersonalMap[$nominasId]) && 
           !isset($departamentoPersonalMap[$gestoriaId])) {
            $this->errors[] = "El responsable del departamento de Gestoría es requerido si el de Nóminas y Seguridad Social está asignado";
        }   
        
        foreach(DEPARTAMENTOS_TIPO_GERENCIA as $depto) {
            $principalId = $this->getDepartamentoId($depto['principal']);
            $secundarioId = $this->getDepartamentoId($depto['secundario']);

            if(isset($departamentoPersonalMap[$secundarioId]) && 
               !isset($departamentoPersonalMap[$principalId])) {
                $this->errors[] = "El responsable del departamento ".$depto['principal']." es requerido si el departamento ".$depto['secundario']." está asignado";
            }
        }

        if($this->contract['contractId'] > 0) {
             // Validar servicios y sus departamentos requeridos
            $servicios = $this->getServiciosContratados(); 
            $departamentosQueRequierenResponsable = array_column($servicios, 'departamentoId');
            $departamentosQueRequierenResponsable = array_unique($departamentosQueRequierenResponsable);
            foreach($departamentosQueRequierenResponsable as $departamentoId) {
                if(!isset($departamentoPersonalMap[$departamentoId])) {
                    $nombreDepartamento = $this->getNombreDepartamento($departamentoId);
                    $this->errors[] = "El responsable del departamento ".$nombreDepartamento." es requerido cuando tenga servicios asignados del mismo";
                }
            }
        }
       

        return empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }

    // Métodos auxiliares
    private function getDepartamentoId($nombre) {
        $this->Util()->DB()->setQuery("SELECT departamentoId FROM departamentos WHERE departamento = '".$nombre."' LIMIT 1");
        $result = $this->Util()->DB()->GetRow();
        return $result['departamentoId'];
    }

    private function getNombreDepartamento($deptoId) {
        $this->Util()->DB()->setQuery("SELECT departamento FROM departamentos WHERE departamentoId = ".$deptoId." LIMIT 1");
        $result = $this->Util()->DB()->GetRow();
        return $result['departamento'];
    }

    private function getServiciosContratados() {

        global $customer;
        return $customer->GetServiciosActivosById($this->contract['contractId']);
    }
}
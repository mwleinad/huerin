<?php

class Company extends Main
{
    private $id;

    public function setId($value)
    {
        $this->Util()->ValidateInteger($value);
        $this->id = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    private $prospect_id;
    public function setProspectId($value)
    {
        $this->Util()->ValidateInteger($value);
        $this->prospect_id = $value;
    }

    public function getProspectId()
    {
        return $this->prospect_id;
    }

    private $name;

    public function setName($value)
    {
        $this->Util()->ValidateRequireField($value, "Nombre o razon social");
        $this->name = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    private $phone;

    public function setPhone($value)
    {
        $this->Util()->ValidateRequireField($value, "Telefono");
        $this->phone = $value;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    private $email;

    public function setEmail($value)
    {
        $this->Util()->ValidateRequireField($value, "Email");
        $this->Util()->ValidateMail($value, "Email");
        $this->email = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    private $observation;

    public function setObservation($value)
    {
        $this->observation = $value;
    }

    public function getObservation()
    {
        return $this->observation;
    }

    private $legal_representative;

    public function setLegalRepresentative($value)
    {
        $this->legal_representative = $value;
    }

    public function getLegalRepresentative()
    {
        return $this->legal_representative;
    }

    private $business_activity;

    public function setBusinessActivity($value)
    {
        $this->Util()->ValidateRequireField($value, "Giro o actividad principal");
        $this->business_activity = $value;
    }

    public function getBusinessActivity()
    {
        return $this->business_activity;
    }

    private $regimen_id;

    public function setRegimenId($value)
    {
        $this->Util()->ValidateRequireField($value, "Régimen de contribución");
        $this->regimen_id = $value;
    }

    public function getRegimenId()
    {
        return $this->regimen_id;
    }

    private $constitution_date;

    public function setConstitutionDate($value)
    {
        if($this->Util()->ValidateRequireField($value, "Fecha de constitución"))
            if($this->Util()->validateDateFormat($value, 'Fecha de constitución', 'd-m-Y'))
                $this->constitution_date = $this->Util()->FormatDateMySql($value);

    }

    public function getConstitutionDate()
    {
        return $this->constitution_date;
    }

    private $is_new_company;

    public function setIsNewCompany($value)  {
        $this->is_new_company = $value;
    }

    public function isNewCompany()
    {
        return $this->is_new_company;
    }

    private $rfc;

    public function setRfc($value)
    {
        $this->Util()->ValidateRequireField($value, "RFC");
        $this->rfc = $value;
    }

    public function getRfc()
    {
        return $this->rfc;
    }

    public function info()
    {
        $sQuery = "select * from company 
                   where id = '" . $this->id . "' ";
        $this->Util()->DB()->setQuery($sQuery);
        $row = $this->Util()->DB()->GetRow();

        if($row)
            $row['services'] = $this->serviceByCompany();

        return $row;
    }

    public function enumerate()
    {
        $this->Util()->DB()->setQuery("SELECT COUNT(*) FROM company where deleted_at is null and prospect_id = '".$this->prospect_id."' ");
        $total = $this->Util()->DB()->GetSingle();

        $pages = $this->Util->HandleMultipages($this->page, $total, WEB_ROOT . "/company");

        $sql_add = "LIMIT " . $pages["start"] . ", " . $pages["items_per_page"];
        $sQuery = "select  * from company a 
                             where deleted_at is null
                             and prospect_id = '".$this->prospect_id."' order by created_at desc  " . $sql_add;
        $this->Util()->DB()->setQuery($sQuery);
        $result = $this->Util()->DB()->GetResult();
        $data["items"] = $result;
        $data["pages"] = $pages;
        return $data;
    }

    public function save()
    {
        if ($this->Util()->PrintErrors())
            return false;

        $sql = "INSERT INTO company(
                    prospect_id,
                    name,
                    is_new_company,
                    constitution_date,
                    rfc,
                    email,
                    phone,
                    legal_representative,
                    observation   
                ) VALUES (
                    '" . $this->prospect_id . "',
                    '" . $this->name . "', 
                    '" . $this->is_new_company . "',
                    '" . $this->constitution_date . "',
                    '" . $this->rfc . "',
                    '" . $this->email . "',
                    '" . $this->phone . "',
                    '" . $this->legal_representative . "',
                    '" . $this->observation . "'
                 )";
        $this->Util()->DB()->setQuery($sql);
        $lastId = $this->Util()->DB()->InsertData();

        $this->assocServiceToCompany($lastId, $_POST['services']);

        $this->Util()->setError(0, "complete", "Registro guardado");
        $this->Util()->PrintErrors();
        return true;
    }
    private function serviceByCompany() {
        $sql =  "select * from company_service where company_id = '".$this->id."'";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }

    public function assocServiceToCompany ($id, $data = []) {
        $this->setId($id);
        $currentServices = $this->serviceByCompany();
        $currentServicesLineal =  count($currentServices) ? array_column($currentServices, 'service_id'): [];
        $postServices = count($_POST['services']) ? $_POST['services'] : [];
        $diff = array_diff($currentServicesLineal, $postServices);

        $count = 0;
        $sql =  "insert into company_service(company_id, service_id) values";
        $compQuery = "";
        foreach($_POST['services'] as $val) {
            if (!in_array($val, $currentServicesLineal)) {
                $compQuery .=  "(".$id.", ".$val."),";
                $count++;
            }
        }
        if ($count > 0) {
            $compQuery =  substr($compQuery, 0, -1);
            $sql = $sql . $compQuery;
            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->InsertData();

        }
        $this->removeServiceFromCompany($id, $diff);

    }
    private function removeServiceFromCompany($id, $data = []) {
        if (count($data)) {
            $sql = "delete from company_service 
                    where service_id IN(".implode(',', $data).")
                    and company_id = '".$id."' ";
            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->DeleteData();
        }
    }

    public function update()
    {
        if ($this->Util()->PrintErrors())
            return false;

        $sql = "UPDATE company set 
                    name = '".$this->name."',
                    is_new_company = '".$this->is_new_company."',
                    constitution_date = '".$this->constitution_date."',
                    rfc = '".$this->rfc."',
                    email = '".$this->email."',
                    phone = '".$this->phone."',
                    legal_representative = '".$this->legal_representative."',
                    observation = '".$this->observation."'   
                    WHERE id = '".$this->id."' ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();
        $this->assocServiceToCompany($this->id, $_POST['services']);
        $this->Util()->setError(0, "complete", "Registro actualizado");
        $this->Util()->PrintErrors();
        return true;
    }
}

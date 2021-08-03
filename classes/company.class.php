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

    private $tax_purpose;

    public function setTaxPurpose($value)
    {
        $this->Util()->ValidateRequireField($value, "Tipo de persona");
        $this->tax_purpose = $value;
    }

    public function getTaxPurpose()
    {
        return $this->tax_purpose;
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
        if ($this->Util()->ValidateRequireField($value,
            $this->tax_purpose === 'moral' ? "Fecha de constitución" : 'Fecha de alta en el SAT'))
            if ($this->Util()->validateDateFormat($value, 'Fecha de constitución', 'd-m-Y'))
                $this->constitution_date = $this->Util()->FormatDateMySql($value);

    }

    public function getConstitutionDate()
    {
        return $this->constitution_date;
    }

    private $is_new_company;

    public function setIsNewCompany($value)
    {
        $this->is_new_company = $value;
    }

    public function isNewCompany()
    {
        return $this->is_new_company;
    }

    private $rfc;

    public function setRfc($value)
    {
        $this->rfc = $value;
    }

    public function getRfc()
    {
        return $this->rfc;
    }

    public function info($withQuoteComplete = false)
    {
        $sQuery = "select * from company 
                   where id = '" . $this->id . "' ";
        $this->Util()->DBProspect()->setQuery($sQuery);
        $row = $this->Util()->DBProspect()->GetRow();

        if ($row)
            $row['services'] = $this->serviceByCompany($withQuoteComplete);

        return $row;
    }

    public function enumerate()
    {
        $sql = "SELECT COUNT(*) FROM company
                 WHERE deleted_at is null and prospect_id = '" . $this->prospect_id . "' ";
        $this->Util()->DBProspect()->setQuery($sql);
        $total = $this->Util()->DBProspect()->GetSingle();

        $pages = $this->Util->HandleMultipages($this->page, $total, WEB_ROOT . "/company");

        $sql_add = "LIMIT " . $pages["start"] . ", " . $pages["items_per_page"];
        $sQuery = "select  * from company a 
                             where deleted_at is null
                             and prospect_id = '" . $this->prospect_id . "' order by created_at desc  " . $sql_add;
        $this->Util()->DBProspect()->setQuery($sQuery);
        $result = $this->Util()->DBProspect()->GetResult();
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
                    tax_purpose,
                    name,
                    taxpayer_id,
                    legal_representative,
                    activity_id,
                    regimen_id,
                    step_id,
                    date_constitution,
                    is_new_company,
                    comment,
                    contract_id,
                    created_at,
                    updated_at
                ) VALUES (
                    '" . $this->prospect_id . "',
                    '" . $this->tax_purpose . "',
                    '" . $this->name . "', 
                    '" . $this->rfc . "',
                    '" . $this->legal_representative . "',
                    '" . $this->business_activity . "',
                    '" . $this->regimen_id . "',
                    1,
                    '" . $this->constitution_date . "',
                    '" . $this->is_new_company . "',
                    '" . $this->observation . "',
                    '" . $_POST['contract_exists'] . "',
                    now(),
                    now()
                 )";
        $this->Util()->DBProspect()->setQuery($sql);
        $lastId = $this->Util()->DBProspect()->InsertData();

        $this->assocServiceToCompany($lastId, $_POST['services']);

        $this->Util()->setError(0, "complete", "Registro guardado");
        $this->Util()->PrintErrors();
        return true;
    }

    private function serviceByCompany($quoteComplete = false)
    {
        $sql = "select * from company_service
                inner join service on company_service.service_id = service.id
                where company_service.company_id = '" . $this->id . "' ";
        $this->Util()->DBProspect()->setQuery($sql);
        $result = $this->Util()->DBProspect()->GetResult();
        foreach ($result as $key => $val) {
            $result[$key]['quote_id'] = $this->getQuoteByService($val['company_id'], $val['service_id'], $quoteComplete);
        }
        return $result;
    }

    private function getQuoteByService($companyId, $serviceId, $quoteComplete = false)
    {
        $sql = "select * from quotation 
                where deleted_at is null and company_id = '" . $companyId . "' and service_id = '" . $serviceId . "'";
        $this->Util()->DBProspect()->setQuery($sql);
        $row = $this->Util()->DBProspect()->GetRow();
        return $quoteComplete ? $row : $row['id'];
    }

    public function assocServiceToCompany($id, $data = [])
    {
        $this->setId($id);
        $currentServices = $this->serviceByCompany();
        $currentCompany = $this->info();
        $currentServicesLineal = count($currentServices) ? array_column($currentServices, 'service_id') : [];
        $diff1 = array_diff($data, $currentServicesLineal);
        $diff2 = array_diff($currentServicesLineal, $data);
        $changeService = count($diff1) === 0 && count($diff2) === 0 ? false : true;
        $count = 0;
        $sql = "insert into company_service(company_id, service_id, created_at, updated_at) values";
        $sqlProspect = "insert into prospect_service(prospect_id, service_id) values";
        $compQuery = "";
        $compProspect = "";
        foreach ($data as $val) {
            $compQuery .= "(" . $id . ", " . $val . ", now(), now()),";
            $compProspect .= "(" . $currentCompany['prospect_id'] . ", " . $val . "),";
            $count++;

        }
        if ($count > 0) {
            $this->removeServiceFromCompany($currentCompany);
            $compQuery = substr($compQuery, 0, -1);
            $compProspect = substr($compProspect, 0, -1);
            $sql = $sql . $compQuery;
            $this->Util()->DBProspect()->setQuery($sql);
            $this->Util()->DBProspect()->InsertData();

            $sqlProspect = $sqlProspect . $compProspect;
            $this->Util()->DBProspect()->setQuery($sqlProspect);
            $this->Util()->DBProspect()->InsertData();

        }
        if ($changeService) {
            $sql = "update company set step_id = 1 where id = '" . $id . "' ";
            $this->Util()->DBProspect()->setQuery($sql);
            $this->Util()->DBProspect()->UpdateData();
        }
    }

    private function removeServiceFromCompany($dataCompany)
    {
        $sql = "delete from company_service 
                    where company_id = '" . $dataCompany['id'] . "' ";
        $this->Util()->DBProspect()->setQuery($sql);
        $this->Util()->DBProspect()->DeleteData();

        $sql = "delete from prospect_service 
                    where prospect_id = '" . $dataCompany['prospect_id'] . "' ";
        $this->Util()->DBProspect()->setQuery($sql);
        $this->Util()->DBProspect()->DeleteData();

    }

    public function update()
    {
        if ($this->Util()->PrintErrors())
            return false;
        $activity = 'activity_id = ' . ($this->business_activity ? $this->business_activity : 'NULL') . ',';
        $regimen = 'regimen_id = ' . ($this->regimen_id ? $this->regimen_id : 'NULL') . ',';
        $sql = "UPDATE company set 
                    tax_purpose = '" . $this->tax_purpose . "',
                    name = '" . $this->name . "',
                    taxpayer_id = '" . $this->rfc . "',
                    legal_representative = '" . $this->legal_representative . "',
                    " . $activity . "
                    " . $regimen . "
                    date_constitution = '" . $this->constitution_date . "',
                    is_new_company = '" . $this->is_new_company . "',
                    comment = '" . $this->observation . "',  
                    contract_id = '" . $_POST['contract_exists'] . "',                
                    updated_at = now()
                    WHERE id = '" . $this->id . "' ";
        $this->Util()->DBProspect()->setQuery($sql);
        $this->Util()->DBProspect()->UpdateData();
        $this->assocServiceToCompany($this->id, $_POST['services']);
        $this->Util()->setError(0, "complete", "Registro actualizado");
        $this->Util()->PrintErrors();
        return true;
    }

    public function processSendToMain()
    {
        $params = [];
        array_push($params, ['type' =>'i', 'value' => $this->id]);
        $sql = "select company.*   from company
                inner join prospect on company.prospect_id = prospect.id
                where company.id = ? ";
        $this->Util()->DBProspect()->PrepareStmtQuery($sql, $params);
        $row = $this->Util()->DBProspect()->GetStmtRow();
        dd($row);
    }

}

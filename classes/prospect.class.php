<?php

class Prospect extends Main
{
    private $id;
    public function setId($value) {
        $this->Util()->ValidateInteger($value);
        $this->id = $value;
    }
    public function getId() {
        return $this->id;
    }

    private $name;
    public function setName($value) {
        $this->Util()->ValidateRequireField($value, "Nombre o razon social");
        $this->name = $value;
    }
    public function getName() {
        return $this->name;
    }

    private $legal_representative;
    public function setLegalRepresentative($value) {
        $this->Util()->ValidateRequireField($value, "Nombre o razon social");
        $this->legal_representative = $value;
    }
    public function getLegalRepresentative() {
        return $this->legal_representative;
    }

    private $business_activity;
    public function setBusinessActivity($value) {
        $this->Util()->ValidateRequireField($value, "Giro o actividad principal");
        $this->business_activity = $value;
    }
    public function getBusinessActivity() {
        return $this->business_activity;
    }

    private $regimen_id;
    public function setRegimenId($value) {
        $this->Util()->ValidateRequireField($value, "Régimen de contribución");
        $this->regimen_id = $value;
    }
    public function getRegimenId() {
        return $this->regimen_id;
    }

    private $constitution_date;
    public function setConstitutionDate($value) {
        $this->Util()->ValidateRequireField($value, "Fecha de constitución");
        $this->constitution_date = $value;
    }
    public function getConstitutionDate() {
        return $this->constitution_date;
    }

    private $is_new_company;
    public function setIsNewCompany($value) {
        $this->Util()->ValidateRequireField($value, "Es empresa de nueva creación");
        $this->is_new_company = $value;
    }
    public function isNewCompany() {
        return $this->is_new_company;
    }

    private $leaflet_email;
    public function setLeafletEmail($value) {
        $this->Util()->ValidateRequireField($value, "Correo electronico");
        $this->leaflet_email = $value;
    }
    public function getLeafletEmail() {
        return $this->leaflet_email;
    }

    private $contact_name;
    public function setContactName($value) {
        $this->Util()->ValidateRequireField($value, "Nombre de contacto");
        $this->contact_name = $value;
    }
    public function getContactName() {
        return $this->contact_name;
    }

    private $contact_phone;
    public function setContactPhone($value) {
        $this->Util()->ValidateRequireField($value, "Telefono de contacto");
        $this->contact_phone = $value;
    }
    public function getContactPhone() {
        return $this->contact_phone;
    }

    private $contact_email;
    public function setContactEmail($value) {
        $this->Util()->ValidateRequireField($value, "Correo de contacto");
        $this->contact_email = $value;
    }
    public function getContactEmail() {
        return $this->contact_email;
    }

    private $general_comment;
    public function setGeneralComment($value) {
        $this->general_comment = $value;
    }
    public function getGeneralComment() {
        return $this->general_comment;
    }

    private $sale_amount_per_month;
    public function setSaleAmountPerMonth($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->contact_email = $value;
    }
    public function getSaleAmountPerMonth() {
        return $this->contact_email;
    }

    private $number_account_bank;
    public function setNumberAccountBank($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->number_account_bank = $value;
    }
    public function getNumberAccountBank() {
        return $this->number_account_bank;
    }

    private $invoice_per_month;
    public function setInvoicePerMonth($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->invoice_per_month = $value;
    }
    public function getInvoicePerMonth() {
        return $this->invoice_per_month;
    }

    private $deposit_per_month;
    public function setDepositPerMonth($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->deposit_per_month = $value;
    }
    public function getDepositPerMonth() {
        return $this->deposit_per_month;
    }

    private $transfer_per_month;
    public function setTransferPerMonth($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->transfer_per_month = $value;
    }
    public function getTransferPerMonth() {
        return $this->transfer_per_month;
    }

    private $expense_per_month;
    public function setExpensePerMonth($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->expense_per_month = $value;
    }
    public function getExpensePerMonth() {
        return $this->expense_per_month;
    }

    private $account_comment;
    public function setAccountComment($value) {
        $this->account_comment = $value;
    }
    public function getAccountComment() {
        return $this->expense_per_month;
    }

    private $have_payroll;
    public function setHavePayroll($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->have_payroll = $value;
    }
    public function getHavePayroll() {
        return $this->have_payroll;
    }

    private $number_employee;
    public function setNumberEmployee($value) {
        $this->number_employee = $value;
    }
    public function getNumberEmployee() {
        return $this->number_employee;
    }

    private $required_help_make_receipt_payroll;
    public function setRequiredHelpMakeReceiptPayroll($value) {
        $this->required_help_make_receipt_payroll = $value;
    }
    public function getRequiredHelpMakeReceiptPayroll() {
        return $this->required_help_make_receipt_payroll;
    }
    private $payroll_comment;
    public function setPayrollComment($value) {
        $this->payroll_comment = $value;
    }
    public function getPayrollComment() {
        return $this->payroll_comment;
    }

    private $required_formulation_financial_state;
    public function setRequiredFormulationFinancialState($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->required_formulation_financial_state = $value;
    }
    public function getRequiredFormulationFinancialState() {
        return $this->required_formulation_financial_state;
    }

    private $required_formulation_specific_report;
    public function setRequiredFormulationSpecificReport($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->required_formulation_specific_report = $value;
    }
    public function getRequiredFormulationSpecificReport() {
        return $this->required_formulation_specific_report;
    }

    private $required_meeting;
    public function setRequiredMeeting($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->required_meeting = $value;
    }
    public function getRequiredMeeting() {
        return $this->required_meeting;
    }

    private $financial_comment;
    public function setFinancialComment($value) {
        $this->financial_comment = $value;
    }
    public function getFinancialComment() {
        return $this->financial_comment;
    }

    private $periodic_check_fiscal_mailbox;
    public function setPeriodicCheckFiscalMailbox($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->periodic_check_fiscal_mailbox = $value;
    }
    public function getPeriodicCheckFiscalMailbox() {
        return $this->periodic_check_fiscal_mailbox;
    }

    private $specific_help_tax_obligation;
    public function setSpecificHelpTaxObligation($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->specific_help_tax_obligation = $value;
    }
    public function getSpecificHelpTaxObligation() {
        return $this->specific_help_tax_obligation;
    }

    private $specific_transact_tax;
    public function setSpecificTransactTax($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->specific_transact_tax = $value;
    }
    public function getSpecificTransactTax() {
        return $this->specific_transact_tax;
    }

    private $administrative_comment;
    public function setAdministrativeComment($value) {
        $this->administrative_comment = $value;
    }
    public function getAdministrativeComment() {
        return $this->administrative_comment;
    }

    private $expectation;
    public function setExpectation($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->expectation = $value;
    }
    public function getExpectation() {
        return $this->expectation;
    }

    private $other_service;
    public function setOtherService($value) {
        $this->Util()->ValidateRequireField($value, "");
        $this->other_service = $value;
    }
    public function getOtherService() {
        return $this->other_service;
    }

    private $quality_comment;
    public function setQualityComment($value) {
        $this->quality_comment = $value;
    }
    public function getQualityComment() {
        return $this->quality_comment;
    }

    public function info() {
        $sQuery = "select a.*, b.nombreRegimen from prospect a 
                   inner join tipoRegimen b on a.regimen_id = b.tipoRegimenId 
                   where a.id = '".$this->id."' ";
        $this->Util()->DB()->setQuery($sQuery);
        return $this->Util()->DB()->GetRow();
    }
    public function enumerate() {
        $this->Util()->DB()->setQuery('SELECT COUNT(*) FROM prospect');
        $total = $this->Util()->DB()->GetSingle();

        $pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/prospect");

        $sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];
        $sQuery = "select * from prospect
                   where 1 order by create_at desc ". $sql_add;
        $this->Util()->DB()->setQuery($sQuery);
        $result = $this->Util()->DB()->GetResult();
        $data["items"] = $result;
        $data["pages"] = $pages;
        return $data;
    }

    public function create(){
        if($this->Util()->PrintErrors())
            return false;
        $sql  ="INSERT INTO leaflet (
                    name,
                    legal_representative,
                    business_activity,
                    regimen_id,
                    constitution_date,
                    is_new_company,
                    leaflet_email,
                    contact_name,
                    contact_phone,
                    contact_email,
                    general_comment,
                    sale_amount_per_month,
                    number_account_bank,
                    invoice_per_month,
                    deposit_per_month,
                    transfer_per_month,
                    expense_per_month,
                    account_comment,
                    have_payroll,
                    number_employee,
                    required_help_make_receipt_payroll,
                    payroll_comment,
                    required_formulation_financial_state,
                    required_formulation_specific_report,
                    required_meeting,
                    financial_comment,
                    periodic_check_fiscal_mailbox,
                    specific_help_tax_obligation,
                    specific_transact_tax,
                    administrative_comment,
                    expectation,
                    other_service,
                    quality_comment    
                )VALUES(
                '".$this->name."',
                '".$this->legal_representative."',
                '".$this->business_activity."',
                '".$this->regimen_id."',
                '".$this->constitution_date."',
                '".$this->is_new_company."',
                '".$this->leaflet_email."',
                '".$this->contact_name."',
                '".$this->contact_phone."',
                '".$this->contact_email."',
                '".$this->general_comment."',
                '".$this->sale_amount_per_month."',
                '".$this->number_account_bank."',
                '".$this->invoice_per_month."',
                '".$this->deposit_per_month."',
                '".$this->transfer_per_month."',
                '".$this->expense_per_month."',
                '".$this->account_comment."',
                '".$this->have_payroll."',
                '".$this->number_employee."',
                '".$this->required_help_make_receipt_payroll."',
                '".$this->payroll_comment."',
                '".$this->required_formulation_financial_state."',
                '".$this->required_formulation_specific_report."',
                '".$this->required_meeting."',
                '".$this->financial_comment."',
                '".$this->periodic_check_fiscal_mailbox."',
                '".$this->specific_help_tax_obligation."',
                '".$this->specific_transact_tax."',
                '".$this->administrative_comment."',
                '".$this->expectation."',
                '".$this->other_service."',
                '".$this->quality_comment."'
                ) ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->InsertData();

        $this->Util()->setError(0,"complete","Se ha enviado tus datos en breve le notificaremos el resultado.");
        $this->Util()->PrintErrors();
        return true;
    }

}

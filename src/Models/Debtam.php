<?php 
namespace Barinulka\Parser\Models;

class Debtam 
{
    public function __construct(
        private int $id,                        // ID
        private string $tax_name,               // НаимНалог
        private string $org_name,               // НаимОрг
        private string $inn,                    // ИННЮЛ
        private string $sum_arrears,            // СумНедНалог
        private string $sum_penalties,          // СумПени
        private string $sum_ticket,             // СумШтраф
        private string $total_sum_arrears,      // ОбщСумНед
        private string $created_date            // Дата
    ){
    }

    public function id() :int 
    {
        return $this->id;
    }

    public function taxName() :string
    {
        return $this->tax_name;
    }

    public function setTaxName(string $taxName) :void 
    {
        $this->tax_name = $taxName;
    }

    public function orgName() :string
    {
        return $this->org_name;
    }

    public function setOrgName(string $orgName) :void 
    {
        $this->org_name = $orgName;
    }

    public function inn() :string
    {
        return $this->inn;
    }

    public function setInn(string $inn) :void 
    {
        $this->inn = $inn;
    }

    public function sumArrears() :string
    {
        return $this->sum_arrears;
    }

    public function setSumArrears(string $sumArrears) :void 
    {
        $this->sum_arrears = $sumArrears;
    }

    public function sumPenalties() :string
    {
        return $this->sum_penalties;
    }

    public function setSumPenalties(string $sumPenalties) :void 
    {
        $this->sum_penalties = $sumPenalties;
    }

    public function sumTicket() :string
    {
        return $this->sum_ticket;
    }

    public function setSumTicket(string $sumTicket) :void 
    {
        $this->sum_ticket = $sumTicket;
    }

    public function totalSumArrears() :string
    {
        return $this->total_sum_arrears;
    }

    public function setTotalSumArrears(string $totalSumArrears) :void 
    {
        $this->total_sum_arrears = $totalSumArrears;
    }

    public function createdDate() :string
    {
        return $this->created_date;
    }

    public function setCreatedDate(string $createdDate) :void 
    {
        $this->created_date = $createdDate;
    }

}
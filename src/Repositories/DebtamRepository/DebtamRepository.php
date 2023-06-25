<?php
namespace Barinulka\Parser\Repositories\DebtamRepository;

use PDO;
use PDOStatement;
use Barinulka\Parser\Models\Debtam;
use Barinulka\Parser\Exceptions\DebtamNotFoundException;

class DebtamRepository implements DebtamRepositoryInterface 
{

    public function __construct(
        private PDO $connection
    ){
    }

    public function get(int $id): Debtam
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM debtam WHERE id = :id'
        );

        $statement->execute([
            ':id' => (int)$id,
        ]);

        return $this->getDebtam($statement, $id);
    }

    public function save(Debtam $debtam): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO debtam (tax_name, org_name, inn, sum_arrears, sum_penalties, sum_ticket, total_sum_arrears, created_date) 
                VALUES (:tax_name, :org_name, :inn, :sum_arrears, :sum_penalties, :sum_ticket, :total_sum_arrears, :created_date)'
        );

        $statement->execute([
            ':tax_name' => $debtam->taxName(), 
            ':org_name' => $debtam->orgName(), 
            ':inn' => $debtam->inn(), 
            ':sum_arrears' => $debtam->sumArrears(), 
            ':sum_penalties' => $debtam->sumPenalties(), 
            ':sum_ticket' => $debtam->sumTicket(), 
            ':total_sum_arrears' => $debtam->totalSumArrears(), 
            ':created_date' => $debtam->createdDate()
        ]);
    }

    private function getDebtam(PDOStatement $statement, int $id) :Debtam
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new DebtamNotFoundException(
                "Невозможно найти запись: $id"
            );
        }

        // Создаём объект
        return new Debtam(
            $result['id'],                    
            $result['tax_name'],          
            $result['org_name'],         
            $result['inn'],                 
            $result['sum_arrears'],       
            $result['sum_penalties'],      
            $result['sum_ticket'],         
            $result['total_sum_arrears'],  
            $result['created_date']   
        );
    }
}
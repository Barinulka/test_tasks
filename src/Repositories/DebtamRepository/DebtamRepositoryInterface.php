<?php 
namespace Barinulka\Parser\Repositories\DebtamRepository;

use Barinulka\Parser\Models\Debtam;

interface DebtamRepositoryInterface
{
    public function get(int $id) :Debtam;

    public function save(Debtam $debtam) :void;
}
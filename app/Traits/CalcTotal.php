<?php

namespace App\Traits;

trait CalcTotal
{
    private function getTotal(string $column)
    {
        $ids = $this->getSelected();
        if ($ids == []) {
            $this->setAllSelected();
            $ids = $this->getSelected();
            // dump($ids);
            $this->clearSelected();
        }
        $sum_expr = "sum($column) as sum";
        $sum_total =  $this->model::query()
            ->whereIn($this->getPrimaryKey(), $ids)
            ->selectraw($sum_expr)->get()[0]->sum;

        return number_format($sum_total, 2, '.', ',');

    }

}
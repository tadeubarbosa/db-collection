<?php

namespace DBCollection\Infra\Commands\Query;

class Delete implements QueryOperator
{
    use QueryData;

    /** @var string */
    private $table;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function __toString(): string
    {
        $where = $this->getWhereData();

        return "DELETE FROM `{$this->table}`{$where};";
    }
}
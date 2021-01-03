<?php

namespace DBCollection\Infra\Commands\Query;

class Insert implements QueryOperator
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
        $values = $this->getDataFormatted();

        return "INSERT INTO `{$this->table}`{$values};";
    }
}
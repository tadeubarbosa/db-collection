<?php

namespace DBCollection\Infra\Commands\Query;

class Update implements QueryOperator
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
        return "UPDATE `{$this->table}`;";
    }
}
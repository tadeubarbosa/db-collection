<?php

namespace DBCollection\Infra\Commands\Query;

class Update implements QueryOperator
{
    /** @var string */
    private $table;

    public function __construct(string $table)
    {
        $this->table = $table;
    }
}
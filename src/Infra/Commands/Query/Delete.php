<?php

namespace DBCollection\Infra\Commands\Query;

class Delete implements QueryOperator
{
    /** @var string */
    private $table;

    public function __construct(string $table)
    {
        $this->table = $table;
    }
}
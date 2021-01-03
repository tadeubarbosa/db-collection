<?php

namespace DBCollection\Infra\Commands\Query;

interface QueryOperator
{
    public function __construct(string $table);
}
<?php

namespace DBCollection\Infra\Commands\Query;

class QueryArgumentNull extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
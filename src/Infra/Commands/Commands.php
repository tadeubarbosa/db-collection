<?php

namespace DBCollection\Infra;

use DBCollection\Infra\Commands\Query\Query;

class Commands
{
    /** @var string */
    private $table;

    public function table(string $table): void
    {
        $this->table = $table;
    }

    public function query(): Query
    {
        if ($this->table === null) {
            throw new \InvalidArgumentException(
                "You must set the table name before call query method! Call `table(\$tableName)` method."
            );
        }

        return new Query($this->table);
    }
}
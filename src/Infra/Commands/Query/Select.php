<?php

namespace DBCollection\Infra\Commands\Query;

use InvalidArgumentException;

class Select extends SelectBase implements QueryOperator
{

    public function __toString(): string
    {
        $columns = $this->getColumns();
        $where = $this->getDataFormatted();
        $limit = $this->getLimit();

        return "SELECT {$columns} FROM `{$this->table}`{$where}{$limit};";
    }

    public function columns($columns): self
    {
        if (is_array($columns) && count($columns) === 0) {
            throw new InvalidArgumentException("You are trying to pass an empty array as column value!");
        }

        if (is_array($columns)) {
            $this->columns = array_merge($this->columns, $columns);
            return $this;
        }

        if (!$columns) {
            throw new InvalidArgumentException("The column passed must be an valid string or an array.");
        }

        $this->columns[] = $columns;
        return $this;
    }

    public function count(string $column = null): self
    {
        $this->count = true;
        $this->countColumn = $column;

        return $this;
    }

    public function limit(int $first, int $last = null): self
    {
        $start = $last? $first: 0;
        $end = $last ?: $first;

        $this->limit = [$start, $end];

        return $this;
    }
}
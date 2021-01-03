<?php

namespace DBCollection\Infra\Commands\Query;

class SelectBase
{
    use QueryData;

    /** @var string */
    protected $table;
    /** @var string */
    protected $countColumn;
    /** @var array */
    protected $columns;
    /** @var bool */
    protected $count;
    /** @var int[] */
    protected $limit;

    public function __construct(string $table)
    {
        $this->table = $table;
        $this->columns = [];
        $this->limit = [];
        $this->count = false;
    }

    protected function getColumns(): string
    {
        if ($this->count && $this->countColumn && count($this->columns) === 0) {
            return "COUNT({$this->countColumn})";
        }

        if ($this->count && count($this->columns) === 0) {
            return "COUNT(*)";
        }

        if (count($this->columns) === 0) {
            return "*";
        }

        return implode(",", $this->columns);
    }

    protected function getLimit(): ?string
    {
        if (!$this->limit || count($this->limit) === 0) {
            return null;
        }

        [$start, $end] = $this->limit;

        return " LIMIT {$start},{$end}";
    }
}
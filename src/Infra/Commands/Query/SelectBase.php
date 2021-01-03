<?php

namespace DBCollection\Infra\Commands\Query;

use Closure;
use InvalidArgumentException;

class SelectBase
{
    /** @var string */
    protected $table;
    /** @var array */
    protected $data;
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

    public function setData(array $data): self
    {
        if (empty($data) || count($data) === 0) {
            throw new InvalidArgumentException("You must pass some values to data parameters.");
        }

        $data = array_filter($data, $this->filterDataBeforeSet());

        if (count($data) === 0) {
            throw new InvalidArgumentException("The data values is invalid.");
        }

        $this->data = $data;
        return $this;
    }

    public function data(): array
    {
        if (count($this->data) === 0) {
            return [];
        }

        return array_map(static function ($params) {
            $name = "";
            $value = "";

            if (count($params) === 2) {
                [$name, $value] = $params;
            }

            if (count($params) === 3) {
                [$name, , $value] = $params;
            }

            return [":{$name}", $value];
        }, $this->data);
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

    protected function getDataFormatted(): string
    {
        if (empty($this->data)) {
            return "";
        }

        $response = " WHERE ";
        $values = array_map($this->manipulateDataValues(), $this->data);
        $response .= $values? implode(", ", $values): "";

        return $response;
    }

    private function manipulateDataValues(): Closure
    {
        return static function(array $params) {
            $name = "";
            $operator = "=";

            if (count($params) === 2) {
                $name = $params[0];
            }

            if (count($params) === 3) {
                [$name, $operator] = $params;
            }

            $operator = $operator==="like"? "LIKE": $operator;

            return "{$name} {$operator} :{$name}";
        };
    }

    private function filterDataBeforeSet(): Closure
    {
        return static function ($params) {
            if (is_string($params)) {
                return false;
            }
            if (count($params) < 2) {
                return false;
            }
            if (count($params) === 2 && in_array($params[1], ["like", "LIKE"], true)) {
                return false;
            }
            return true;
        };
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
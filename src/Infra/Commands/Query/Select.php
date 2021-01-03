<?php

namespace DBCollection\Infra\Commands\Query;

use bar\baz\source_with_namespace;
use InvalidArgumentException;

class Select implements QueryOperator
{
    /** @var string */
    private $table;
    /** @var array */
    private $data;
    /** @var array */
    private $columns;
    /** @var bool */
    private $count;
    /** @var string */
    private $countColumn;

    public function __construct(string $table)
    {
        $this->table = $table;
        $this->columns = [];
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

    public function __toString(): string
    {
        $columns = $this->getColumns();
        $where = $this->getDataFormatted();

        return "SELECT {$columns} FROM `{$this->table}`{$where};";
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

    private function getColumns(): string
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

    public function count(string $column = null): self
    {
        $this->count = true;
        $this->countColumn = $column;

        return $this;
    }

    private function getDataFormatted(): string
    {
        if (empty($this->data)) {
            return "";
        }

        $response = " WHERE ";
        $values = array_map($this->manipulateDataValues(), $this->data);
        $response .= $values? implode(", ", $values): "";

        return $response;
    }

    private function manipulateDataValues(): \Closure
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

    /**
     * @return \Closure
     */
    private function filterDataBeforeSet(): \Closure
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
}
<?php

namespace DBCollection\Infra\Commands\Query;

use Closure;

trait QueryWhere
{
    /** @var array */
    protected $where;

    public function where($data = null, $values = null): self
    {
        if (is_string($data) && $data && $values) {
            $this->where[] = [$data, "=", $values];
            return $this;
        }

        $data = array_map(static function ($params) {
            if (count($params) === 3) {
                return $params;
            }
            return [$params[0], "=", $params[1]];
        }, $data);

        $this->where = array_map($this->where, $data);
        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->setData($column, "IS NULL");
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->setData($column, "IS NOT NULL");
        return $this;
    }

    protected function getWhereData(): ?string
    {
        if (!$this->where || count($this->where) === 0) {
            return null;
        }

        $response = " WHERE ";
        $values = array_map($this->manipulateWhereValues(), $this->where);
        $response .= $values? implode(" AND ", $values): "";

        return $response;
    }

    private function manipulateWhereValues(): Closure
    {
        return static function($params) {
            [$name, $operator] = $params;

            $operator = $operator==="like"? "LIKE": $operator;

            return "{$name} {$operator} :{$name}";
        };
    }
}
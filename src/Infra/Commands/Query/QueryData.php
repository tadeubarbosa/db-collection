<?php

namespace DBCollection\Infra\Commands\Query;

use Closure;
use InvalidArgumentException;

trait QueryData
{
    use QueryWhere;

    /** @var array */
    private $data;

    public function setData($data = null, $values = null): self
    {
        $this->validateFindParams($data, $values);

        if (is_string($data) && $values !== null) {
            $data = [
                [$data, $values]
            ];
        }

        if (empty($data) || count($data) === 0) {
            throw new InvalidArgumentException("You must pass some values to data parameters.");
        }

        $data = $this->dataFilterForQueryData($data);

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

    protected function getDataFormatted(): ?string
    {
        if (empty($this->data)) {
            return null;
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

    /**
     * @param null $data
     * @param null $values
     * @throws QueryArgumentNull
     */
    private function validateFindParams($data = null, $values = null): void
    {
        if ($data === null) {
            throw new QueryArgumentNull("You must pass the column name to `find` method.");
        }

        if (is_string($data) && $values === null) {
            throw new QueryArgumentNull("You must pass value to `find` method before try find some values.");
        }
    }

    protected function dataFilterForQueryData(array $data): array
    {
        return $data;
    }
}
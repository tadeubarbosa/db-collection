<?php

namespace DBCollection\Infra\Commands\Query;

use InvalidArgumentException;

class Update implements QueryOperator
{
    use QueryData;

    /** @var string */
    private $table;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function __toString(): string
    {
        $sets = $this->manipulateDataUpdateKeys();

        return "UPDATE `{$this->table}`{$sets};";
    }

    private function manipulateDataUpdateKeys(): ?string
    {
        if (!$this->data || count($this->data) === 0) {
            return null;
        }

        $data = array_filter($this->data, static function ($value) {
            return empty($value) === false;
        });

        if ($data === null || count($data) === 0) {
            throw new InvalidArgumentException("Some params of data is wrong.");
        }

        $data = array_map(static function (string $key) {
            return "`{$key}` = :{$key}";
        }, array_keys($data));

        return " SET " . implode(" AND ", $data);
    }

    protected function dataFilterForQueryData(array $data): array
    {
        foreach ($data as $key => $value) {
            if(is_numeric($key)) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
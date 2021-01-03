<?php

namespace DBCollection\Infra\Commands\Query;

class Query
{
    /** @var string */
    private $table;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * @param string|array $data
     * @param null $values
     * @return Select
     * @throws QueryArgumentNull
     */
    public function find($data = null, $values = null): Select
    {
        $this->validateFindParams($data, $values);

        $operator = new Select($this->table, $data, $values);

        if (is_string($data)) {
            $data = [
                [$data, $values]
            ];
        }

        return $operator->setData($data);
    }

    public function update(array $data): Update
    {
        $operator = new Update($this->table, $data);
        return $operator;
    }

    public function delete(string $column, $value): Delete
    {
        $operator = new Delete($this->table, $column, $value);
        return $operator;
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
}
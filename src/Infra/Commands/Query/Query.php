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
     * @param null $data
     * @param null $values
     * @return Select
     */
    public function find($data = null, $values = null): Select
    {
        $operator = new Select($this->table);

        return $operator->setData($data, $values);
    }

    /**
     * @param null $data
     * @param null $values
     * @return Update
     */
    public function update($data = null, $values = null): Update
    {
        $operator = new Update($this->table);

        return $operator->setData($data, $values);
    }

    /**
     * @param null $data
     * @param null $values
     * @return Delete
     */
    public function delete($data = null, $values = null): Delete
    {
        $operator = new Delete($this->table);

        return $operator->setData($data, $values);
    }
}
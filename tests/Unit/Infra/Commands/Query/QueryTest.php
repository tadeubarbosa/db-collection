<?php

namespace DBCollection\Tests\Infra\Commands\Query;

use DBCollection\Infra\Commands\Query\Query;
use DBCollection\Infra\Commands\Query\QueryArgumentNull;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testIfFindCanReceiveAStringAndValueAsParam(): void
    {
        $query = new Query("table_name");
        $sql = $query->find("id", "123");

        $this->assertStringContainsString("SELECT", $sql);
    }

    public function testIfThrowAnErrorWhenNoDataIsPassed(): void
    {
        $this->expectException(QueryArgumentNull::class);

        $query = new Query("table_name");
        $query->find();
    }

    public function testIfThrowAnErrorWhenColumnIsPassedButValueNot(): void
    {
        $this->expectException(QueryArgumentNull::class);

        $query = new Query("table_name");
        $query->find("id");
    }

    public function testIfFindCanReceiveAnArrayasData(): void
    {
        $query = new Query("table_name");
        $data = [
            ["id", ">", 100]
        ];
        $sql = $query->find($data);

        $this->assertStringContainsString("SELECT", $sql);
    }
}

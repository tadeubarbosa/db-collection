<?php

namespace DBCollection\Tests\Integration\Infra;

use DBCollection\Domain\Connection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    public function testIfReturnsAnThrowBeforeSetValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Connection::getInstance();
    }

    public function testCanInsertAndRetrieverAnData(): void
    {
        Connection::setDns("sqlite::memory:");
        $conn = Connection::getInstance();

        $conn->exec("CREATE TABLE items (value TEXT)");
        $conn->exec("INSERT INTO items (value) VALUES ('Item 1')");

        $stmt = $conn->query("SELECT * FROM items;");
        $result = $stmt->fetch(\PDO::FETCH_OBJ);

        $this->assertEquals("Item 1", $result->value);
    }
}

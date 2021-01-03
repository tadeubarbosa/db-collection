<?php

namespace DBCollection\Tests\Infra\Commands\Query;

use DBCollection\Infra\Commands\Query\Insert;
use PHPUnit\Framework\TestCase;

class InsertTest extends TestCase
{
    /** @var Insert */
    private static $insert;

    protected function setUp(): void
    {
        parent::setUp();

        self::$insert = new Insert("table");
    }

    public function testCanMountWithoutData(): void
    {
        self::assertEquals("INSERT INTO `table`;", (string) self::$insert);
    }

    public function testIfCanAddWhereValues(): void
    {
        self::$insert->setData("id", 1);

        self::assertEquals("INSERT INTO `table` WHERE id = :id;", (string) self::$insert);
    }
}

<?php

namespace DBCollection\Tests\Infra\Commands\Query;

use DBCollection\Infra\Commands\Query\Delete;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    /** @var Delete */
    private static $delete;

    protected function setUp(): void
    {
        parent::setUp();

        self::$delete = new Delete("table");
    }

    public function testCanMountWithoutData(): void
    {
        self::assertEquals("DELETE FROM `table`;", (string) self::$delete);
    }

    public function testIfCanAddWhereValues(): void
    {
        self::$delete->where("id", 1);

        self::assertEquals("DELETE FROM `table` WHERE id = :id;", (string) self::$delete);
    }
}

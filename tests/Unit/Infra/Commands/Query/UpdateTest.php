<?php

namespace DBCollection\Tests\Unit\Infra\Commands\Query;

use DBCollection\Infra\Commands\Query\Update;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    /** @var Update */
    private static $update;

    protected function setUp(): void
    {
        parent::setUp();

        self::$update = new Update("table");
    }

    public function testCanMountWithoutData(): void
    {
        self::assertEquals("UPDATE `table`;", (string) self::$update);
    }
}

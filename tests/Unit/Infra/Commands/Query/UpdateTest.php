<?php

namespace DBCollection\Tests\Unit\Infra\Commands\Query;

use DBCollection\Infra\Commands\Query\QueryArgumentNull;
use DBCollection\Infra\Commands\Query\Update;
use InvalidArgumentException;
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

    public function testCanReceiveData(): void
    {
        $data = [
            "name" => "Jhon Doe",
            "active" => 1
        ];
        self::$update->setData($data);

        self::assertEquals("UPDATE `table` SET `name` = :name AND `active` = :active;", (string) self::$update);
    }

    public function testThrowOnEmptyData(): void
    {
        self::expectException(QueryArgumentNull::class);

        self::$update->setData(null);
    }

    public function testCanPassWhereValues(): void
    {
        self::$update->where("name", "Jhon Doe");

        self::assertEquals("UPDATE `table` SET `name` = :name AND `active` = :active;", (string) self::$update);
    }

    public function testCanReceiveDataAndWheres(): void
    {
        $data = [
            "name" => "Jhon Doe",
            "active" => 1
        ];
        self::$update->setData($data)->where("id", 1);

        self::assertEquals("UPDATE `table` SET `name` = :name AND `active` = :active WHERE id = :id;", (string) self::$update);
    }

    /**
     * @dataProvider wrongDataList
     * @param array|null $data
     */
    public function testThrowOnWrongDatas(array $data = null): void
    {
        self::expectException(InvalidArgumentException::class);

        self::$update->setData($data);
    }

    public function wrongDataList(): array
    {
        return [
            "empty-array" => [[]],
            "wrong-data" => [["id"]],
        ];
    }
}

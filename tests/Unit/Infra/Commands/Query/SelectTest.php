<?php

namespace DBCollection\Tests\Unit\Infra\Commands\Query;

use DBCollection\Infra\Commands\Query\Select;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    /** @var Select */
    private static $select;

    protected function setUp(): void
    {
        parent::setUp();

        self::$select = new Select("table");
    }

    public function testCanMountWithoutData(): void
    {
        self::assertEquals("SELECT * FROM `table`;", (string) self::$select);
    }

    public function testCanReceiveOnlySomeColumns(): void
    {
        self::$select->columns("id");

        self::assertEquals("SELECT id FROM `table`;", (string) self::$select);
    }

    public function testThrowsAnExceptionWhenReceiveAnEmptyColumn(): void
    {
        $this->expectException(InvalidArgumentException::class);

        self::$select->columns("");
    }

    public function testCanReceiveAnArrayOfColumns(): void
    {
        $columns = ["id", "name"];
        self::$select->columns($columns);

        self::assertEquals("SELECT id,name FROM `table`;", (string) self::$select);
    }

    public function testReturnsAnExceptionWhenReceiveAnEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        self::$select->columns([]);
    }

    public function testCanMountCountOperator(): void
    {
        self::$select->count();

        self::assertEquals("SELECT COUNT(*) FROM `table`;", (string) self::$select);
    }

    public function testCanMountCountOperatorWithColumnValue(): void
    {
        self::$select->count("id");

        self::assertEquals("SELECT COUNT(id) FROM `table`;", (string) self::$select);
    }

    /**
     * @dataProvider dataValues
     * @param array $data
     * @param string $expected
     */
    public function testCanReceiveDataValues(array $data, string $expected): void
    {
        self::$select->setData($data);

        self::assertEquals($expected, (string) self::$select);
    }

    /**
     * @dataProvider dataOfInvalidValues
     * @param array $data
     */
    public function testThrowsAnExceptionWhenPassAInvalidNumberOfArguments(array $data): void
    {
        $this->expectException(InvalidArgumentException::class);

        self::$select->setData($data);
    }

    /**
     * @dataProvider limitValues
     * @param int|null $initial
     * @param int|null $end
     * @param string|null $expected
     */
    public function testCanPassLimitAttribute(int $initial = null, int $end = null, string $expected = null): void
    {
        if ($end && $initial !== null) {
            self::$select->limit($initial, $end);
            self::assertEquals($expected, (string) self::$select);
            return;
        }

        self::$select->limit($end);
        self::assertEquals($expected, (string) self::$select);
    }

    public function testGetValidatedData(): void
    {
        $data = [["id", 1]];
        self::$select->setData($data);

        $expected = [[":id", 1]];
        self::assertEquals($expected, self::$select->data());
    }

    public function testCanMountWhereValues(): void
    {
        self::$select->where("id", 1);

        self::assertEquals("SELECT * FROM `table` WHERE id = :id;", (string) self::$select);
    }

    public function testCanMountManyWhereValues(): void
    {
        self::$select
            ->where("id", 1)
            ->where("name", "Jhon Doe");

        self::assertEquals("SELECT * FROM `table` WHERE id = :id AND name = :name;", (string) self::$select);
    }

    public function testCanMountArrayOfWhereValues(): void
    {
        $data = [
            ["id", 1],
            ["email", "like", "email@email.com"],
        ];
        self::$select->where($data);

        self::assertEquals("SELECT * FROM `table` WHERE id = :id AND email LIKE :email;", (string) self::$select);
    }

    public function dataValues(): array
    {
        return [
            "with-two-parameters" => [
                [["id", 1]], "SELECT * FROM `table` WHERE id = :id;"
            ],
            "with-three-parameters" => [
                [["id", "<>", 1]], "SELECT * FROM `table` WHERE id <> :id;"
            ],
            "with-like" => [
                [["name", "like", "%Jhon%"]], "SELECT * FROM `table` WHERE name LIKE :name;"
            ],
        ];
    }

    public function dataOfInvalidValues(): array
    {
        return [
            "with-one-parameter" => [
                [["id"]]
            ],
            "with-a-string-parameter" => [
                ["id"]
            ],
            "with-a-empty-parameter" => [
                []
            ],
            "with-incorrect-like" => [
                [["name", "like"]]
            ],
        ];
    }

    public function limitValues(): array
    {
        return [
            [null, 10, "SELECT * FROM `table` LIMIT 0,10;"],
            [5, 10, "SELECT * FROM `table` LIMIT 5,10;"],
        ];
    }
}

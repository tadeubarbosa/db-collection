<?php

namespace DBCollection\Tests\Infra\Commands\Query;

use DBCollection\Infra\Commands\Query\Select;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    public function testCanMountWithoutData(): void
    {
        $select = new Select("table");

        self::assertEquals("SELECT * FROM `table`;", (string) $select);
    }

    public function testCanReceiveOnlySomeColumns(): void
    {
        $select = new Select("table");
        $select->columns("id");

        self::assertEquals("SELECT id FROM `table`;", (string) $select);
    }

    public function testThrowsAnExceptionWhenReceiveAnEmptyColumn(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $select = new Select("table");
        $select->columns("");
    }

    public function testCanReceiveAnArrayOfColumns(): void
    {
        $select = new Select("table");
        $columns = ["id", "name"];
        $select->columns($columns);

        self::assertEquals("SELECT id,name FROM `table`;", (string) $select);
    }

    public function testReturnsAnExceptionWhenReceiveAnEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $select = new Select("table");
        $select->columns([]);
    }

    public function testCanMountCountOperator(): void
    {
        $select = new Select("table");
        $select->count();

        self::assertEquals("SELECT COUNT(*) FROM `table`;", (string) $select);
    }

    public function testCanMountCountOperatorWithColumnValue(): void
    {
        $select = new Select("table");
        $select->count("id");

        self::assertEquals("SELECT COUNT(id) FROM `table`;", (string) $select);
    }

    public function testThrowsWhenPassAnEmptyData(): void
    {
        self::expectException(InvalidArgumentException::class);

        $select = new Select("table");
        $select->setData([]);
    }

    /**
     * @dataProvider dataValues
     * @param array $data
     * @param string $expected
     */
    public function testCanReceiveDataValues(array $data, string $expected): void
    {
        $select = new Select("table");
        $select->setData($data);

        self::assertEquals($expected, (string) $select);
    }

    /**
     * @dataProvider dataOfInvalidValues
     */
    public function testThrowsAnExceptionWhenPassAInvalidNumberOfArguments(array $data): void
    {
        $this->expectException(InvalidArgumentException::class);

        $select = new Select("table");
        $select->setData($data);
    }

    public function testGetValidatedData(): void
    {
        $select = new Select("table");
        $data = [
            ["id", 1]
        ];
        $select->setData($data);

        $expected = [
            [":id", 1]
        ];
        self::assertEquals($expected, $select->data());
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
}

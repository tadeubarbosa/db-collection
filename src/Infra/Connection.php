<?php

namespace DBCollection\Infra;

use PDO;

class Connection
{
    /** @var string|null */
    private static $host;
    /** @var string|null */
    private static $dbName;
    /** @var string|null */
    private static $user;
    /** @var string|null */
    private static $pass;
    /** @var string */
    private static $dns;
    /** @var PDO */
    private static $instance;

    public static function setHostDBName(string $host, string $dbName): void
    {
        self::$host = $host;
        self::$dbName = $dbName;
    }

    public static function setUserAndPass(string $user = null, string $pass = null): void
    {
        self::$user = $user;
        self::$pass = $pass;
    }

    public static function setDns(string $dns): void
    {
        self::$dns = $dns;
    }

    public static function getInstance(): PDO
    {
        if (self::$dns === null && (self::$host === null || self::$dbName === null)) {
            throw new \InvalidArgumentException(
                "You must call the methods Connection::setHostDBName() before get an instance."
            );
        }

        if (self::$instance === null) {
            try {
                $dsn = self::getDnsString();
                self::$instance = new PDO($dsn, self::$user, self::$pass);
            } catch (\PDOException $e) {
                throw new \RuntimeException($e->getMessage());
            }
        }

        return self::$instance;
    }

    private static function getDnsString(): string
    {
        if (self::$dns) {
            return self::$dns;
        }

        if (self::$host === null || self::$dbName === null) {
            throw new \InvalidArgumentException(
                "You must set host and dbName before start this class."
            );
        }

        return sprintf("mysql:host=%s;dbname=%s", self::$host, self::$dbName);
    }
}
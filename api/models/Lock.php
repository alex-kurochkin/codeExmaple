<?php

declare(strict_types=1);

namespace api\models;

use api\models\exception\LockException;
use yii\db\Connection;

class Lock
{
    private static array $locks = [];

    private Connection $connection;
    private string $lockName;
    private int $timeout;

    private function __construct(string $lockName, int $timeout)
    {
        $this->lockName = $lockName;
        $this->timeout = $timeout;
        $this->connection = Ar::getDb();
    }

    /**
     * Get lock for name.
     *
     * $name - arbitrary string or constant __METHOD__.
     *
     * @param string $name
     * @param int $timeout
     * @param bool $throwException
     * @return bool
     * @throws LockException
     */
    public static function get(string $name, int $timeout = 5, bool $throwException = true): bool
    {
        if (self::isLocked($name)) {
            if (!$throwException) {
                return false;
            }

            $shortName = $name;
            if (is_callable($name) && !function_exists($name)) {
                $shortName = Reflection::getMethodShortName($name);
            }

            throw new LockException('Lock fail for: ' . $shortName);
        }

        self::$locks[$name] = new self($name, $timeout);

        if (!self::$locks[$name]->acquire()) {
            unset(self::$locks[$name]);
            return false;
        }

        return true;
    }

    public static function isLocked(string $lockName): bool
    {
        return array_key_exists($lockName, self::$locks);
    }

    public function acquire(): bool
    {
        return (bool)$this->connection->createCommand(
            'SELECT GET_LOCK(:name, :timeout)',
            [':name' => $this->lockName, ':timeout' => $this->timeout]
        )->queryScalar();
    }

    public static function release(string $lockName): void
    {
        if (self::isLocked($lockName)) {
            unset(self::$locks[$lockName]);
        }
    }

    private function __destruct()
    {
        $this->connection->createCommand(
            'SELECT RELEASE_LOCK(:name)',
            [':name' => $this->lockName]
        )->queryScalar();

        unset($this->connection);
    }
}
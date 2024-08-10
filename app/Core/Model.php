<?php

namespace Solmer\Storage\Core;

use PDO;

class Model implements \Iterator
{
    protected const TABLE_NAME = '';
    protected const DEFAULT_ORDER = false;
    protected const RELATIONS = [];

    private static PDO|false $connection = false;
    private static int $connectionCount = 0;

    private $query = null;
    private $result = false;

    public static function connectToDb(): PDO
    {
        $conn_str = 'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME') . ';charset=utf8';
        return new PDO($conn_str, env('DB_USER'), env('DB_PASSWORD'));
    }

    public function __construct()
    {
        if (!self::$connection) {
            self::$connection = self::connectToDb();
        }
        self::$connectionCount++;
    }

    public function __destruct()
    {
        self::$connectionCount--;
        if (self::$connectionCount == 0) {
            self::$connection = false;
        }
    }

    public function run(string $sql, array $params = []): void
    {
        if ($this->query) {
            $this->query->closeCursor();
        }
        $this->query = self::$connection->prepare($sql);
        if ($params) {
            foreach ($params as $key => $value) {
                $k = is_int($key) ? $key + 1 : $key;
                switch (gettype($value)) {
                    case 'boolean':
                        $t = PDO::PARAM_BOOL;
                        break;
                    case 'integer':
                        $t = PDO::PARAM_INT;
                        break;
                    case 'NULL':
                        $t = PDO::PARAM_NULL;
                        break;
                    default:
                        $t = PDO::PARAM_STR;
                        break;
                }
                $this->query->bindValue($k, $value, $t);
            }
        }
        $this->query->execute();
    }

    public function select(
        string $fields = '*',
        array $links = [],
        string $where = '',
        array $params = [],
        string $order = '',
        array $offset = [],
        array $limit = [],
        string $group = '',
        string $having = '',
    ): void {
        $sql = 'SELECT ' . $fields . ' FROM ' . static::TABLE_NAME;
        if ($links) {
            foreach ($links as $extTable) {
                $rel = static::RELATIONS[$extTable];
                $sql .= ' ' . ((key_exists('type', $rel)) ?
                    $rel['type'] : 'INNER')
                    . ' JOIN '
                    . $extTable
                    . ' ON '
                    . static::TABLE_NAME . '.' . $rel['external']
                    . ' = '
                    . $extTable . '.' . $rel['primary'];
            }
        }
        if ($where) {
            $sql .= ' WHERE ' . $where;
        }
        if ($group) {
            $sql .= ' GROUP BY ' . $group;
            if ($having) {
                $sql .= ' HAVING ' . $having;
            }
        }
        if ($order) {
            $sql .= ' ORDER BY ' . $order;
        } elseif(static::DEFAULT_ORDER) {
            $sql .= ' ORDER BY ' . static::DEFAULT_ORDER;
        }
        if ($offset && $limit) {
            $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        }
        $sql .= ';';
        $this->run($sql, $params);
    }

    public function current(): array|false
    {
        return $this->result;
    }

    public function key(): null
    {
        return null;
    }

    public function next(): void
    {
        $this->result = $this->query->fetch(PDO::FETCH_ASSOC);
    }

    public function rewind(): void
    {
        $this->result = $this->query->fetch(PDO::FETCH_ASSOC);
    }

    public function valid(): bool
    {
        return !empty($this->result);
    }

    public function getRecord(
        string $fields = '*',
        array $links = [],
        string $where = '',
        array $params = [],
    ): array|bool {
        $this->result = null;
        $this->select($fields, $links, $where, $params);
        return $this->query->fetch(PDO::FETCH_ASSOC);
    }

    public function get(
        string $value,
        string $keyField = 'id',
        string $fields = '*',
        array $links = [],
    ): array|bool {
        return $this->getRecord(
            $fields,
            $links,
            $keyField . ' = ?',
            [$value]
        );
    }

    protected function beforeInsert(array &$fields): void
    {
    }

    public function insert(array $fields = []): int
    {
        static::beforeInsert($fields);
        $sql = 'INSERT INTO ' . static::TABLE_NAME;
        $sql2 = $sql1 = '';
        foreach ($fields as $n => $value) {
            if ($sql1 && $sql2) {
                $sql1 .= ', ';
                $sql2 .= ', ';
            }
            $sql1 .= $n;
            $sql2 .= ':' . $n;
        }
        $sql .= ' (' . $sql1 . ') VALUES (' . $sql2 . ');';
        $this->run($sql, $fields);
        $id = self::$connection->lastInsertId();
        return $id;
    }

    protected function beforeUpdate(
        array &$fields,
        string $value,
        string $keyField = 'id'
    ): void {
    }

    public function update(array $fields, string $value, string $keyField = 'id'): void
    {
        static::beforeUpdate($fields, $value, $keyField);
        $sql = 'UPDATE ' . static::TABLE_NAME . ' SET ';
        $sql1 = '';
        foreach ($fields as $n => $v) {
            if ($sql1) {
                $sql1 .= ', ';
            }
            $sql1 .= $n . ' = :' . $n;
        }
        $sql .= $sql1 . ' WHERE ' . $keyField . ' = :__key;';
        $fields['__key'] = $value;
        $this->run($sql, $fields);
    }

    protected function beforeDelete(string $value, string $keyField = 'id')
    {
    }

    public function delete(string $value, string $keyField = 'id')
    {
        static::beforeDelete($value, $keyField);
        $sql = 'DELETE FROM ' . static::TABLE_NAME;
        $sql .= ' WHERE ' . $keyField . ' = ?;';
        $this->run($sql, [$value]);
    }

    public function getAll(
        string $fields = '*',
        array $links = [],
        string $where = '',
        array $params = [],
        string $order = '',
        array $offset = [],
        array $limit = [],
        string $group = '',
        string $having = '',
    ): array {
        $this->select(
            $fields,
            $links,
            $where,
            $params,
            $order,
            $offset,
            $limit,
            $group,
            $having,
        );
        return $this->query->fetchAll(PDO::FETCH_ASSOC);
    }
}

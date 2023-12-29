<?php

namespace Storage\Storage\Core;

use PDO;
use Storage\Storage\Application\Settings;

class Model implements \Iterator
{
    protected const TABLE_NAME = '';
    protected const DEFAULT_ORDER = false;
    protected const RELATIONS = [];

    private static $connection = false;
    private static $connection_count = 0;

    private $query = null;
    private $result = false;

    public static function connect_to_db(): PDO
    {
        $conn_str = 'mysql:host=' . Settings::DB_HOST . ';dbname=' . Settings::DB_NAME . ';charset=utf8';
        return new PDO($conn_str, Settings::DB_USER, Settings::DB_PASSWORD);
    }

    public function __construct()
    {
        if (!self::$connection) {
            self::$connection = self::connect_to_db();
        }
        self::$connection_count++;
    }

    public function __destruct()
    {
        self::$connection_count--;
        if (self::$connection_count == 0) {
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
            foreach ($links as $ext_table) {
                $rel = static::RELATIONS[$ext_table];
                $sql .= ' ' . ((key_exists('type', $rel)) ?
                    $rel['type'] : 'INNER')
                    . ' JOIN '
                    . $ext_table
                    . ' ON '
                    . static::TABLE_NAME . '.' . $rel['external']
                    . ' = '
                    . $ext_table . '.' . $rel['primary'];
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
        } else if(static::DEFAULT_ORDER) {
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

    public function get_record(
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
        string $key_field = 'id',
        string $fields = '*',
        array $links = [],
    ): array|bool {
        return $this->get_record(
            $fields,
            $links,
            $key_field . ' = ?',
            [$value]
        );
    }

    protected function before_insert(array &$fields): void
    {
    }

    function insert(array $fields = []): int
    {
        static::before_insert($fields);
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

    protected function before_update(
        array &$fields,
        string $value,
        string $key_field = 'id'
    ): void {
    }

    public function update(array $fields, string $value, string $key_field = 'id'): void
    {
        static::before_update($fields, $value, $key_field);
        $sql = 'UPDATE ' . static::TABLE_NAME . ' SET ';
        $sql1 = '';
        foreach ($fields as $n => $v) {
            if ($sql1) {
                $sql1 .= ', ';
            }
            $sql1 .= $n . ' = :' . $n;
        }
        $sql .= $sql1 . ' WHERE ' . $key_field . ' = :__key;';
        $fields['__key'] = $value;
        $this->run($sql, $fields);
    }

    protected function before_delete(string $value, string $key_field = 'id')
    {
    }

    function delete(string $value, string $key_field = 'id')
    {
        static::before_delete($value, $key_field);
        $sql = 'DELETE FROM ' . static::TABLE_NAME;
        $sql .= ' WHERE ' . $key_field . ' = ?;';
        $this->run($sql, [$value]);
    }

    function get_all(
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

<?php

/**
 * ORLMS - Database Singleton
 *
 * Manages a single PDO connection throughout the entire request lifecycle.
 * All models access the database through this class.
 *
 * Pattern: Singleton — only one instance is created and reused.
 */

class Database
{
    // ─────────────────────────────────────────────────────────────────────────
    // PROPERTIES
    // ─────────────────────────────────────────────────────────────────────────

    /** @var Database|null The single instance of this class */
    private static ?Database $instance = null;

    /** @var PDO The PDO connection object */
    private PDO $connection;

    /** @var PDOStatement|null The last prepared statement */
    private ?PDOStatement $statement = null;

    // ─────────────────────────────────────────────────────────────────────────
    // CONSTRUCTOR — private to prevent direct instantiation
    // ─────────────────────────────────────────────────────────────────────────

    private function __construct()
    {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Throw exceptions on error
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Return arrays by default
            PDO::ATTR_EMULATE_PREPARES   => false,                    // Use real prepared statements
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"       // Enforce UTF-8 encoding
        ];

        try {
            $this->connection = new PDO(DB_DSN, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Show a clean error — never expose raw PDO errors in production
            die('ORLMS Database Error: Unable to connect to the database. ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET INSTANCE — returns the single shared instance
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns the singleton Database instance.
     * Creates one if it does not yet exist.
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET CONNECTION — returns the raw PDO object
    // Used by core/Model.php
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns the underlying PDO connection.
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // QUERY METHODS — convenience wrappers used by Model.php
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Prepares a SQL statement.
     *
     * @param  string $sql The SQL query string
     * @return self
     */
    public function query(string $sql): self
    {
        $this->statement = $this->connection->prepare($sql);
        return $this;
    }

    /**
     * Binds a value to a named parameter in the prepared statement.
     *
     * @param  string $param  The named parameter (e.g. ':id')
     * @param  mixed  $value  The value to bind
     * @param  int|null $type PDO data type constant (auto-detected if null)
     * @return self
     */
    public function bind(string $param, mixed $value, ?int $type = null): self
    {
        if ($type === null) {
            $type = match (true) {
                is_int($value)  => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default         => PDO::PARAM_STR,
            };
        }

        $this->statement->bindValue($param, $value, $type);
        return $this;
    }

    /**
     * Executes the prepared statement.
     *
     * @return bool
     */
    public function execute(): bool
    {
        return $this->statement->execute();
    }

    /**
     * Executes the statement and returns all matching rows.
     *
     * @return array
     */
    public function resultSet(): array
    {
        $this->execute();
        return $this->statement->fetchAll();
    }

    /**
     * Executes the statement and returns a single row.
     *
     * @return array|false
     */
    public function single(): array|false
    {
        $this->execute();
        return $this->statement->fetch();
    }

    /**
     * Returns the number of rows affected by the last statement.
     *
     * @return int
     */
    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    /**
     * Returns the ID of the last inserted row.
     *
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TRANSACTION METHODS
    // Used for operations that must fully succeed or fully fail (e.g. approvals)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Begins a database transaction.
     */
    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    /**
     * Commits the current transaction.
     */
    public function commit(): void
    {
        $this->connection->commit();
    }

    /**
     * Rolls back the current transaction.
     */
    public function rollBack(): void
    {
        $this->connection->rollBack();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PREVENT CLONING AND UNSERIALIZATION
    // Enforces the singleton pattern strictly.
    // ─────────────────────────────────────────────────────────────────────────

    private function __clone() {}
    public function __wakeup() {}
}

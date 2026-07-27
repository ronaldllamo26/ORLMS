<?php

/**
 * ORLMS - Base Model Class
 *
 * All application models extend this class.
 * Provides common database operations so child models
 * do not need to repeat the same logic.
 *
 * Usage in child model:
 *   class OrdinanceModel extends Model {
 *       protected string $table = 'ordinances';
 *   }
 *
 * Then in the child model you can call:
 *   $this->findAll()
 *   $this->findById(5)
 *   $this->insert([...])
 *   $this->update(5, [...])
 *   $this->delete(5)
 *   $this->query("SELECT ...", [...])  ← for complex custom queries
 */

class Model
{
    // ─────────────────────────────────────────────────────────────────────────
    // PROPERTIES
    // ─────────────────────────────────────────────────────────────────────────

    /** @var Database The database singleton instance */
    protected Database $db;

    /**
     * The database table this model operates on.
     * Child models must override this property.
     *
     * Example: protected string $table = 'ordinances';
     *
     * @var string
     */
    protected string $table = '';

    // ─────────────────────────────────────────────────────────────────────────
    // CONSTRUCTOR
    // ─────────────────────────────────────────────────────────────────────────

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FIND METHODS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns all records from the model's table.
     * Ordered by newest first (created_at DESC) if the column exists.
     *
     * @return array
     */
    public function findAll(): array
    {
        return $this->query("SELECT * FROM {$this->table} ORDER BY id DESC");
    }

    /**
     * Returns a single record by its primary key (id).
     *
     * @param  int $id
     * @return array|false  Returns the row as array, or false if not found
     */
    public function findById(int $id): array|false
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1",
            [':id' => $id]
        , true);
    }

    /**
     * Returns records matching a single column/value pair.
     *
     * Example: $this->findWhere('status', 'draft')
     *
     * @param  string $column
     * @param  mixed  $value
     * @return array
     */
    public function findWhere(string $column, mixed $value): array
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE {$column} = :value ORDER BY id DESC",
            [':value' => $value]
        );
    }

    /**
     * Returns a single record matching a column/value pair.
     *
     * Example: $this->findOneWhere('email', 'admin@orlms.gov.ph')
     *
     * @param  string $column
     * @param  mixed  $value
     * @return array|false
     */
    public function findOneWhere(string $column, mixed $value): array|false
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1",
            [':value' => $value]
        , true);
    }

    /**
     * Returns the total count of records in the table.
     *
     * @return int
     */
    public function count(): int
    {
        $result = $this->query(
            "SELECT COUNT(*) as total FROM {$this->table}",
            [],
            true
        );
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Returns the count of records matching a condition.
     *
     * Example: $this->countWhere('status', 'draft')
     *
     * @param  string $column
     * @param  mixed  $value
     * @return int
     */
    public function countWhere(string $column, mixed $value): int
    {
        $result = $this->query(
            "SELECT COUNT(*) as total FROM {$this->table} WHERE {$column} = :value",
            [':value' => $value],
            true
        );
        return (int) ($result['total'] ?? 0);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INSERT / UPDATE / DELETE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Inserts a new record into the table.
     *
     * @param  array $data  Associative array: ['column' => 'value', ...]
     * @return string|false  Returns the new record's ID on success, false on failure
     */
    public function insert(array $data): string|false
    {
        if (empty($data)) return false;

        $columns      = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($col) => ':' . $col, array_keys($data)));
        $bindings     = [];

        foreach ($data as $column => $value) {
            $bindings[':' . $column] = $value;
        }

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $this->db->query($sql);

        foreach ($bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Updates a record by its primary key (id).
     *
     * @param  int   $id   The record ID to update
     * @param  array $data Associative array of columns and new values
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        if (empty($data)) return false;

        $setParts = array_map(fn($col) => "{$col} = :{$col}", array_keys($data));
        $setClause = implode(', ', $setParts);
        $bindings  = [];

        foreach ($data as $column => $value) {
            $bindings[':' . $column] = $value;
        }

        $bindings[':id'] = $id;

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";

        $this->db->query($sql);

        foreach ($bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        return $this->db->execute();
    }

    /**
     * Deletes a record by its primary key (id).
     * NOTE: In ORLMS, most records should be archived, not deleted.
     * Use this only where truly appropriate (e.g. admin cleanup).
     *
     * @param  int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RAW QUERY — for complex SQL that the helpers above cannot handle
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Executes a raw SQL query with optional parameter bindings.
     *
     * @param  string $sql       The SQL query (use named placeholders like :id)
     * @param  array  $bindings  Associative array: [':param' => value, ...]
     * @param  bool   $single    If true, returns one row; if false, returns all rows
     * @return array|false
     */
    public function query(string $sql, array $bindings = [], bool $single = false): array|false
    {
        $this->db->query($sql);

        foreach ($bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        if ($single) {
            return $this->db->single();
        }

        return $this->db->resultSet();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PAGINATION HELPER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns a paginated result set from the table.
     *
     * @param  int $page    Current page number (1-indexed)
     * @param  int $perPage Number of records per page (default from config)
     * @return array        ['data' => [...], 'total' => int, 'pages' => int]
     */
    public function paginate(int $page = 1, int $perPage = RECORDS_PER_PAGE): array
    {
        $page   = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $total  = $this->count();
        $pages  = (int) ceil($total / $perPage);

        $data = $this->query(
            "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT :limit OFFSET :offset",
            [':limit' => $perPage, ':offset' => $offset]
        );

        return [
            'data'    => $data ?: [],
            'total'   => $total,
            'pages'   => $pages,
            'current' => $page,
            'perPage' => $perPage
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TRANSACTION SHORTCUTS
    // ─────────────────────────────────────────────────────────────────────────

    protected function beginTransaction(): void  { $this->db->beginTransaction(); }
    protected function commit(): void             { $this->db->commit(); }
    protected function rollBack(): void           { $this->db->rollBack(); }
}

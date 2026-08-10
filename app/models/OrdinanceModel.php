<?php

/**
 * ORLMS - Ordinance Model
 *
 * Handles all database operations for the `ordinances` table.
 *
 * Table columns:
 *   id, ordinance_no, title, subject, content, author_id,
 *   committee_id, status, ai_summary, file_path,
 *   date_filed, created_at, updated_at
 */

class OrdinanceModel extends Model
{
    protected string $table = 'ordinances';

    /**
     * Returns the most recent ordinances.
     *
     * @param  int $limit
     * @return array
     */
    public function getRecent(int $limit = 5): array
    {
        return $this->query(
            "SELECT o.*, u.name AS author_name
             FROM ordinances o
             LEFT JOIN users u ON o.author_id = u.id
             ORDER BY o.created_at DESC
             LIMIT :limit",
            [':limit' => $limit]
        );
    }

    /**
     * Returns all ordinances with author name via JOIN.
     *
     * @return array
     */
    public function getAllWithAuthor(): array
    {
        return $this->query(
            "SELECT o.*, u.name AS author_name, c.name AS committee_name
             FROM ordinances o
             LEFT JOIN users u ON o.author_id = u.id
             LEFT JOIN committees c ON o.committee_id = c.id
             ORDER BY o.created_at DESC"
        );
    }

    /**
     * Returns a single ordinance by ID with author name.
     *
     * @param  int $id
     * @return array|false
     */
    public function getByIdWithAuthor(int $id): array|false
    {
        return $this->query(
            "SELECT o.*, u.name AS author_name, c.name AS committee_name
             FROM ordinances o
             LEFT JOIN users u ON o.author_id = u.id
             LEFT JOIN committees c ON o.committee_id = c.id
             WHERE o.id = :id
             LIMIT 1",
            [':id' => $id],
            true
        );
    }

    /**
     * Returns ordinances filtered by status.
     *
     * @param  string $status
     * @return array
     */
    public function getByStatus(string $status): array
    {
        return $this->query(
            "SELECT o.*, u.name AS author_name
             FROM ordinances o
             LEFT JOIN users u ON o.author_id = u.id
             WHERE o.status = :status
             ORDER BY o.created_at DESC",
            [':status' => $status]
        );
    }

    /**
     * Generates the next ordinance number in sequence.
     * Format: ORD-YYYY-NNN
     *
     * @return string
     */
    public function generateOrdinanceNo(): string
    {
        $year = date('Y');

        $result = $this->query(
            "SELECT COUNT(*) as total
             FROM ordinances
             WHERE ordinance_no LIKE :prefix",
            [':prefix' => 'ORD-' . $year . '-%'],
            true
        );

        $count = (int) ($result['total'] ?? 0);
        return ORD_PREFIX . '-' . $year . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Updates the status of an ordinance.
     *
     * @param  int    $id
     * @param  string $status
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }
}

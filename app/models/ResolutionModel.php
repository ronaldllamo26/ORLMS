<?php

/**
 * ORLMS - Resolution Model
 *
 * Handles all database operations for the `resolutions` table.
 *
 * Table columns:
 *   id, resolution_no, title, subject, content, author_id,
 *   committee_id, status, ai_summary, file_path,
 *   date_filed, created_at, updated_at
 */

class ResolutionModel extends Model
{
    protected string $table = 'resolutions';

    /**
     * Returns the most recent resolutions.
     *
     * @param  int $limit
     * @return array
     */
    public function getRecent(int $limit = 5): array
    {
        return $this->query(
            "SELECT r.*, u.name AS author_name
             FROM resolutions r
             LEFT JOIN users u ON r.author_id = u.id
             ORDER BY r.created_at DESC
             LIMIT :limit",
            [':limit' => $limit]
        );
    }

    /**
     * Returns all resolutions with author name via JOIN.
     *
     * @return array
     */
    public function getAllWithAuthor(): array
    {
        return $this->query(
            "SELECT r.*, u.name AS author_name, c.name AS committee_name
             FROM resolutions r
             LEFT JOIN users u ON r.author_id = u.id
             LEFT JOIN committees c ON r.committee_id = c.id
             ORDER BY r.created_at DESC"
        );
    }

    /**
     * Returns a single resolution by ID with author name.
     *
     * @param  int $id
     * @return array|false
     */
    public function getByIdWithAuthor(int $id): array|false
    {
        return $this->query(
            "SELECT r.*, u.name AS author_name, c.name AS committee_name
             FROM resolutions r
             LEFT JOIN users u ON r.author_id = u.id
             LEFT JOIN committees c ON r.committee_id = c.id
             WHERE r.id = :id
             LIMIT 1",
            [':id' => $id],
            true
        );
    }

    /**
     * Returns resolutions filtered by status.
     *
     * @param  string $status
     * @return array
     */
    public function getByStatus(string $status): array
    {
        return $this->query(
            "SELECT r.*, u.name AS author_name
             FROM resolutions r
             LEFT JOIN users u ON r.author_id = u.id
             WHERE r.status = :status
             ORDER BY r.created_at DESC",
            [':status' => $status]
        );
    }

    /**
     * Generates the next resolution number in sequence.
     * Format: RES-YYYY-NNN
     *
     * @return string
     */
    public function generateResolutionNo(): string
    {
        $year = date('Y');

        $result = $this->query(
            "SELECT COUNT(*) as total
             FROM resolutions
             WHERE resolution_no LIKE :prefix",
            [':prefix' => 'RES-' . $year . '-%'],
            true
        );

        $count = (int) ($result['total'] ?? 0);
        return RES_PREFIX . '-' . $year . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Updates the status of a resolution.
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

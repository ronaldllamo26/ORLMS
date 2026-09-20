<?php

/**
 * ORLMS - User Model
 *
 * Handles all database operations for the `users` table.
 * Also handles `audit_logs` table entries — since audit logging
 * is triggered by user actions across the entire system.
 *
 * Table: users
 * Columns: id, name, email, password, role, is_active, created_at
 */

class UserModel extends Model
{
    protected string $table = 'users';

    // ─────────────────────────────────────────────────────────────────────────
    // AUTH-RELATED METHODS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Finds a single user by email address.
     * Used by AuthController during login.
     *
     * @param  string $email
     * @return array|false
     */
    public function findByEmail(string $email): array|false
    {
        $this->ensureSecurityColumns();
        return $this->findOneWhere('email', $email);
    }

    /**
     * Check if a user's account is currently locked out due to excessive failed attempts.
     *
     * @param  array $user
     * @return bool
     */
    public function isAccountLocked(array $user): bool
    {
        if (!empty($user['lockout_until'])) {
            return strtotime($user['lockout_until']) > time();
        }
        return false;
    }

    /**
    /**
     * Get the remaining lockout duration in seconds.
     *
     * @param  array $user
     * @return int
     */
    public function getRemainingLockoutSeconds(array $user): int
    {
        if (!empty($user['lockout_until'])) {
            $diff = strtotime($user['lockout_until']) - time();
            return max(0, $diff);
        }
        return 0;
    }

    /**
     * Get the remaining lockout duration in minutes.
     *
     * @param  array $user
     * @return int
     */
    public function getRemainingLockoutMinutes(array $user): int
    {
        return (int) ceil($this->getRemainingLockoutSeconds($user) / 60);
    }

    /**
     * Record a failed login attempt for a user.
     * If attempts reach 3 or more, locks the account.
     * NOTE: Temporarily configured to 30 SECONDS for fast live oral defense demonstration
     * (Standard production policy: 15 MINUTES).
     *
     * @param  int $userId
     * @return array ['count' => int, 'locked' => bool, 'lockout_until' => ?string]
     */
    public function incrementFailedAttempts(int $userId): array
    {
        $this->ensureSecurityColumns();
        $user = $this->findById($userId);
        $currentAttempts = (int) ($user['failed_login_attempts'] ?? 0);
        $newAttempts = $currentAttempts + 1;
        $locked = false;
        $lockoutUntil = null;

        $db = \Database::getInstance()->getConnection();
        if ($newAttempts >= 3) {
            $locked = true;
            // 30 SECONDS for live oral defense demonstration (15 MINUTE in standard production)
            $stmt = $db->prepare(
                "UPDATE users 
                 SET failed_login_attempts = :attempts, 
                     lockout_until = DATE_ADD(NOW(), INTERVAL 30 SECOND) 
                 WHERE id = :id"
            );
            $stmt->execute([':attempts' => $newAttempts, ':id' => $userId]);

            $updatedUser = $this->findById($userId);
            $lockoutUntil = $updatedUser['lockout_until'] ?? date('Y-m-d H:i:s', time() + 30);

            $this->logAudit(
                $userId,
                'ACCOUNT_LOCKED',
                'users',
                $userId,
                ['failed_attempts' => $currentAttempts],
                ['failed_attempts' => $newAttempts, 'lockout_until' => $lockoutUntil, 'note' => '30s demonstration lockout']
            );
        } else {
            $stmt = $db->prepare("UPDATE users SET failed_login_attempts = :attempts WHERE id = :id");
            $stmt->execute([':attempts' => $newAttempts, ':id' => $userId]);
        }

        return [
            'count'         => $newAttempts,
            'locked'        => $locked,
            'lockout_until' => $lockoutUntil,
        ];
    }

    /**
     * Reset failed login attempts and clear lockout for a user upon successful authentication.
     *
     * @param  int $userId
     * @return void
     */
    public function resetFailedAttempts(int $userId): void
    {
        $this->ensureSecurityColumns();
        $db = \Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET failed_login_attempts = 0, lockout_until = NULL WHERE id = :id");
        $stmt->execute([':id' => $userId]);
    }

    /**
     * Safely ensure the users table has failed_login_attempts and lockout_until columns.
     */
    public function ensureSecurityColumns(): void
    {
        static $ensured = false;
        if ($ensured) {
            return;
        }
        try {
            $db = \Database::getInstance()->getConnection();
            $db->exec("ALTER TABLE users ADD COLUMN `failed_login_attempts` INT UNSIGNED NOT NULL DEFAULT 0");
        } catch (\Throwable $e) {}
        try {
            $db = \Database::getInstance()->getConnection();
            $db->exec("ALTER TABLE users ADD COLUMN `lockout_until` DATETIME DEFAULT NULL");
        } catch (\Throwable $e) {}

        // Ensure data_deletion_requests table exists for RA 10173 compliance
        try {
            $db = \Database::getInstance()->getConnection();
            $db->exec("CREATE TABLE IF NOT EXISTS `data_deletion_requests` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `ticket_no` VARCHAR(30) NOT NULL UNIQUE,
                `requester_name` VARCHAR(150) NOT NULL,
                `requester_email` VARCHAR(150) NOT NULL,
                `requester_phone` VARCHAR(50) NULL,
                `request_type` VARCHAR(50) NOT NULL DEFAULT 'erasure',
                `reason` TEXT NOT NULL,
                `status` ENUM('pending', 'in_review', 'completed', 'rejected') NOT NULL DEFAULT 'pending',
                `admin_notes` TEXT NULL,
                `ip_address` VARCHAR(45) NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (\Throwable $e) {}

        // Ensure default oral defense demo admin accounts exist with Password@123
        try {
            $db = \Database::getInstance()->getConnection();
            $hashedPwd = password_hash('Password@123', PASSWORD_BCRYPT);

            foreach (['admin@csjdm.gov.ph', 'admin@orlms.ph'] as $adminEmail) {
                $checkStmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
                $checkStmt->execute([':email' => $adminEmail]);
                $adminUser = $checkStmt->fetch();

                if ($adminUser) {
                    $updStmt = $db->prepare(
                        "UPDATE users 
                         SET password = :pwd, is_active = 1, failed_login_attempts = 0, lockout_until = NULL 
                         WHERE email = :email"
                    );
                    $updStmt->execute([':pwd' => $hashedPwd, ':email' => $adminEmail]);
                } else {
                    $insStmt = $db->prepare(
                        "INSERT INTO users (name, email, password, role, is_active, failed_login_attempts, lockout_until) 
                         VALUES ('City Administrator', :email, :pwd, 'super_admin', 1, 0, NULL)"
                    );
                    $insStmt->execute([':email' => $adminEmail, ':pwd' => $hashedPwd]);
                }
            }
        } catch (\Throwable $e) {}

        $ensured = true;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // USER MANAGEMENT METHODS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns all users ordered by newest first.
     *
     * @return array
     */
    public function getAllUsers(): array
    {
        return $this->query(
            "SELECT id, name, email, role, is_active, created_at
             FROM users
             ORDER BY created_at DESC"
        );
    }

    /**
     * Creates a new user with a securely hashed password.
     *
     * @param  string $name
     * @param  string $email
     * @param  string $plainPassword  Raw password — will be hashed here
     * @param  string $role
     * @return string|false           New user ID on success, false on failure
     */
    public function createUser(string $name, string $email, string $plainPassword, string $role): string|false
    {
        // Check if email is already taken
        if ($this->findByEmail($email)) {
            return false;
        }

        return $this->insert([
            'name'       => $name,
            'email'      => $email,
            'password'   => password_hash($plainPassword, PASSWORD_BCRYPT),
            'role'       => $role,
            'is_active'  => 1,
        ]);
    }

    /**
     * Updates a user's basic profile information.
     * Does not update password — use updatePassword() for that.
     *
     * @param  int    $id
     * @param  string $name
     * @param  string $email
     * @param  string $role
     * @return bool
     */
    public function updateUser(int $id, string $name, string $email, string $role): bool
    {
        return $this->update($id, [
            'name'  => $name,
            'email' => $email,
            'role'  => $role,
        ]);
    }

    /**
     * Updates a user's password.
     * Hashes the new password before saving.
     *
     * @param  int    $id
     * @param  string $plainPassword
     * @return bool
     */
    public function updatePassword(int $id, string $plainPassword): bool
    {
        return $this->update($id, [
            'password' => password_hash($plainPassword, PASSWORD_BCRYPT),
        ]);
    }

    /**
     * Activates or deactivates a user account.
     *
     * @param  int  $id
     * @param  bool $isActive
     * @return bool
     */
    public function setActiveStatus(int $id, bool $isActive): bool
    {
        return $this->update($id, [
            'is_active' => $isActive,
        ]);
    }

    /**
     * Returns the count of users per role.
     * Used on the Dashboard for statistics.
     *
     * @return array  Keys: role name, Values: count
     */
    public function countByRole(): array
    {
        $rows = $this->query(
            "SELECT role, COUNT(*) as total
             FROM users
             WHERE is_active = 1
             GROUP BY role"
        );

        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['role']] = (int) $row['total'];
        }
        return $counts;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AUDIT LOGGING
    // Called from any controller to record significant system events.
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Inserts a record into the audit_logs table.
     *
     * This method is intentionally in UserModel because all
     * auditable actions are tied to a user performing them.
     *
     * @param  int|null    $userId     The user performing the action
     * @param  string      $action     Action label (e.g. 'LOGIN', 'CREATE', 'APPROVE')
     * @param  string      $tableName  The affected table (e.g. 'ordinances')
     * @param  int|null    $recordId   The affected record's ID
     * @param  mixed       $oldValue   Previous value (will be JSON-encoded if array)
     * @param  mixed       $newValue   New value (will be JSON-encoded if array)
     * @return bool
     */
    public function logAudit(
        ?int $userId,
        string $action,
        string $tableName,
        ?int $recordId = null,
        mixed $oldValue = null,
        mixed $newValue = null
    ): bool {
        try {
            // Encode arrays/objects to JSON for storage
            $oldEncoded = is_array($oldValue) ? json_encode($oldValue) : $oldValue;
            $newEncoded = is_array($newValue) ? json_encode($newValue) : $newValue;

            // Get the requester's IP address and location safely
            $ip = class_exists('GeoIPHelper') ? GeoIPHelper::getClientIp() : ($_SERVER['REMOTE_ADDR'] ?? null);
            $location = class_exists('GeoIPHelper') && $ip ? GeoIPHelper::getLocation($ip) : null;

            $db = \Database::getInstance()->getConnection();

            // Auto-patch missing columns on audit_logs table if any
            $columnsToEnsure = [
                'table_name'   => "VARCHAR(100) DEFAULT NULL",
                'target_table' => "VARCHAR(100) DEFAULT NULL",
                'record_id'    => "INT UNSIGNED DEFAULT NULL",
                'target_id'    => "INT UNSIGNED DEFAULT NULL",
                'ip_address'   => "VARCHAR(45) DEFAULT NULL",
                'location'     => "VARCHAR(150) DEFAULT NULL",
                'details'      => "TEXT DEFAULT NULL",
                'old_value'    => "TEXT DEFAULT NULL",
                'new_value'    => "TEXT DEFAULT NULL",
            ];

            foreach ($columnsToEnsure as $col => $typeDef) {
                try {
                    $db->exec("ALTER TABLE audit_logs ADD COLUMN `{$col}` {$typeDef}");
                } catch (\Throwable $e) {}
            }

            // Perform robust direct PDO statement execution
            $stmt = $db->prepare(
                "INSERT INTO audit_logs
                    (user_id, action, table_name, target_table, record_id, target_id, old_value, new_value, ip_address, location, created_at)
                 VALUES
                    (:user_id, :action, :table_name, :target_table, :record_id, :target_id, :old_value, :new_value, :ip_address, :location, NOW())"
            );

            return $stmt->execute([
                ':user_id'      => $userId,
                ':action'       => strtoupper($action),
                ':table_name'   => $tableName,
                ':target_table' => $tableName,
                ':record_id'    => $recordId,
                ':target_id'    => $recordId,
                ':old_value'    => $oldEncoded,
                ':new_value'    => $newEncoded,
                ':ip_address'   => $ip,
                ':location'     => $location,
            ]);

        } catch (\Throwable $e) {
            error_log("logAudit exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Returns the latest audit log entries.
     * Includes the user's name via JOIN.
     *
     * @param  int $limit  Number of records to return
     * @return array
     */
    public function getRecentAuditLogs(int $limit = 50): array
    {
        return $this->query(
            "SELECT al.*, u.name AS user_name
             FROM audit_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC
             LIMIT :limit",
            [':limit' => $limit]
        );
    }
}

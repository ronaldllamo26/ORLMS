<?php

/**
 * ORLMS - Audit Logs Controller
 *
 * Displays a full read-only audit trail of all system actions.
 * Restricted to super_admin only.
 *
 * Routes:
 *   GET /audit_logs         → index()
 *   GET /audit_logs/view/{id} → view($id)
 */

class AuditLogsController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
        $this->requireRole([ROLE_SUPER_ADMIN]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — Full audit log listing with filters
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $db = \Database::getInstance()->getConnection();

        // Ensure columns exist on audit_logs
        try { $db->exec("ALTER TABLE audit_logs ADD COLUMN table_name VARCHAR(100) DEFAULT NULL"); } catch (\Throwable $e) {}
        try { $db->exec("ALTER TABLE audit_logs ADD COLUMN record_id INT UNSIGNED DEFAULT NULL"); } catch (\Throwable $e) {}

        // Filter params
        $filterAction = trim($_GET['action'] ?? '');
        $filterTable  = trim($_GET['table']  ?? '');
        $filterUser   = trim($_GET['user']   ?? '');
        $filterDate   = trim($_GET['date']   ?? '');

        $where  = ['1=1'];
        $params = [];

        if (!empty($filterAction)) {
            $where[]  = 'al.action = :action';
            $params[':action'] = $filterAction;
        }
        if (!empty($filterTable)) {
            $where[]  = '(al.table_name = :table_name OR al.target_table = :table_name)';
            $params[':table_name'] = $filterTable;
        }
        if (!empty($filterUser)) {
            $where[]  = 'u.name LIKE :user';
            $params[':user'] = '%' . $filterUser . '%';
        }
        if (!empty($filterDate)) {
            $where[]  = 'DATE(al.created_at) = :date';
            $params[':date'] = $filterDate;
        }

        $whereClause = implode(' AND ', $where);

        $stmt = $db->prepare(
            "SELECT al.*, COALESCE(al.table_name, al.target_table, '') AS table_name, u.name AS user_name, u.role AS user_role
             FROM audit_logs al
             LEFT JOIN users u ON al.user_id = u.id
             WHERE {$whereClause}
             ORDER BY al.created_at DESC
             LIMIT 500"
        );
        $stmt->execute($params);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get distinct actions and tables for filter dropdowns
        $allActions = [];
        try {
            $actionsStmt = $db->query("SELECT DISTINCT action FROM audit_logs ORDER BY action ASC");
            $allActions = $actionsStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (\Throwable $e) {}

        $allTables = [];
        try {
            $tablesStmt = $db->query("SELECT DISTINCT COALESCE(table_name, target_table) FROM audit_logs WHERE table_name IS NOT NULL OR target_table IS NOT NULL ORDER BY 1 ASC");
            $allTables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (\Throwable $e) {}

        $this->render('audit_logs/index', [
            'pageTitle'    => 'Audit Logs',
            'logs'         => $logs,
            'allActions'   => $allActions,
            'allTables'    => $allTables,
            'filterAction' => $filterAction,
            'filterTable'  => $filterTable,
            'filterUser'   => $filterUser,
            'filterDate'   => $filterDate,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VIEW — Detail view of a single audit log entry
    // ─────────────────────────────────────────────────────────────────────────

    public function view(string $id): void
    {
        $db   = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "SELECT al.*, u.name AS user_name, u.role AS user_role, u.email AS user_email
             FROM audit_logs al
             LEFT JOIN users u ON al.user_id = u.id
             WHERE al.id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => (int) $id]);
        $log = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$log) {
            $this->flash('error', 'Audit log entry not found.');
            $this->redirect('audit_logs');
        }

        // Decode JSON values
        $oldValue = null;
        $newValue = null;

        if (!empty($log['old_value'])) {
            $decoded = json_decode($log['old_value'], true);
            $oldValue = $decoded ?? $log['old_value'];
        }
        if (!empty($log['new_value'])) {
            $decoded = json_decode($log['new_value'], true);
            $newValue = $decoded ?? $log['new_value'];
        }

        $this->render('audit_logs/view', [
            'pageTitle' => 'Audit Log #' . $id,
            'log'       => $log,
            'oldValue'  => $oldValue,
            'newValue'  => $newValue,
        ]);
    }
}

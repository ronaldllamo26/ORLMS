<?php

/**
 * ORLMS - Database Backup & CSV Export Controller
 *
 * Provides Super Admin with 1-click utilities for:
 *   1. Viewing System Health & Database Backup Status
 *   2. Downloading full PostgreSQL database backup (.sql file)
 *   3. Exporting ordinances and resolutions to CSV (Excel format)
 */
class BackupController extends Controller
{
    /** @var UserModel */
    private UserModel $userModel;

    /** @var OrdinanceModel */
    private OrdinanceModel $ordinanceModel;

    /** @var ResolutionModel */
    private ResolutionModel $resolutionModel;

    public function __construct()
    {
        $this->requireLogin();
        // Only Super Admin can access backup utilities
        $this->requireRole(ROLE_SUPER_ADMIN);

        $this->userModel       = $this->model('UserModel');
        $this->ordinanceModel  = $this->model('OrdinanceModel');
        $this->resolutionModel = $this->model('ResolutionModel');
    }

    /**
     * Renders the System Backup & Export Dashboard
     */
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();

        // Get basic database statistics
        $tables = ['ordinances', 'resolutions', 'users', 'ai_validation_reports', 'audit_logs', 'public_consultations', 'publications'];
        $tableCounts = [];

        foreach ($tables as $table) {
            try {
                $stmt = $db->query("SELECT COUNT(*) AS total FROM {$table}");
                $tableCounts[$table] = (int) $stmt->fetchColumn();
            } catch (Exception $e) {
                $tableCounts[$table] = 0;
            }
        }

        $this->render('backup/index', [
            'pageTitle'   => 'System Backup & Data Export',
            'tableCounts' => $tableCounts,
        ]);
    }

    /**
     * Triggers browser download of a full database SQL export script
     */
    public function downloadSql(): void
    {
        $db = Database::getInstance()->getConnection();
        $filename = 'orlms_backup_' . date('Y-m-d_H-i-s') . '.sql';

        $output = "-- ORLMS Database Backup Export\n";
        $output .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- Host: " . DB_HOST . " | Database: " . DB_NAME . "\n\n";

        $tables = ['users', 'committees', 'ordinances', 'resolutions', 'amendments', 'ai_validation_reports', 'public_consultations', 'publications', 'audit_logs'];

        foreach ($tables as $table) {
            try {
                $stmt = $db->query("SELECT * FROM {$table}");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($rows)) {
                    $output .= "-- Data for table {$table}\n";
                    foreach ($rows as $row) {
                        $cols = array_keys($row);
                        $vals = array_map(function ($val) use ($db) {
                            if ($val === null) return 'NULL';
                            return $db->quote($val);
                        }, array_values($row));

                        $output .= "INSERT INTO {$table} (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ");\n";
                    }
                    $output .= "\n";
                }
            } catch (Exception $e) {
                $output .= "-- Error exporting table {$table}: " . $e->getMessage() . "\n\n";
            }
        }

        $this->userModel->logAudit(
            $this->userId(),
            'DATABASE_BACKUP',
            'database',
            null,
            null,
            ['filename' => $filename]
        );

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($output));
        echo $output;
        exit;
    }

    /**
     * Triggers browser download of a CSV export file containing all legislative documents
     */
    public function exportCsv(): void
    {
        $filename = 'orlms_documents_export_' . date('Y-m-d') . '.csv';

        $ordinances  = $this->ordinanceModel->getAllWithAuthor() ?: [];
        $resolutions = $this->resolutionModel->getAllWithAuthor() ?: [];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        // Add UTF-8 BOM for Microsoft Excel compatibility
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        // CSV Header
        fputcsv($out, ['Type', 'Tracking No', 'Title', 'Subject', 'Author', 'Committee', 'Status', 'Date Filed', 'Created At']);

        foreach ($ordinances as $ord) {
            fputcsv($out, [
                'Ordinance',
                $ord['ordinance_no'] ?? 'N/A',
                $ord['title'] ?? '',
                $ord['subject'] ?? '',
                $ord['author_name'] ?? 'N/A',
                $ord['committee_name'] ?? 'Unassigned',
                ucfirst($ord['status'] ?? 'draft'),
                $ord['date_filed'] ?? 'N/A',
                $ord['created_at'] ?? ''
            ]);
        }

        foreach ($resolutions as $res) {
            fputcsv($out, [
                'Resolution',
                $res['resolution_no'] ?? 'N/A',
                $res['title'] ?? '',
                $res['subject'] ?? '',
                $res['author_name'] ?? 'N/A',
                $res['committee_name'] ?? 'Unassigned',
                ucfirst($res['status'] ?? 'draft'),
                $res['date_filed'] ?? 'N/A',
                $res['created_at'] ?? ''
            ]);
        }

        fclose($out);

        $this->userModel->logAudit(
            $this->userId(),
            'EXPORT_CSV',
            'ordinances',
            null,
            null,
            ['filename' => $filename, 'records' => count($ordinances) + count($resolutions)]
        );

        exit;
    }
}

<?php

/**
 * ORLMS - Public Search Portal Controller
 *
 * Accessible to anonymous guest users (the general public) without logging in.
 * Enables lookup of officially published ordinances and resolutions.
 *
 * Routes:
 *   GET /portal                 → index()
 *   GET /portal/view/{type}/{id}→ view($type, $id)
 */

class PortalController extends Controller
{
    // NO requireLogin() in the constructor to allow public access!

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — Public search page
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $db = \Database::getInstance()->getConnection();

        // Search params
        $search = trim($_GET['search'] ?? '');
        $type   = trim($_GET['type'] ?? '');
        $year   = trim($_GET['year'] ?? '');

        $where  = ['p.id IS NOT NULL'];
        $params = [];

        if (!empty($search)) {
            $where[] = '(o.title LIKE :search OR o.subject LIKE :search OR o.content LIKE :search OR r.title LIKE :search OR r.subject LIKE :search OR r.content LIKE :search OR p.plain_summary LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        if (!empty($type) && in_array($type, ['ordinance', 'resolution'])) {
            $where[] = 'p.document_type = :type';
            $params[':type'] = $type;
        }
        if (!empty($year) && preg_match('/^\d{4}$/', $year)) {
            $where[] = '( (p.document_type = \'ordinance\' AND o.ordinance_no LIKE :year) OR (p.document_type = \'resolution\' AND r.resolution_no LIKE :year) )';
            $params[':year'] = '%-' . $year . '-%';
        }

        $whereClause = implode(' AND ', $where);

        $stmt = $db->prepare(
            "SELECT p.*,
                    u.name AS published_by_name,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.ordinance_no
                        WHEN 'resolution' THEN r.resolution_no
                    END AS doc_no,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.title
                        WHEN 'resolution' THEN r.title
                    END AS doc_title,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.subject
                        WHEN 'resolution' THEN r.subject
                    END AS doc_subject,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.date_filed
                        WHEN 'resolution' THEN r.date_filed
                    END AS date_filed,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN au.name
                        WHEN 'resolution' THEN ru.name
                    END AS author_name
             FROM publications p
             LEFT JOIN users u ON p.published_by = u.id
             LEFT JOIN ordinances o ON p.document_type = 'ordinance' AND p.document_id = o.id
             LEFT JOIN users au ON o.author_id = au.id
             LEFT JOIN resolutions r ON p.document_type = 'resolution' AND p.document_id = r.id
             LEFT JOIN users ru ON r.author_id = ru.id
             WHERE {$whereClause}
             ORDER BY p.published_at DESC"
        );
        $stmt->execute($params);
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch distinct years from published dates for filter dropdown
        $yearsStmt = $db->query(
            "SELECT DISTINCT CASE p.document_type
                WHEN 'ordinance'  THEN SUBSTRING(o.ordinance_no, 5, 4)
                WHEN 'resolution' THEN SUBSTRING(r.resolution_no, 5, 4)
             END AS pub_year
             FROM publications p
             LEFT JOIN ordinances o ON p.document_type = 'ordinance' AND p.document_id = o.id
             LEFT JOIN resolutions r ON p.document_type = 'resolution' AND p.document_id = r.id
             ORDER BY pub_year DESC"
        );
        $availableYears = array_filter($yearsStmt->fetchAll(PDO::FETCH_COLUMN));

        $this->renderPublic('portal/index', [
            'pageTitle'      => 'Public Document Registry',
            'publications'   => $publications,
            'search'         => $search,
            'type'           => $type,
            'year'           => $year,
            'availableYears' => $availableYears,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VIEW — Public detail view page
    // ─────────────────────────────────────────────────────────────────────────

    public function view(string $type, string $id): void
    {
        if (!in_array($type, ['ordinance', 'resolution'])) {
            $this->redirect('portal');
        }

        $db   = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "SELECT p.*,
                    u.name AS published_by_name,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.ordinance_no
                        WHEN 'resolution' THEN r.resolution_no
                    END AS doc_no,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.title
                        WHEN 'resolution' THEN r.title
                    END AS doc_title,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.content
                        WHEN 'resolution' THEN r.content
                    END AS doc_content,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.subject
                        WHEN 'resolution' THEN r.subject
                    END AS doc_subject,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.date_filed
                        WHEN 'resolution' THEN r.date_filed
                    END AS date_filed,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN au.name
                        WHEN 'resolution' THEN ru.name
                    END AS author_name
             FROM publications p
             LEFT JOIN users u ON p.published_by = u.id
             LEFT JOIN ordinances o ON p.document_type='ordinance' AND p.document_id=o.id
             LEFT JOIN users au ON o.author_id = au.id
             LEFT JOIN resolutions r ON p.document_type='resolution' AND p.document_id=r.id
             LEFT JOIN users ru ON r.author_id = ru.id
             WHERE p.document_type = :type AND p.document_id = :id
             LIMIT 1"
        );
        $stmt->execute([':type' => $type, ':id' => (int) $id]);
        $publication = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$publication) {
            $this->redirect('portal');
        }

        $this->renderPublic('portal/view', [
            'pageTitle'   => $publication['doc_no'] ?? 'Document Registry',
            'publication' => $publication,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE RENDERER FOR PUBLIC VIEWS (WITHOUT SIDEBAR)
    // ─────────────────────────────────────────────────────────────────────────

    private function renderPublic(string $view, array $data = []): void
    {
        $viewPath = APP_ROOT . '/views/' . $view . '.php';

        if (!empty($data)) {
            extract($data);
        }

        ob_start();
        require_once $viewPath;
        $content = ob_get_clean();

        require_once APP_ROOT . '/views/layouts/public.php';
    }
}

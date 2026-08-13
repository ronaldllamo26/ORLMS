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

        // Fetch public consultations history
        $consultationModel = $this->model('ConsultationModel');
        $consultations     = $consultationModel->getForDocument($type, (int)$id);

        $this->renderPublic('portal/view', [
            'pageTitle'     => $publication['doc_no'] ?? 'Document Registry',
            'publication'   => $publication,
            'consultations' => $consultations,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VERIFY — Public document verification page
    // ─────────────────────────────────────────────────────────────────────────

    public function verify(string $type, string $id): void
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
            $this->renderPublic('portal/verify_failed', [
                'pageTitle' => 'Verification Failed',
                'type'      => $type,
                'id'        => $id,
            ]);
            return;
        }

        $this->renderPublic('portal/verify', [
            'pageTitle'   => 'Verify: ' . ($publication['doc_no'] ?? 'Document'),
            'publication' => $publication,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CHAT — AJAX endpoint for AI public chat validation
    // ─────────────────────────────────────────────────────────────────────────

    public function chat(): void
    {
        header('Content-Type: application/json');

        $message = trim($_POST['message'] ?? '');
        if (empty($message)) {
            echo json_encode(['success' => false, 'reply' => 'Mag-type ng tanong po.']);
            exit;
        }

        $db = \Database::getInstance()->getConnection();
        
        // Fetch all published documents, including their full text content, to feed to the AI context
        $stmt = $db->query(
            "SELECT p.document_type,
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
                    p.plain_summary
             FROM publications p
             LEFT JOIN ordinances o ON p.document_type = 'ordinance' AND p.document_id = o.id
             LEFT JOIN resolutions r ON p.document_type = 'resolution' AND p.document_id = r.id
             ORDER BY p.published_at DESC"
        );
        $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Build an ultra-smart, conversational, and grounded system prompt for Tanya SP
        $systemPrompt = "Ikaw si 'Tanya SP', ang matalino, magalang, at makabagong AI Legislative Assistant ng Sangguniang Panlungsod ng San Jose del Monte, Bulacan.\n\n" .
                        "IKAW AY DISENYADONG SUPORTAHAN ANG MGA MAMAMAYAN SA NATURAL AT KASWAL NA MGA TANONG (CASUAL INTENT DETECTION):\n" .
                        "1. Maunawaan ang mga kaswal, balbal, o pang-araw-araw na pananalita ng mga mamamayan (halimbawa: 'pwede ba maglakad ng nakahubad sa labas?', 'bawal ba mag-ingay kapag gabi?', 'magkano multa sa MTR o sa sidewalk?', 'kailan curfew ng kabataan?').\n" .
                        "2. KAPAG MAY NAGTANONG NG KASWAL NA HAKBANG O PERMISYO SA LIPUNAN:\n" .
                        "   - Suriin muna kung may kaugnay na Ordinansa o Resolusyon sa ating lokal na database sa ibaba.\n" .
                        "   - Ipaliwanag sa malinaw at madaling maunawaang paraan ang patakaran sa ilalim ng Lokal na Batas ng CSJDM at Pambansang Batas ng Pilipinas (halimbawa: Ang paglalakad nang nakahubad sa pampublikong lugar ay itinuturing na paglabag sa Public Decency / Grave Scandal sa ilalim ng Revised Penal Code at mga lokal na ordinansa sa pampublikong kaayusan).\n" .
                        "   - Magbigay ng payo o patnubay nang magalang at edukado.\n" .
                        "3. PANGANGASIWA SA FORMAT NG SAGOT:\n" .
                        "   - Magbigay ng detalyado, komprehensibo, ngunit madaling maunawaang sagot sa Tagalog o Taglish.\n" .
                        "   - Gumamit ng markdown formatting:\n" .
                        "     * Gamitin ang **bold text** para i-highlight ang mga mahahalagang salita (hal. **Bawal**, **Multa: ₱1,000**, **Curfew: 10 PM**).\n" .
                        "     * Gamitin ang bulleted lists kapag naglilista ng mga probisyon, parusa, o paalala.\n" .
                        "   - Kung tumutugma sa opisyal na ordinansa sa ating database, ibigay ang opisyal na Document No. (hal. Ordinance No. ORD-2026-0002) at ang Pamagat nito.\n" .
                        "   - Panatilihing magalang, mabait, at gumamit ng 'po' at 'opo'.\n\n" .
                        "LISTAHAN NG MGA OPISYAL NA PUBLISHED DOCUMENTS SA ATING DATABASE:\n";

        if (empty($docs)) {
            $systemPrompt .= "(Kasalukuyang walang nakarehistrong published documents sa database, ngunit maaari ka pa ring magbigay ng pangkalahatang legal at sibil na gabay bilang AI Assistant ng CSJDM.)";
        } else {
            foreach ($docs as $doc) {
                $docTypeLabel = $doc['document_type'] === 'ordinance' ? 'ORDINANCE' : 'RESOLUTION';
                $systemPrompt .= "- [{$docTypeLabel} {$doc['doc_no']}]\n" .
                                 "  Title: {$doc['doc_title']}\n" .
                                 "  Summary: {$doc['plain_summary']}\n" .
                                 "  Full Document Text:\n  " . str_replace("\n", "\n  ", $doc['doc_content']) . "\n\n";
            }
        }

        // Prepare the payload for Groq
        $payload = json_encode([
            'model'       => GROQ_MODEL,
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $message],
            ],
            'temperature' => 0.4, // Natural conversational intelligence
            'max_tokens'  => 1000,
        ]);

        $ch = curl_init(GROQ_API_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . GROQ_API_KEY,
            ],
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode !== 200) {
            echo json_encode(['success' => false, 'reply' => 'Paumanhin po, nagkaroon ng aberya sa pagkonekta sa AI assistant. Subukan muli mamaya.']);
            exit;
        }

        $responseData = json_decode($response, true);
        $reply = $responseData['choices'][0]['message']['content'] ?? 'Paumanhin po, hindi ko mahanap ang tugma sa aking rekord.';

        echo json_encode(['success' => true, 'reply' => $reply]);
        exit;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE RENDERER FOR PUBLIC VIEWS (WITHOUT SIDEBAR)
    // ─────────────────────────────────────────────────────────────────────────

    private function renderPublic(string $view, array $data = []): void
    {
        $viewPath = ROOT . '/src/pages/' . $view . '.php';

        if (!empty($data)) {
            extract($data);
        }

        ob_start();
        require_once $viewPath;
        $content = ob_get_clean();

        require_once ROOT . '/src/pages/layouts/public.php';
    }
}

<?php

/**
 * ORLMS - AI Validation Model
 *
 * Handles all database operations for the ai_validation_reports table.
 * Also contains the Groq API call logic for running AI validation.
 *
 * Table: ai_validation_reports
 */

class AiValidationModel extends Model
{
    protected string $table = 'ai_validation_reports';

    // ─────────────────────────────────────────────────────────────────────────
    // FETCH METHODS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns all validation reports with document details.
     *
     * @return array
     */
    public function getAllReports(): array
    {
        return $this->query(
            "SELECT r.*,
                    u.name AS validated_by_name,
                    CASE
                        WHEN r.document_type = 'ordinance'
                        THEN o.ordinance_no
                        ELSE res.resolution_no
                    END AS document_no,
                    CASE
                        WHEN r.document_type = 'ordinance'
                        THEN o.title
                        ELSE res.title
                    END AS document_title
             FROM ai_validation_reports r
             LEFT JOIN users u ON r.validated_by = u.id
             LEFT JOIN ordinances o ON r.document_type = 'ordinance' AND r.document_id = o.id
             LEFT JOIN resolutions res ON r.document_type = 'resolution' AND r.document_id = res.id
             ORDER BY r.created_at DESC"
        );
    }

    /**
     * Returns the latest AI validation report for a specific document.
     *
     * @param  string $type  'ordinance' or 'resolution'
     * @param  int    $id
     * @return array|false
     */
    public function getLatestForDocument(string $type, int $id): array|false
    {
        return $this->query(
            "SELECT * FROM ai_validation_reports
             WHERE document_type = :type AND document_id = :id
             ORDER BY created_at DESC
             LIMIT 1",
            [':type' => $type, ':id' => $id],
            true
        );
    }

    /**
     * Returns all reports for a specific document (history).
     *
     * @param  string $type
     * @param  int    $id
     * @return array
     */
    public function getHistoryForDocument(string $type, int $id): array
    {
        return $this->query(
            "SELECT r.*, u.name AS validated_by_name
             FROM ai_validation_reports r
             LEFT JOIN users u ON r.validated_by = u.id
             WHERE r.document_type = :type AND r.document_id = :id
             ORDER BY r.created_at DESC",
            [':type' => $type, ':id' => $id]
        );
    }

    /**
     * Returns a single report by ID with document details.
     *
     * @param  int $id
     * @return array|false
     */
    public function getReportById(int $id): array|false
    {
        return $this->query(
            "SELECT r.*,
                    u.name AS validated_by_name,
                    CASE
                        WHEN r.document_type = 'ordinance'
                        THEN o.ordinance_no
                        ELSE res.resolution_no
                    END AS document_no,
                    CASE
                        WHEN r.document_type = 'ordinance'
                        THEN o.title
                        ELSE res.title
                    END AS document_title
             FROM ai_validation_reports r
             LEFT JOIN users u ON r.validated_by = u.id
             LEFT JOIN ordinances o ON r.document_type = 'ordinance' AND r.document_id = o.id
             LEFT JOIN resolutions res ON r.document_type = 'resolution' AND r.document_id = res.id
             WHERE r.id = :id
             LIMIT 1",
            [':id' => $id],
            true
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GROQ API — MAIN VALIDATION METHOD
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Runs the AI validation for a given document.
     *
     * Steps:
     * 1. Fetches the document content
     * 2. Fetches existing enacted/published docs for comparison
     * 3. Builds the prompt and calls the Groq API
     * 4. Parses and saves the result
     * 5. Returns the saved report ID or false on failure
     *
     * @param  string $type        'ordinance' or 'resolution'
     * @param  int    $documentId
     * @param  int    $userId      Who triggered the validation
     * @return int|false           The new report ID, or false on failure
     */
    public function runValidation(string $type, int $documentId, int $userId): int|false
    {
        // ── Step 1: Fetch the document ────────────────────────────────────────
        $document = $this->fetchDocument($type, $documentId);
        if (!$document) {
            return false;
        }

        // ── Step 2: Fetch existing enacted/published docs for comparison ──────
        $existingDocs = $this->fetchExistingDocuments($type, $documentId);

        // ── Step 3: Build prompt and call Groq API ────────────────────────────
        $aiResult = $this->callGroqApi($document, $existingDocs, $type);
        if (!$aiResult) {
            return false;
        }

        // ── Step 4: Save the report ───────────────────────────────────────────
        $completenessDetails = json_encode($aiResult['completeness'] ?? []);
        $similarityDetails   = json_encode($aiResult['similarity'] ?? []);

        $reportId = $this->insert([
            'document_type'        => $type,
            'document_id'          => $documentId,
            'validation_status'    => $aiResult['overall_status'] ?? 'flagged',
            'completeness_score'   => (int) ($aiResult['completeness']['score'] ?? 0),
            'similarity_score'     => (float) ($aiResult['similarity']['score'] ?? 0),
            'similar_document_type'=> $aiResult['similarity']['most_similar_document_type'] ?? null,
            'similar_document_id'  => $aiResult['similarity']['most_similar_document_id'] ?? null,
            'similar_document_no'  => $aiResult['similarity']['most_similar_document_no'] ?? null,
            'completeness_details' => $completenessDetails,
            'similarity_details'   => $similarityDetails,
            'ai_summary'           => $aiResult['summary'] ?? null,
            'recommendation'       => $aiResult['recommendation'] ?? null,
            'raw_response'         => json_encode($aiResult),
            'validated_by'         => $userId,
        ]);

        return $reportId ? (int) $reportId : false;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Fetches a document from the ordinances or resolutions table.
     *
     * @param  string $type
     * @param  int    $id
     * @return array|false
     */
    private function fetchDocument(string $type, int $id): array|false
    {
        $table = $type === 'ordinance' ? 'ordinances' : 'resolutions';
        $noCol = $type === 'ordinance' ? 'ordinance_no' : 'resolution_no';

        return $this->query(
            "SELECT * FROM {$table} WHERE id = :id LIMIT 1",
            [':id' => $id],
            true
        );
    }

    /**
     * Fetches existing enacted or published documents for comparison.
     * Limits content to 600 characters per document to stay within token limits.
     *
     * @param  string $type
     * @param  int    $excludeId  Current document ID to exclude
     * @return array
     */
    private function fetchExistingDocuments(string $type, int $excludeId): array
    {
        // Fetch ordinances
        $ordinances = $this->query(
            "SELECT ordinance_no AS doc_no, title, LEFT(content, 600) AS excerpt, status
             FROM ordinances
             WHERE status IN ('enacted','published','approved')
             AND id != :exclude
             ORDER BY created_at DESC
             LIMIT 10",
            [':exclude' => $type === 'ordinance' ? $excludeId : 0]
        );

        // Fetch resolutions
        $resolutions = $this->query(
            "SELECT resolution_no AS doc_no, title, LEFT(content, 600) AS excerpt, status
             FROM resolutions
             WHERE status IN ('enacted','published','approved','endorsed')
             AND id != :exclude
             ORDER BY created_at DESC
             LIMIT 10",
            [':exclude' => $type === 'resolution' ? $excludeId : 0]
        );

        return array_merge(
            array_map(fn($d) => array_merge($d, ['type' => 'ordinance']), $ordinances),
            array_map(fn($d) => array_merge($d, ['type' => 'resolution']), $resolutions)
        );
    }

    /**
     * Builds the prompt and calls the Groq API.
     * Returns parsed JSON result or false on failure.
     *
     * @param  array  $document
     * @param  array  $existingDocs
     * @param  string $type
     * @return array|false
     */
    private function callGroqApi(array $document, array $existingDocs, string $type): array|false
    {
        $noField    = $type === 'ordinance' ? 'ordinance_no' : 'resolution_no';
        $docNo      = $document[$noField] ?? 'N/A';
        $docTitle   = $document['title'] ?? '';
        $docSubject = $document['subject'] ?? '';
        // Limit content to ~3000 chars to avoid token limits
        $docContent = mb_substr($document['content'] ?? '', 0, 3000);

        // Build existing docs section
        $existingList = '';
        if (!empty($existingDocs)) {
            foreach ($existingDocs as $i => $ed) {
                $existingList .= ($i + 1) . ". [{$ed['type']}] {$ed['doc_no']}: {$ed['title']}\n";
                $existingList .= "   Excerpt: " . mb_substr($ed['excerpt'], 0, 400) . "\n\n";
            }
        } else {
            $existingList = "No existing enacted documents found for comparison.\n";
        }

        $systemPrompt = "You are an AI legal document validator for a Philippine local government legislative management system (ORLMS). You validate ordinances and resolutions for completeness and similarity to existing documents. Always respond with valid JSON only — no markdown fences, no extra text outside the JSON object.";

        $userPrompt = <<<PROMPT
Validate the following {$type}:

DOCUMENT TO VALIDATE:
Number: {$docNo}
Title: {$docTitle}
Subject: {$docSubject}
Content (may be truncated):
{$docContent}

---

EXISTING ENACTED/PUBLISHED DOCUMENTS FOR SIMILARITY COMPARISON:
{$existingList}

---

Respond with ONLY this exact JSON structure (no markdown, no extra text):
{
  "completeness": {
    "score": <integer 0-100>,
    "checks": {
      "has_whereas": <true or false>,
      "has_enacting_clause": <true or false - "NOW THEREFORE" for ordinances, "BE IT RESOLVED" for resolutions>,
      "has_separability_clause": <true or false>,
      "has_repealing_clause": <true or false>,
      "has_effectivity_clause": <true or false>
    },
    "missing_sections": [<list of missing section names as strings>],
    "notes": "<brief explanation of completeness assessment>"
  },
  "similarity": {
    "score": <integer 0-100, where 0=completely unique, 100=identical>,
    "most_similar_document_no": "<document number or null>",
    "most_similar_document_type": "<ordinance or resolution or null>",
    "most_similar_document_id": <null>,
    "explanation": "<brief explanation of similarity findings>"
  },
  "summary": "<2-3 sentence plain-language summary of what this document does>",
  "overall_status": "<passed or flagged or failed>",
  "recommendation": "<brief recommendation for the legislative committee reviewers>"
}

Rules for overall_status:
- "passed"  = completeness score >= 80 AND similarity score <= 30
- "flagged" = completeness score >= 60 AND similarity score <= 60 (but not passed)
- "failed"  = completeness score < 60 OR similarity score > 60
PROMPT;

        // ── cURL call to Groq API ─────────────────────────────────────────────
        $payload = json_encode([
            'model'       => GROQ_MODEL,
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $userPrompt],
            ],
            'temperature'     => 0.2,
            'max_tokens'      => 1500,
            'response_format' => ['type' => 'json_object'],
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

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Handle cURL or HTTP errors
        if ($curlError || $httpCode !== 200) {
            error_log('ORLMS AI Validation Error — HTTP ' . $httpCode . ': ' . ($curlError ?: $response));
            return false;
        }

        // Parse the Groq API response
        $responseData = json_decode($response, true);
        $content      = $responseData['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            error_log('ORLMS AI Validation Error — Empty content from API');
            return false;
        }

        // Parse the AI's JSON response
        $result = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($result)) {
            error_log('ORLMS AI Validation Error — Invalid JSON from AI: ' . $content);
            return false;
        }

        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STATS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns counts of reports by validation status.
     *
     * @return array
     */
    public function getStatusCounts(): array
    {
        $rows = $this->query(
            "SELECT validation_status, COUNT(*) as total
             FROM ai_validation_reports
             GROUP BY validation_status"
        );
        $counts = ['passed' => 0, 'flagged' => 0, 'failed' => 0];
        foreach ($rows as $row) {
            $counts[$row['validation_status']] = (int) $row['total'];
        }
        return $counts;
    }
}

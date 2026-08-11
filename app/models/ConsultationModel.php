<?php

/**
 * ORLMS - Consultation Model
 *
 * Handles database operations for the `public_consultations` table.
 */
class ConsultationModel extends Model
{
    protected string $table = 'public_consultations';

    /**
     * Gets all public consultations for a specific document.
     *
     * @param string $docType ('ordinance' or 'resolution')
     * @param int $docId
     * @return array
     */
    public function getForDocument(string $docType, int $docId): array
    {
        return $this->query(
            "SELECT * FROM public_consultations
             WHERE document_type = :doc_type AND document_id = :doc_id
             ORDER BY hearing_date DESC, created_at DESC",
            [':doc_type' => $docType, ':doc_id' => $docId]
        );
    }
}

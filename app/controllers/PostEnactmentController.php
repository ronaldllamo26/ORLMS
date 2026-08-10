<?php

/**
 * ORLMS - Post Enactment Controller
 *
 * Handles the manual update of statuses by the Legislative Staff
 * for documents that have passed the 3rd reading (Enacted).
 * This acts as the digital twin for offline processes like
 * Mayor's signature and Provincial SP review.
 */
class PostEnactmentController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
        // Only Legislative Staff and Super Admin can access this
        $this->requireRole([ROLE_LEGISLATIVE_STAFF, ROLE_SUPER_ADMIN]);
    }

    /**
     * Display list of Enacted documents awaiting post-enactment steps
     */
    public function index(): void
    {
        $ordinanceModel  = $this->model('OrdinanceModel');
        $resolutionModel = $this->model('ResolutionModel');

        // Fetch documents that are past the 3rd reading but not yet published or archived
        // Statuses included: enacted, certified, signed_lce, vetoed, sp_review_approved, sp_review_comments
        $activeStatuses = [
            STATUS_ENACTED,
            STATUS_CERTIFIED,
            STATUS_SIGNED_LCE,
            STATUS_VETOED,
            STATUS_SP_REVIEW_APPROVED,
            STATUS_SP_REVIEW_COMMENTS,
            STATUS_SP_REVIEW_DISAPPROVED
        ];

        $ordinances = $ordinanceModel->query(
            "SELECT o.*, u.name AS author_name 
             FROM ordinances o 
             LEFT JOIN users u ON o.author_id = u.id 
             WHERE o.status IN ('" . implode("','", $activeStatuses) . "')
             ORDER BY o.created_at DESC"
        ) ?: [];

        $resolutions = $resolutionModel->query(
            "SELECT r.*, u.name AS author_name 
             FROM resolutions r 
             LEFT JOIN users u ON r.author_id = u.id 
             WHERE r.status IN ('" . implode("','", $activeStatuses) . "')
             ORDER BY r.created_at DESC"
        ) ?: [];

        $this->render('post_enactment/index', [
            'pageTitle'   => 'Post-Enactment Tracking',
            'ordinances'  => $ordinances,
            'resolutions' => $resolutions
        ]);
    }

    /**
     * Handle the status update action
     */
    public function updateStatus(): void
    {
        if (!$this->isPost()) {
            $this->redirect('post_enactment');
        }

        $id     = (int) $this->post('id');
        $type   = $this->post('type'); // 'ordinance' or 'resolution'
        $status = $this->post('new_status');

        if (!$id || !$type || !$status) {
            $this->flash('error', 'Invalid request parameters.');
            $this->redirect('post_enactment');
        }

        $model = ($type === 'ordinance') 
                 ? $this->model('OrdinanceModel') 
                 : $this->model('ResolutionModel');

        // Simple validation to ensure only allowed statuses are set here
        $allowed = [
            STATUS_CERTIFIED,
            STATUS_SIGNED_LCE,
            STATUS_VETOED,
            STATUS_SP_REVIEW_APPROVED,
            STATUS_SP_REVIEW_DISAPPROVED,
            STATUS_SP_REVIEW_COMMENTS,
            STATUS_PUBLISHED
        ];

        if (in_array($status, $allowed)) {
            $model->updateStatus($id, $status);
            $this->flash('success', 'Document status successfully updated.');
        } else {
            $this->flash('error', 'Invalid status selection.');
        }

        $this->redirect('post_enactment');
    }
}

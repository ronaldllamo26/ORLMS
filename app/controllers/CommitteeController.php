<?php

/**
 * ORLMS - Committee Controller
 *
 * Handles creation, modification, and management of legislative committees.
 * Restricted to super_admin only.
 *
 * Routes:
 *   GET  /committee               → index()
 *   GET  /committee/create        → create()
 *   POST /committee/create        → create()
 *   GET  /committee/edit/{id}     → edit($id)
 *   POST /committee/edit/{id}     → edit($id)
 *   POST /committee/toggle/{id}   → toggle($id)
 */

class CommitteeController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
        $this->requireRole([ROLE_SUPER_ADMIN]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — List all committees
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $db = \Database::getInstance()->getConnection();

        // Fetch all committees with chairperson's name
        $stmt = $db->prepare(
            "SELECT c.*, u.name AS chairperson_name
             FROM committees c
             LEFT JOIN users u ON c.chairperson_id = u.id
             ORDER BY c.name ASC"
        );
        $stmt->execute();
        $committees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('committees/index', [
            'pageTitle'  => 'Committees',
            'committees' => $committees,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE — Add a new committee
    // ─────────────────────────────────────────────────────────────────────────

    public function create(): void
    {
        $db = \Database::getInstance()->getConnection();

        // Fetch all potential chairpersons (Committee members, SP members, Admins)
        $stmtUsers = $db->prepare(
            "SELECT id, name, role FROM users
             WHERE is_active = 1 AND role IN ('committee_member', 'sp_member', 'super_admin')
             ORDER BY name ASC"
        );
        $stmtUsers->execute();
        $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

        $errors = [];
        $input  = [];

        if ($this->isPost()) {
            $input = [
                'name'           => trim($this->post('name', '')),
                'jurisdiction'   => trim($this->post('jurisdiction', '')),
                'chairperson_id' => $this->post('chairperson_id', ''),
            ];

            if (empty($input['name'])) {
                $errors['name'] = 'Committee name is required.';
            }
            if (empty($input['jurisdiction'])) {
                $errors['jurisdiction'] = 'Jurisdiction details are required.';
            }

            if (empty($errors)) {
                $chairId = !empty($input['chairperson_id']) ? (int) $input['chairperson_id'] : null;

                $stmtInsert = $db->prepare(
                    "INSERT INTO committees (name, jurisdiction, chairperson_id, is_active)
                     VALUES (:name, :jurisdiction, :chairperson_id, 1)"
                );
                $stmtInsert->execute([
                    ':name'           => $input['name'],
                    ':jurisdiction'   => $input['jurisdiction'],
                    ':chairperson_id' => $chairId,
                ]);
                $newId = $db->lastInsertId();

                // Audit Log
                $userModel = $this->model('UserModel');
                $userModel->logAudit(
                    $this->userId(), 'CREATE_COMMITTEE', 'committees', (int) $newId,
                    null,
                    ['name' => $input['name'], 'chairperson_id' => $chairId]
                );

                $this->flash('success', 'Committee "' . $input['name'] . '" has been created.');
                $this->redirect('committee');
            }
        }

        $this->render('committees/create', [
            'pageTitle' => 'New Committee',
            'users'     => $users,
            'errors'    => $errors,
            'input'     => $input,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EDIT — Modify committee details
    // ─────────────────────────────────────────────────────────────────────────

    public function edit(string $id): void
    {
        $db = \Database::getInstance()->getConnection();

        // Fetch committee details
        $stmt = $db->prepare("SELECT * FROM committees WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => (int) $id]);
        $committee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$committee) {
            $this->flash('error', 'Committee not found.');
            $this->redirect('committee');
        }

        // Fetch potential chairpersons
        $stmtUsers = $db->prepare(
            "SELECT id, name, role FROM users
             WHERE is_active = 1 AND role IN ('committee_member', 'sp_member', 'super_admin')
             ORDER BY name ASC"
        );
        $stmtUsers->execute();
        $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

        $errors = [];
        $input  = $committee;

        if ($this->isPost()) {
            $input = [
                'name'           => trim($this->post('name', '')),
                'jurisdiction'   => trim($this->post('jurisdiction', '')),
                'chairperson_id' => $this->post('chairperson_id', ''),
            ];

            if (empty($input['name'])) {
                $errors['name'] = 'Committee name is required.';
            }
            if (empty($input['jurisdiction'])) {
                $errors['jurisdiction'] = 'Jurisdiction details are required.';
            }

            if (empty($errors)) {
                $chairId = !empty($input['chairperson_id']) ? (int) $input['chairperson_id'] : null;

                $stmtUpdate = $db->prepare(
                    "UPDATE committees
                     SET name = :name, jurisdiction = :jurisdiction, chairperson_id = :chairperson_id
                     WHERE id = :id"
                );
                $stmtUpdate->execute([
                    ':name'           => $input['name'],
                    ':jurisdiction'   => $input['jurisdiction'],
                    ':chairperson_id' => $chairId,
                    ':id'             => (int) $id,
                ]);

                // Audit Log
                $userModel = $this->model('UserModel');
                $userModel->logAudit(
                    $this->userId(), 'UPDATE_COMMITTEE', 'committees', (int) $id,
                    ['name' => $committee['name'], 'chairperson_id' => $committee['chairperson_id']],
                    ['name' => $input['name'], 'chairperson_id' => $chairId]
                );

                $this->flash('success', 'Committee "' . $input['name'] . '" has been updated.');
                $this->redirect('committee');
            }
        }

        $this->render('committees/edit', [
            'pageTitle' => 'Edit Committee',
            'committee' => $committee,
            'users'     => $users,
            'errors'    => $errors,
            'input'     => $input,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TOGGLE — Activate or deactivate committee
    // ─────────────────────────────────────────────────────────────────────────

    public function toggle(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('committee');
        }

        $db = \Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM committees WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => (int) $id]);
        $committee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$committee) {
            $this->flash('error', 'Committee not found.');
            $this->redirect('committee');
        }

        $newStatus = !$committee['is_active'];
        $stmtToggle = $db->prepare("UPDATE committees SET is_active = :status WHERE id = :id");
        $stmtToggle->execute([
            ':status' => $newStatus ? 1 : 0,
            ':id'     => (int) $id,
        ]);

        // Audit Log
        $userModel = $this->model('UserModel');
        $userModel->logAudit(
            $this->userId(),
            $newStatus ? 'ACTIVATE_COMMITTEE' : 'DEACTIVATE_COMMITTEE',
            'committees',
            (int) $id,
            ['is_active' => $committee['is_active']],
            ['is_active' => (int) $newStatus]
        );

        $label = $newStatus ? 'activated' : 'deactivated';
        $this->flash('success', 'Committee "' . $committee['name'] . '" has been ' . $label . '.');
        $this->redirect('committee');
    }
}

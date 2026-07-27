<?php

/**
 * ORLMS - User Management Controller
 *
 * Restricted to super_admin only.
 * Users are never deleted — only deactivated (is_active = 0).
 *
 * Routes:
 *   GET  /user_management            → index()
 *   GET  /user_management/create     → create()
 *   POST /user_management/create     → create()
 *   GET  /user_management/edit/{id}  → edit($id)
 *   POST /user_management/edit/{id}  → edit($id)
 *   POST /user_management/toggle/{id}→ toggle($id)  — activate/deactivate
 */

class UserManagementController extends Controller
{
    /** @var UserModel */
    private UserModel $userModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->requireRole([ROLE_SUPER_ADMIN]);
        $this->userModel = $this->model('UserModel');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — List all users
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $users = $this->userModel->getAllUsers();

        $this->render('user_management/index', [
            'pageTitle' => 'User Management',
            'users'     => $users,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE — Add a new system user
    // ─────────────────────────────────────────────────────────────────────────

    public function create(): void
    {
        $errors = [];
        $input  = [];

        if ($this->isPost()) {
            $input = [
                'name'     => trim($this->post('name', '')),
                'email'    => trim($this->post('email', '')),
                'role'     => trim($this->post('role', '')),
                'password' => $this->post('password', ''),
                'confirm'  => $this->post('password_confirm', ''),
            ];

            // Validate
            if (empty($input['name'])) {
                $errors['name'] = 'Full name is required.';
            }
            if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'A valid email address is required.';
            }
            if (!in_array($input['role'], [
                ROLE_SUPER_ADMIN, ROLE_LEGISLATIVE_STAFF,
                ROLE_COMMITTEE_MEMBER, ROLE_SP_MEMBER
            ])) {
                $errors['role'] = 'Please select a valid role.';
            }
            if (strlen($input['password']) < 8) {
                $errors['password'] = 'Password must be at least 8 characters.';
            }
            if ($input['password'] !== $input['confirm']) {
                $errors['confirm'] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                $newId = $this->userModel->createUser(
                    $input['name'],
                    $input['email'],
                    $input['password'],
                    $input['role']
                );

                if ($newId) {
                    $this->userModel->logAudit(
                        $this->userId(), 'CREATE_USER', 'users', (int) $newId,
                        null,
                        ['name' => $input['name'], 'email' => $input['email'], 'role' => $input['role']]
                    );

                    $this->flash('success', 'User "' . $input['name'] . '" has been created successfully.');
                    $this->redirect('user_management');
                } else {
                    $errors['email'] = 'This email address is already registered.';
                }
            }
        }

        $this->render('user_management/create', [
            'pageTitle' => 'New User',
            'errors'    => $errors,
            'input'     => $input,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EDIT — Edit user profile and optionally change password
    // ─────────────────────────────────────────────────────────────────────────

    public function edit(string $id): void
    {
        $user = $this->userModel->findById((int) $id);

        if (!$user) {
            $this->flash('error', 'User not found.');
            $this->redirect('user_management');
        }

        $errors = [];
        $input  = $user;

        if ($this->isPost()) {
            $input = [
                'name'  => trim($this->post('name', '')),
                'email' => trim($this->post('email', '')),
                'role'  => trim($this->post('role', '')),
            ];

            $newPassword = $this->post('new_password', '');
            $confirmPwd  = $this->post('new_password_confirm', '');

            // Validate
            if (empty($input['name'])) {
                $errors['name'] = 'Full name is required.';
            }
            if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'A valid email address is required.';
            }
            if (!in_array($input['role'], [
                ROLE_SUPER_ADMIN, ROLE_LEGISLATIVE_STAFF,
                ROLE_COMMITTEE_MEMBER, ROLE_SP_MEMBER
            ])) {
                $errors['role'] = 'Please select a valid role.';
            }
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 8) {
                    $errors['new_password'] = 'New password must be at least 8 characters.';
                } elseif ($newPassword !== $confirmPwd) {
                    $errors['new_password_confirm'] = 'Passwords do not match.';
                }
            }

            if (empty($errors)) {
                $this->userModel->updateUser(
                    (int) $id,
                    $input['name'],
                    $input['email'],
                    $input['role']
                );

                if (!empty($newPassword)) {
                    $this->userModel->updatePassword((int) $id, $newPassword);
                }

                $this->userModel->logAudit(
                    $this->userId(), 'UPDATE_USER', 'users', (int) $id,
                    ['name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']],
                    ['name' => $input['name'], 'email' => $input['email'], 'role' => $input['role']]
                );

                $this->flash('success', 'User "' . $input['name'] . '" has been updated successfully.');
                $this->redirect('user_management');
            }
        }

        $this->render('user_management/edit', [
            'pageTitle' => 'Edit User',
            'user'      => $user,
            'errors'    => $errors,
            'input'     => $input,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TOGGLE — Activate or deactivate a user
    // ─────────────────────────────────────────────────────────────────────────

    public function toggle(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('user_management');
        }

        // Prevent deactivating yourself
        if ((int) $id === $this->userId()) {
            $this->flash('error', 'You cannot deactivate your own account.');
            $this->redirect('user_management');
        }

        $user = $this->userModel->findById((int) $id);

        if (!$user) {
            $this->flash('error', 'User not found.');
            $this->redirect('user_management');
        }

        $newStatus = !$user['is_active'];
        $this->userModel->setActiveStatus((int) $id, $newStatus);

        $this->userModel->logAudit(
            $this->userId(),
            $newStatus ? 'ACTIVATE_USER' : 'DEACTIVATE_USER',
            'users',
            (int) $id,
            ['is_active' => $user['is_active']],
            ['is_active' => (int) $newStatus]
        );

        $label = $newStatus ? 'activated' : 'deactivated';
        $this->flash('success', '"' . $user['name'] . '" has been ' . $label . '.');
        $this->redirect('user_management');
    }
}

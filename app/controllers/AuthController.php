<?php

/**
 * ORLMS - Authentication Controller
 *
 * Handles user login, logout, and session management.
 *
 * Routes:
 *   GET  /auth/login  → show login form
 *   POST /auth/login  → process login credentials
 *   GET  /auth/logout → destroy session and redirect
 */

class AuthController extends Controller
{
    /** @var UserModel */
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = $this->model('UserModel');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOGIN — GET: show form | POST: process credentials
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Shows the login form on GET.
     * Processes credentials on POST.
     *
     * URL: /auth/login
     */
    public function login(): void
    {
        // If already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }

        $error = null;

        // ── Handle POST (form submission) ─────────────────────────────────────
        if ($this->isPost()) {

            $email    = trim($this->post('email', ''));
            $password = $this->post('password', '');

            // Basic input validation
            if (empty($email) || empty($password)) {
                $error = 'Email address and password are required.';

            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';

            } else {
                // Look up the user by email
                $user = $this->userModel->findByEmail($email);

                if (!$user) {
                    // User not found — use vague error to prevent user enumeration
                    $error = 'Invalid email address or password.';

                } elseif (!(bool) $user['is_active']) {
                    // Account is deactivated
                    $error = 'Your account has been deactivated. Please contact the administrator.';

                } elseif (!password_verify($password, $user['password'])) {
                    // Wrong password
                    $error = 'Invalid email address or password.';

                } else {
                    // ── Credentials are correct — create session ──────────────
                    $this->createSession($user);

                    // Log the login event in audit_logs
                    $this->userModel->logAudit(
                        $user['id'],
                        'LOGIN',
                        'users',
                        $user['id'],
                        null,
                        null
                    );

                    // Redirect to dashboard
                    $this->redirect('dashboard');
                }
            }
        }

        // ── Render the login page (GET or failed POST) ────────────────────────
        $this->render('auth/login', ['error' => $error], false);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOGOUT
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Destroys the session and redirects to login page.
     *
     * URL: /auth/logout
     */
    public function logout(): void
    {
        if ($this->isLoggedIn()) {
            // Log the logout event before destroying session
            $this->userModel->logAudit(
                $this->userId(),
                'LOGOUT',
                'users',
                $this->userId(),
                null,
                null
            );
        }

        // Destroy all session data
        $_SESSION = [];
        session_destroy();

        // Redirect to login
        $this->redirect('auth/login');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Creates a user session after successful login.
     * Only stores minimal, safe data in the session.
     *
     * @param  array $user  The user record from the database
     * @return void
     */
    private function createSession(array $user): void
    {
        // Regenerate session ID to prevent session fixation attacks
        session_regenerate_id(true);

        $_SESSION['user_id']    = (int) $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];
        $_SESSION['logged_in']  = true;
        $_SESSION['login_time'] = time();
    }
}

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
                    // ── Credentials correct — store temporary session for OTP ──
                    $_SESSION['otp_pending']    = true;
                    $_SESSION['otp_user_id']    = (int) $user['id'];
                    $_SESSION['otp_user_name']  = $user['name'];
                    $_SESSION['otp_user_email'] = $user['email'];
                    $_SESSION['otp_user_role']  = $user['role'];
                    $_SESSION['otp_code']       = (defined('MFA_SMTP_ENABLE') && MFA_SMTP_ENABLE) ? rand(100000, 999999) : 123456;
                    $_SESSION['otp_expires']    = time() + 300; // 5 min expiration

                    // Always log the OTP code to server console for easy access/debugging
                    error_log("[MFA OTP] Verification code for " . $user['email'] . ": " . $_SESSION['otp_code']);

                    // Send REAL email if SMTP is enabled
                    if (defined('MFA_SMTP_ENABLE') && MFA_SMTP_ENABLE) {
                        $this->sendOtpEmail($user['email'], $user['name'], $_SESSION['otp_code']);
                    }

                    // Redirect to OTP verification view
                    $this->redirect('auth/otp');
                }
            }
        }

        // ── Render the login page (GET or failed POST) ────────────────────────
        $this->render('auth/login', ['error' => $error], false);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OTP — GET: show OTP form | POST: process OTP code
    // ─────────────────────────────────────────────────────────────────────────
    public function otp(): void
    {
        // Redirect to login if no pending OTP
        if (!isset($_SESSION['otp_pending']) || !isset($_SESSION['otp_code'])) {
            $this->redirect('auth/login');
        }

        $error = null;

        if ($this->isPost()) {
            $enteredOtp = trim($this->post('otp_code', ''));

            if (empty($enteredOtp)) {
                $error = 'Mangyaring ilagay ang 6-digit verification code.';
            } elseif (time() > $_SESSION['otp_expires']) {
                $error = 'Expired na ang iyong verification code. I-resend ito.';
            } elseif ($enteredOtp !== (string)$_SESSION['otp_code']) {
                $error = 'Maling verification code. Pakisuri at subukan muli.';
            } else {
                // Correct OTP! Establish session
                $user = [
                    'id'    => $_SESSION['otp_user_id'],
                    'name'  => $_SESSION['otp_user_name'],
                    'email' => $_SESSION['otp_user_email'],
                    'role'  => $_SESSION['otp_user_role'],
                ];

                $this->createSession($user);

                // Clear temporary OTP session data
                unset($_SESSION['otp_pending']);
                unset($_SESSION['otp_user_id']);
                unset($_SESSION['otp_user_name']);
                unset($_SESSION['otp_user_email']);
                unset($_SESSION['otp_user_role']);
                unset($_SESSION['otp_code']);
                unset($_SESSION['otp_expires']);

                // Log the successful MFA login in audit_logs
                $this->userModel->logAudit(
                    $user['id'],
                    'LOGIN',
                    'users',
                    $user['id'],
                    null,
                    ['mfa_verified' => true]
                );

                // Redirect to dashboard
                $this->redirect('dashboard');
            }
        }

        $this->render('auth/otp', ['error' => $error], false);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RESEND OTP
    // ─────────────────────────────────────────────────────────────────────────
    public function resend_otp(): void
    {
        if (!isset($_SESSION['otp_pending']) || !isset($_SESSION['otp_user_id'])) {
            $this->redirect('auth/login');
        }

        $_SESSION['otp_code']    = rand(100000, 999999);
        $_SESSION['otp_expires'] = time() + 300;

        // Send REAL email if SMTP is enabled
        if (MFA_SMTP_ENABLE) {
            $this->sendOtpEmail($_SESSION['otp_user_email'], $_SESSION['otp_user_name'], $_SESSION['otp_code']);
        }

        $this->flash('success', 'Bagong verification code ay naipadalang muli.');
        $this->redirect('auth/otp');
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

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Destroy all session data cleanly
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        // Redirect to landing page (root)
        $this->redirect('');
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

    /**
     * Sends the OTP code to the user's email via SMTP using PHPMailer.
     *
     * @param  string $recipientEmail
     * @param  string $recipientName
     * @param  int    $otpCode
     * @return bool
     */
    private function sendOtpEmail(string $recipientEmail, string $recipientName, int $otpCode): bool
    {
        // For development/testing: Redirect all OTP emails to the developer's personal email
        $recipientEmail = 'orlms2026@gmail.com';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->Timeout    = 8; // 8 second timeout

            // Recipients
            $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
            $mail->addAddress($recipientEmail, $recipientName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'MFA Verification Code - CSJDM ORLMS';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 500px; margin: auto; padding: 25px; border: 1px solid #dee2e6; border-top: 5px solid #0C2340; border-radius: 8px; background-color: #ffffff;'>
                    <div style='text-align: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px;'>
                        <h2 style='color: #0C2340; margin: 0; font-size: 20px;'>CSJDM Sangguniang Panlungsod</h2>
                        <p style='color: #F2A900; font-size: 11px; font-weight: bold; text-transform: uppercase; margin: 5px 0 0 0; letter-spacing: 0.8px;'>Multi-Factor Authentication</p>
                    </div>
                    <p style='font-size: 14px; color: #333333;'>Magandang araw, <strong>{$recipientName}</strong>,</p>
                    <p style='font-size: 13.5px; color: #4a5568; line-height: 1.6;'>Nakatanggap kami ng kahilingan na mag-login sa iyong account. Gamitin ang sumusunod na verification code para makumpleto ang proseso:</p>
                    <div style='text-align: center; margin: 30px 0; padding: 18px; background-color: #f8f9fa; border: 1px dashed #0C2340; border-radius: 6px;'>
                        <span style='font-size: 28px; font-weight: 800; letter-spacing: 6px; color: #0C2340;'>{$otpCode}</span>
                    </div>
                    <p style='font-size: 12px; color: #718096; font-style: italic; line-height: 1.5;'>Ang code na ito ay may bisa lamang sa loob ng 5 minuto. Mangyaring huwag ibahagi ang code na ito sa kahit kanino.</p>
                    <hr style='border: none; border-top: 1px solid #dee2e6; margin: 25px 0;'>
                    <p style='font-size: 10.5px; color: #a0aec0; text-align: center;'>Ito ay isang awtomatikong email mula sa ORLMS Portal ng Lungsod ng San Jose del Monte, Bulacan.</p>
                </div>
            ";
            $mail->AltBody = "Magandang araw {$recipientName},\n\nAng iyong MFA Verification code ay: {$otpCode}\n\nIto ay may bisa sa loob ng 5 minuto.";

            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log('MFA SMTP Error sending to ' . $recipientEmail . ': ' . $mail->ErrorInfo);
            return false;
        }
    }
}

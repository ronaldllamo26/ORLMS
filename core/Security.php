<?php

/**
 * ORLMS - Security & Cryptographic Engine
 *
 * Implements AES-256-CBC authenticated encryption (with HMAC-SHA256)
 * to comply with Data Privacy Act of 2012 (RA 10173) and National Privacy Commission
 * Circular 16-01 (Security of Personal Data in Government Agencies).
 */
class Security
{
    private const CIPHER = 'aes-256-cbc';

    /**
     * Retrieves the master cryptographic key.
     */
    private static function getKey(): string
    {
        $rawKey = defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : (getenv('APP_KEY') ?: 'orlms_secret_aes_key_2026_csjdm_sanggunian!');
        return hash('sha256', $rawKey, true); // Ensure 32-byte key for AES-256
    }

    /**
     * Encrypts plaintext using AES-256-CBC with HMAC-SHA256 authentication.
     *
     * @param string $plaintext
     * @return string Base64 encoded payload: [IV (16b) | HMAC (32b) | Ciphertext]
     */
    public static function encrypt(string $plaintext): string
    {
        if ($plaintext === '') {
            return '';
        }

        $key = self::getKey();
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $ciphertext = openssl_encrypt($plaintext, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
        if ($ciphertext === false) {
            throw new RuntimeException('Encryption failure.');
        }

        // Generate HMAC for authentication (Encrypt-then-MAC prevents padding oracle attacks)
        $hmac = hash_hmac('sha256', $iv . $ciphertext, $key, true);

        return base64_encode($iv . $hmac . $ciphertext);
    }

    /**
     * Decrypts an AES-256-CBC ciphertext after verifying HMAC integrity.
     *
     * @param string $encodedPayload
     * @return string|null Plaintext, or null if tampered/invalid
     */
    public static function decrypt(string $encodedPayload): ?string
    {
        if (empty($encodedPayload)) {
            return null;
        }

        $decoded = base64_decode($encodedPayload, true);
        if ($decoded === false) {
            return null;
        }

        $key = self::getKey();
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $hmacLength = 32; // SHA-256 output length in raw bytes

        if (strlen($decoded) < ($ivLength + $hmacLength)) {
            return null;
        }

        $iv = substr($decoded, 0, $ivLength);
        $hmac = substr($decoded, $ivLength, $hmacLength);
        $ciphertext = substr($decoded, $ivLength + $hmacLength);

        // Verify HMAC before attempting decryption
        $calculatedHmac = hash_hmac('sha256', $iv . $ciphertext, $key, true);
        if (!hash_equals($hmac, $calculatedHmac)) {
            return null; // Tampered data or invalid key
        }

        $plaintext = openssl_decrypt($ciphertext, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
        return ($plaintext !== false) ? $plaintext : null;
    }

    /**
     * Masks an email address for public/audit display (e.g., ad***@orlms.ph).
     */
    public static function maskEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return '***';
        }
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        $len = strlen($name);
        if ($len <= 2) {
            $maskedName = substr($name, 0, 1) . '*';
        } else {
            $maskedName = substr($name, 0, 2) . str_repeat('*', max(1, $len - 3)) . substr($name, -1);
        }

        return $maskedName . '@' . $domain;
    }

    /**
     * Masks a Philippine mobile number (e.g., 0912***7890).
     */
    public static function maskPhone(string $phone): string
    {
        $clean = preg_replace('/[^\d+]/', '', $phone);
        $len = strlen($clean);
        if ($len < 7) {
            return '***';
        }
        return substr($clean, 0, 4) . str_repeat('*', max(1, $len - 8)) . substr($clean, -4);
    }
}

<?php
class CSRF {
    private const TOKEN_KEY = '_csrf_token';

    public static function token(): string {
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::TOKEN_KEY];
    }

    public static function field(): string {
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(self::token(), ENT_QUOTES) . '">';
    }

    public static function verify(string $token): bool {
        return isset($_SESSION[self::TOKEN_KEY]) &&
               hash_equals($_SESSION[self::TOKEN_KEY], $token);
    }

    public static function verifyRequest(bool $isJson = false): void {
        $token = $isJson
            ? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')
            : ($_POST['_csrf'] ?? '');

        if (!self::verify($token)) {
            if ($isJson) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
                exit;
            }
            http_response_code(403);
            die('Invalid CSRF token. Please go back and try again.');
        }
    }
}

<?php
require_once ROOT_PATH . '/config/database.php';

class Auth {
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
            session_name(SESSION_NAME);
            session_start();
        }
    }

    public static function register(string $name, string $email, string $password, string $phone = ''): array {
        $name  = trim($name);
        $email = strtolower(trim($email));

        if (strlen($name) < 2)         return ['success' => false, 'error' => 'Name must be at least 2 characters.'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return ['success' => false, 'error' => 'Invalid email address.'];
        if (strlen($password) < 8)     return ['success' => false, 'error' => 'Password must be at least 8 characters.'];

        $existing = DB::row('SELECT id FROM users WHERE email = ?', [$email]);
        if ($existing) return ['success' => false, 'error' => 'Email is already registered.'];

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        DB::exec('INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)',
            [$name, $email, $hash, trim($phone)]);
        $id = (int) DB::lastId();

        self::loginById($id);
        return ['success' => true];
    }

    public static function login(string $email, string $password): array {
        $email = strtolower(trim($email));

        // Rate limit: max 10 failed attempts per IP per 15 minutes
        $ip       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key      = 'login_fails_' . md5($ip);
        $fails    = (int)($_SESSION[$key] ?? 0);
        $lastFail = (int)($_SESSION[$key . '_ts'] ?? 0);
        if ($lastFail && time() - $lastFail > 900) { $fails = 0; } // reset after 15 min
        if ($fails >= 10) {
            return ['success' => false, 'error' => 'Too many failed attempts. Please try again in 15 minutes.'];
        }

        $user = DB::row('SELECT * FROM users WHERE email = ? AND is_active = 1', [$email]);
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION[$key]        = $fails + 1;
            $_SESSION[$key . '_ts'] = time();
            $remaining = 10 - ($fails + 1);
            return ['success' => false, 'error' => 'Invalid email or password.' . ($remaining <= 3 ? " ($remaining attempts left)" : '')];
        }

        // Clear fail counter on success
        unset($_SESSION[$key], $_SESSION[$key . '_ts']);
        self::loginById((int)$user['id'], $user);
        return ['success' => true];
    }

    private static function loginById(int $id, ?array $user = null): void {
        if (!$user) $user = DB::row('SELECT * FROM users WHERE id = ?', [$id]);
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email']= $user['email'];
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool {
        return !empty($_SESSION['user_id']);
    }

    public static function requireLogin(string $redirect = 'index.php'): void {
        if (!self::check()) {
            header('Location: ' . APP_URL . '/' . $redirect);
            exit;
        }
    }

    public static function user(): ?array {
        if (!self::check()) return null;
        return [
            'id'    => $_SESSION['user_id'],
            'name'  => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
        ];
    }

    public static function userId(): int {
        return (int)($_SESSION['user_id'] ?? 0);
    }
}

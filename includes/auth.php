<?php

declare(strict_types=1);

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']['id']);
}

function is_role(string $role): bool
{
    return is_logged_in() && ($_SESSION['user']['role'] ?? null) === $role;
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Silakan login terlebih dahulu.');
        redirect(APP_URL . '/src/auth/login.php');
    }
}

function require_role(string|array $roles): void
{
    require_login();

    $roles = (array) $roles;
    if (!in_array($_SESSION['user']['role'], $roles, true)) {
        http_response_code(403);
        exit('Akses ditolak. Anda tidak memiliki hak akses ke halaman ini.');
    }
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function dashboard_url_by_role(string $role): string
{
    return match ($role) {
        ROLE_ADMIN => APP_URL . '/src/admin/dashboard.php',
        ROLE_PANITIA => APP_URL . '/src/panitia/dashboard.php',
        ROLE_JURI => APP_URL . '/src/juri/dashboard.php',
        default => APP_URL . '/src/peserta/dashboard.php',
    };
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'nama' => $user['nama'],
        'nim' => $user['nim'],
        'email' => $user['email'],
        'fakultas' => $user['fakultas'],
        'role' => $user['role'],
    ];
    $_SESSION['last_activity'] = time();
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function log_activity(?int $userId, ?string $role, string $action, ?string $description = null): void
{
    try {
        db_query(
            'INSERT INTO activity_logs (user_id, role, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)',
            [
                $userId,
                $role,
                $action,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ]
        );
    } catch (Throwable $e) {
        error_log('[ACTIVITY_LOG_ERROR] ' . $e->getMessage());
    }
}

function find_user_by_identifier(string $identifier): ?array
{
    return db_fetch(
        'SELECT * FROM users WHERE (email = ? OR nim = ?) AND is_active = 1 LIMIT 1',
        [$identifier, $identifier]
    );
}

<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

// Harden session cookies and start the session.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => (($_SERVER['HTTPS'] ?? '') === 'on'),
    ]);
    session_start();
}

/** The logged-in user row, or null. Cached per request. */
function current_user(): ?array
{
    static $cached = false;
    static $user = null;
    if ($cached) {
        return $user;
    }
    $cached = true;
    $id = $_SESSION['user_id'] ?? null;
    if ($id) {
        $user = one('SELECT * FROM users WHERE id = ?', [$id]);
    }
    return $user;
}

/** The current user's technician profile, or null. */
function current_technician(): ?array
{
    $u = current_user();
    if (!$u) {
        return null;
    }
    return one('SELECT * FROM technician_profiles WHERE user_id = ?', [$u['id']]);
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function user_role(): string
{
    return current_user()['role'] ?? 'guest';
}

/** Redirect guests to login, preserving where they were headed. */
function require_login(): void
{
    if (!is_logged_in()) {
        $here = $_SERVER['REQUEST_URI'] ?? '';
        redirect('/login.php?redirect=' . urlencode($here));
    }
}

/** Require a specific role, else 403. */
function require_role(string $role): void
{
    require_login();
    if (user_role() !== $role) {
        http_response_code(403);
        require __DIR__ . '/header.php';
        echo '<main class="container narrow"><div class="card pad center">'
            . '<h1>Not allowed</h1><p class="muted">You don\'t have permission to view this page.</p>'
            . '<a class="btn" href="' . e(url('/account.php')) . '">Back to account</a></div></main>';
        require __DIR__ . '/footer.php';
        exit;
    }
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
}

function logout_user(): void
{
    $_SESSION = [];
    session_destroy();
}

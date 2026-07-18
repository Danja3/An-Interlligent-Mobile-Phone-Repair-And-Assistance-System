<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

/** Escape for HTML output. */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** Build an app URL from a root-relative path. */
function url(string $path = ''): string
{
    if ($path === '' || $path[0] !== '/') {
        $path = '/' . $path;
    }
    return BASE_URL . $path;
}

/** Redirect to an app path and stop. */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

/** Format money. NGN renders with the Naira sign. */
function money(int $amount, string $currency = 'NGN'): string
{
    $symbol = $currency === 'NGN' ? '₦' : $currency . ' ';
    return $symbol . number_format($amount);
}

/** Flash messages (one-shot, stored in session). */
function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}
function take_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/** CSRF helpers. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}
function verify_csrf(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(400);
        exit('Invalid request token. Go back, refresh, and try again.');
    }
}

/**
 * Normalize a Nigerian phone number to WhatsApp digits (234XXXXXXXXXX),
 * or null if it isn't a valid NG mobile number.
 */
function normalize_phone(string $input): ?string
{
    $digits = preg_replace('/\D/', '', $input);
    $national = null;
    if (strlen($digits) === 13 && str_starts_with($digits, '234')) {
        $national = substr($digits, 3);
    } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
        $national = substr($digits, 1);
    } elseif (strlen($digits) === 10) {
        $national = $digits;
    }
    if ($national === null || !preg_match('/^[789]\d{9}$/', $national)) {
        return null;
    }
    return '234' . $national;
}

/** Skills shared between the apply form and directory filters. */
const SKILLS = [
    'Screen Replacement', 'Battery Replacement', 'Charging Port Repair',
    'Speaker Replacement', 'Microphone Replacement', 'Camera Replacement',
    'Motherboard Repair', 'Software Repair', 'Water Damage Repair', 'Data Recovery',
];

/** Northern Nigeria cities used in dropdowns. */
const CITIES = [
    'Kano', 'Kaduna', 'Katsina', 'Sokoto', 'Maiduguri', 'Bauchi', 'Gombe',
    'Zaria', 'Jos', 'Yola', 'Dutse', 'Birnin Kebbi', 'Damaturu', 'Gusau', 'Minna',
];

/**
 * Validate + resize an uploaded image with GD (max 800px, JPEG ~82%).
 * Returns ['url' => string|null, 'error' => string|null]. No file = no error.
 */
function save_photo(array $file): array
{
    $err = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($err === UPLOAD_ERR_NO_FILE) return ['url' => null, 'error' => null];
    if ($err !== UPLOAD_ERR_OK)      return ['url' => null, 'error' => 'Upload failed, please try again.'];
    if (($file['size'] ?? 0) > 8 * 1024 * 1024) return ['url' => null, 'error' => 'Image too large (max 8MB).'];

    $info = @getimagesize($file['tmp_name']);
    if (!$info) return ['url' => null, 'error' => 'That file is not a valid image.'];
    [$w, $h] = $info;
    switch ($info[2]) {
        case IMAGETYPE_JPEG: $src = @imagecreatefromjpeg($file['tmp_name']); break;
        case IMAGETYPE_PNG:  $src = @imagecreatefrompng($file['tmp_name']); break;
        case IMAGETYPE_WEBP: $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file['tmp_name']) : false; break;
        default: return ['url' => null, 'error' => 'Use a JPG, PNG, or WEBP image.'];
    }
    if (!$src) return ['url' => null, 'error' => 'Could not read that image.'];

    $scale = min(1, 800 / max($w, $h));
    $nw = max(1, (int) round($w * $scale));
    $nh = max(1, (int) round($h * $scale));
    $dst = imagecreatetruecolor($nw, $nh);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

    if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0775, true);
    $name = 'tech-' . bin2hex(random_bytes(8)) . '.jpg';
    imagejpeg($dst, UPLOAD_DIR . '/' . $name, 82);
    imagedestroy($src);
    imagedestroy($dst);
    return ['url' => UPLOAD_URL . '/' . $name, 'error' => null];
}

/** Inline SVG pictograms for problem categories (icon-first UX). */
function category_icon(?string $slug): string
{
    $paths = [
        'screen'   => '<rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12" y2="18"/>',
        'battery'  => '<rect x="2" y="7" width="16" height="10" rx="2"/><line x1="22" y1="11" x2="22" y2="13"/>',
        'charging' => '<path d="M11 2 6 13h4l-1 9 7-12h-4l1-8z"/>',
        'power'    => '<path d="M12 2v10"/><path d="M5 7a9 9 0 1 0 14 0"/>',
        'network'  => '<path d="M2 20h.01"/><path d="M7 20v-4"/><path d="M12 20v-8"/><path d="M17 20V8"/><path d="M22 4v16"/>',
        'audio'    => '<path d="M11 5 6 9H2v6h4l5 4z"/><path d="M15 9a4 4 0 0 1 0 6"/>',
        'camera'   => '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>',
        'software' => '<rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/>',
        'water-damage' => '<path d="M12 2.7s6 6 6 10a6 6 0 1 1-12 0c0-4 6-10 6-10z"/>',
    ];
    $inner = $paths[$slug] ?? '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>';
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $inner . '</svg>';
}

<?php

declare(strict_types=1);

require_once __DIR__ . '/_response.php';

api_require_method('POST');
$input = api_input();
$email = strtolower(trim((string)($input['email'] ?? '')));

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
    api_error('Format email tidak valid.', 400, ['available' => false]);
}

$user = db_fetch('SELECT id, nama, nim, role FROM users WHERE email = ? LIMIT 1', [$email]);

api_success($user ? 'Email sudah terdaftar.' : 'Email tersedia.', [
    'email' => $email,
    'exists' => (bool)$user,
    'available' => !$user,
    'user' => $user ? [
        'id' => (int)$user['id'],
        'nama' => $user['nama'],
        'nim' => $user['nim'],
        'role' => $user['role'],
    ] : null,
]);

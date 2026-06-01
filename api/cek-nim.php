<?php

declare(strict_types=1);

require_once __DIR__ . '/_response.php';

api_require_method('POST');
$input = api_input();
$nim = trim((string)($input['nim'] ?? ''));

if (!api_digits($nim, 3, 30)) {
    api_error('Format NIM tidak valid. NIM hanya boleh angka 3-30 digit.', 400, ['available' => false]);
}

$user = db_fetch('SELECT id, nama, email, role FROM users WHERE nim = ? LIMIT 1', [$nim]);

api_success($user ? 'NIM sudah terdaftar.' : 'NIM tersedia.', [
    'nim' => $nim,
    'exists' => (bool)$user,
    'available' => !$user,
    'user' => $user ? [
        'id' => (int)$user['id'],
        'nama' => $user['nama'],
        'email' => $user['email'],
        'role' => $user['role'],
    ] : null,
]);

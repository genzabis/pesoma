<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

/**
 * Kumpulan helper umum yang dapat dipakai lintas halaman PESOMA 2026.
 */

/**
 * Format tanggal/datetime ke format Indonesia ringkas.
 */
function format_tanggal(?string $datetime, bool $withTime = true): string
{
    if (!$datetime) {
        return '-';
    }
    $ts = strtotime($datetime);
    if ($ts === false) {
        return '-';
    }
    return date($withTime ? 'd M Y H:i' : 'd M Y', $ts);
}

/**
 * Decode JSON menjadi array secara aman (mengembalikan [] bila gagal).
 *
 * @return array<mixed>
 */
function json_to_array(?string $json): array
{
    $data = json_decode((string) $json, true);
    return is_array($data) ? $data : [];
}

/**
 * Potong teks panjang dengan elipsis tanpa memutus multibyte.
 */
function ringkas(string $text, int $length = 160): string
{
    return mb_strimwidth($text, 0, $length, '…');
}

/**
 * Ambil nilai integer aman dari sebuah array sumber (mis. $_GET/$_POST).
 *
 * @param array<string,mixed> $source
 */
function input_int(array $source, string $key, int $default = 0): int
{
    return isset($source[$key]) && is_numeric($source[$key]) ? (int) $source[$key] : $default;
}

/**
 * Pastikan nilai termasuk dalam daftar yang diizinkan, jika tidak kembalikan default.
 *
 * @param array<int,string> $allowed
 */
function nilai_atau_default(mixed $value, array $allowed, string $default = ''): string
{
    return in_array($value, $allowed, true) ? (string) $value : $default;
}

/**
 * Label peran yang ramah dibaca.
 */
function label_role(string $role): string
{
    return match ($role) {
        ROLE_ADMIN => 'Administrator',
        ROLE_PANITIA => 'Panitia',
        ROLE_JURI => 'Juri',
        ROLE_PESERTA => 'Peserta',
        default => ucfirst($role),
    };
}

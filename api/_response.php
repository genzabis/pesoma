<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function api_response(string $status, int $code, string $message, array $data = []): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode([
        'status' => $status,
        'code' => $code,
        'message' => $message,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function api_success(string $message = 'OK', array $data = [], int $code = 200): never
{
    api_response('success', $code, $message, $data);
}

function api_error(string $message, int $code = 400, array $data = []): never
{
    api_response('error', $code, $message, $data);
}

function api_require_method(string $method): void
{
    if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== strtoupper($method)) {
        api_error('Method tidak diizinkan. Gunakan ' . strtoupper($method) . '.', 405);
    }
}

function api_input(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (str_contains(strtolower($contentType), 'application/json')) {
        $raw = file_get_contents('php://input') ?: '';
        $json = json_decode($raw, true);
        return is_array($json) ? $json : [];
    }
    return $_POST;
}

function api_int_param(array $source, string $key, int $default = 0): int
{
    $value = $source[$key] ?? $default;
    return filter_var($value, FILTER_VALIDATE_INT) !== false ? (int)$value : $default;
}

function api_limit(array $source, int $default = 10, int $max = 100): int
{
    $limit = api_int_param($source, 'limit', $default);
    if ($limit < 1) return $default;
    return min($limit, $max);
}

function api_valid_date(?string $date): bool
{
    if (!$date) return false;
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    return $dt instanceof DateTime && $dt->format('Y-m-d') === $date;
}

function api_digits(string $value, int $min = 3, int $max = 30): bool
{
    return (bool) preg_match('/^[0-9]{' . $min . ',' . $max . '}$/', $value);
}

<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Membuat koneksi PDO singleton ke database PESOMA 2026.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        $pdo->exec("SET time_zone = '+07:00'");
        return $pdo;
    } catch (PDOException $e) {
        error_log('[DB_CONNECTION_ERROR] ' . $e->getMessage());
        http_response_code(500);
        exit('Koneksi database gagal. Silakan hubungi administrator.');
    }
}

/**
 * Helper prepared statement untuk SELECT/INSERT/UPDATE/DELETE.
 *
 * @param string $sql Query SQL dengan placeholder.
 * @param array<int|string,mixed> $params Parameter binding.
 */
function db_query(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_fetch(string $sql, array $params = []): ?array
{
    $row = db_query($sql, $params)->fetch();
    return $row === false ? null : $row;
}

function db_fetch_all(string $sql, array $params = []): array
{
    return db_query($sql, $params)->fetchAll();
}

function db_last_insert_id(): string
{
    return db()->lastInsertId();
}

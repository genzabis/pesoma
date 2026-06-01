<?php

declare(strict_types=1);

const PESOMA_CRON_LOG_FILE = '/var/log/pesoma/cron.log';
const PESOMA_ADMIN_EMAIL = 'admin@pesoma.local';

function cron_log_file(): string
{
    $configured = getenv('CRON_LOG_FILE');
    if (is_string($configured) && trim($configured) !== '') {
        return $configured;
    }

    if (DIRECTORY_SEPARATOR === '\\') {
        return __DIR__ . '/../storage/logs/cron.log';
    }

    $primaryDir = dirname(PESOMA_CRON_LOG_FILE);
    if ((is_dir($primaryDir) || @mkdir($primaryDir, 0775, true)) && is_writable($primaryDir)) {
        return PESOMA_CRON_LOG_FILE;
    }

    return __DIR__ . '/../storage/logs/cron.log';
}

function cron_log(string $script, string $message, string $level = 'INFO'): void
{
    $line = sprintf("[%s] [%s] [%s] %s%s", date('Y-m-d H:i:s'), strtoupper($level), $script, $message, PHP_EOL);
    $logFile = cron_log_file();
    $dir = dirname($logFile);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    if (@file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX) === false) {
        error_log($line);
    }
}

function cron_admin_email(): string
{
    $email = getenv('CRON_ADMIN_EMAIL') ?: PESOMA_ADMIN_EMAIL;
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : PESOMA_ADMIN_EMAIL;
}

function cron_mail(string $to, string $subject, string $message): bool
{
    $fromEmail = getenv('CRON_FROM_EMAIL') ?: 'no-reply@pesoma.local';
    $fromName = getenv('CRON_FROM_NAME') ?: 'PESOMA Cron';
    $from = sprintf('%s <%s>', $fromName, filter_var($fromEmail, FILTER_VALIDATE_EMAIL) ? $fromEmail : 'no-reply@pesoma.local');

    if ((getenv('CRON_MAIL_DISABLED') ?: '') === '1') {
        cron_log('mail', 'Mail disabled. Subject: ' . $subject . ' To: ' . $to, 'WARNING');
        return false;
    }

    $headers = [
        'From: ' . $from,
        'Content-Type: text/plain; charset=UTF-8',
    ];

    return @mail($to, $subject, $message, implode("\r\n", $headers));
}

function cron_error(string $script, Throwable $e): void
{
    $message = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    cron_log($script, $message, 'ERROR');
    cron_mail(cron_admin_email(), '[PESOMA Cron Error] ' . $script, $message . PHP_EOL . PHP_EOL . $e->getTraceAsString());
}

function cron_run(string $script, callable $callback): void
{
    try {
        cron_log($script, 'Started.');
        $result = $callback();
        cron_log($script, 'Finished. ' . (is_string($result) ? $result : 'OK'));
    } catch (Throwable $e) {
        cron_error($script, $e);
        exit(1);
    }
}

function cron_insert_activity(string $action, string $description): void
{
    db_query(
        'INSERT INTO activity_logs (user_id, role, action, description, ip_address, user_agent) VALUES (NULL, "system", ?, ?, "127.0.0.1", "cron")',
        [$action, $description]
    );
}

function cron_ensure_dir(string $dir): void
{
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Gagal membuat folder: ' . $dir);
    }
}

function cron_should_run_once(string $key, DateTimeImmutable $runAt): bool
{
    $markerDir = __DIR__ . '/../storage/cron-markers';
    cron_ensure_dir($markerDir);
    $marker = $markerDir . '/' . preg_replace('/[^a-z0-9_\-]/i', '_', $key) . '.done';
    if (is_file($marker)) {
        return false;
    }
    if (new DateTimeImmutable('now', $runAt->getTimezone()) < $runAt) {
        return false;
    }
    file_put_contents($marker, date('c'));
    return true;
}

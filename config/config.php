<?php

declare(strict_types=1);


/**
 * PESOMA III 2026 - UIN Prof. K.H. Saifuddin Zuhri Purwokerto
 * Copyright (c) 2026 Tim Pengembang PESOMA III. All Rights Reserved.
 *
 * This file is part of a proprietary software project. Unauthorized
 * copying, redistribution, or use of this file, via any medium, is
 * strictly prohibited. See LICENSE for the full terms.
 */

/**
 * Konfigurasi utama Portal PESOMA 2026.
 * Sesuaikan nilai konstanta DB_* pada environment production.
 */

define('APP_NAME', 'Portal PESOMA 2026');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost/pesoma');
define('APP_TIMEZONE', 'Asia/Jakarta');

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'pesoma_2026');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

define('UPLOAD_MAX_SIZE', 100 * 1024 * 1024); // 100MB
define('UPLOAD_DOC_MAX_SIZE', 50 * 1024 * 1024); // 50MB
define('UPLOAD_VIDEO_MAX_SIZE', 100 * 1024 * 1024); // 100MB
define('SESSION_TIMEOUT', 1800); // 30 menit
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCK_MINUTES', 15);
define('LOG_DIR', __DIR__ . '/../storage/logs');
define('ERROR_LOG_FILE', LOG_DIR . '/php-error.log');

date_default_timezone_set(APP_TIMEZONE);

if (!is_dir(LOG_DIR)) {
    mkdir(LOG_DIR, 0775, true);
}

ini_set('log_errors', '1');
ini_set('error_log', ERROR_LOG_FILE);

if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    error_reporting(0);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

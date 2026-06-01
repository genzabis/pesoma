<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

if (is_logged_in()) {
    log_activity((int) $_SESSION['user']['id'], $_SESSION['user']['role'], 'logout', 'User logout dari sistem.');
}

logout_user();
session_start();
flash('success', 'Anda berhasil logout.');
redirect(APP_URL . '/src/auth/login.php');

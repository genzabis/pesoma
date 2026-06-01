# Tambah copyright header ke file PHP utama yang belum punya.
# Idempotent: kalau header sudah ada, file di-skip.
# Strategi: parse line demi line. Skip <?php opening + declare()
# (kalau ada) + blank line. Sisipkan header sebelum konten lainnya.

$header = @(
    '/**',
    ' * PESOMA III 2026 - UIN Prof. K.H. Saifuddin Zuhri Purwokerto',
    ' * Copyright (c) 2026 Tim Pengembang PESOMA III. All Rights Reserved.',
    ' *',
    ' * This file is part of a proprietary software project. Unauthorized',
    ' * copying, redistribution, or use of this file, via any medium, is',
    ' * strictly prohibited. See LICENSE for the full terms.',
    ' */'
)

$root = 'd:\xampp\htdocs\pesoma'
$targets = @(
    'index.php',
    'config\config.php',
    'config\constants.php',
    'config\database.php',
    'includes\auth.php',
    'includes\session.php',
    'includes\functions.php',
    'includes\header.php',
    'includes\footer.php',
    'includes\auth-layout.php',
    'includes\upload-handler.php',
    'src\admin\_layout.php',
    'src\panitia\_layout.php',
    'src\juri\_layout.php',
    'src\peserta\_layout.php',
    'src\auth\login.php',
    'src\auth\register.php',
    'src\auth\forgot-password.php',
    'design\preview.php'
)

$added = 0
$skipped = 0

foreach ($rel in $targets) {
    $path = Join-Path $root $rel
    if (-not (Test-Path $path)) {
        Write-Host ("MISS  " + $rel)
        continue
    }

    $lines = Get-Content $path
    $joined = $lines -join "`n"
    if ($joined -match 'Copyright \(c\) 2026 Tim Pengembang PESOMA') {
        $skipped++
        continue
    }

    if ($lines.Count -eq 0 -or $lines[0].Trim() -ne '<?php') {
        Write-Host ("SKIP  " + $rel + " (line 1 is not '<?php')")
        continue
    }

    # Cari index baris setelah <?php + optional declare() + optional blank
    $insertAt = 1  # default: setelah <?php
    for ($i = 1; $i -lt [Math]::Min($lines.Count, 5); $i++) {
        $t = $lines[$i].Trim()
        if ($t -eq '' -or $t.StartsWith('declare(')) {
            $insertAt = $i + 1
        }
        else {
            break
        }
    }

    $newLines = @()
    $newLines += $lines[0..($insertAt - 1)]
    $newLines += ''
    $newLines += $header
    $newLines += ''
    if ($insertAt -lt $lines.Count) {
        $newLines += $lines[$insertAt..($lines.Count - 1)]
    }

    # Tulis dengan UTF-8 TANPA BOM (PowerShell 5 Set-Content -Encoding UTF8 menulis BOM,
    # dan BOM menyebabkan "strict_types must be very first statement").
    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllLines($path, $newLines, $utf8NoBom)
    $added++
    Write-Host ("ADD   " + $rel)
}

Write-Host ""
Write-Host ("Added headers: " + $added + ", already had headers: " + $skipped)

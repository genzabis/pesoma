# Full verification script — cek semua jalur yang sudah dirombak.

$ErrorActionPreference = 'Continue'
$base = 'http://localhost/pesoma'
$total = 0
$fail = 0

function Test-Page {
    param([string]$Label, [string]$Path, [Microsoft.PowerShell.Commands.WebRequestSession]$Session = $null)
    $script:total++
    $url = $script:base + $Path
    try {
        if ($Session) {
            $r = Invoke-WebRequest -Uri $url -UseBasicParsing -WebSession $Session -MaximumRedirection 5 -TimeoutSec 15
        }
        else {
            $r = Invoke-WebRequest -Uri $url -UseBasicParsing -MaximumRedirection 5 -TimeoutSec 15
        }
        $hasFatal = ($r.Content -match 'Fatal error|Parse error|Uncaught|Warning:|Notice:|Deprecated:|Undefined (variable|index|array key|offset)')
        if ($hasFatal) {
            $script:fail++
            $m = [regex]::Match($r.Content, '(Fatal error|Parse error|Uncaught|Warning:|Notice:|Deprecated:|Undefined (?:variable|index|array key|offset))[^<]{0,160}')
            Write-Host ("FAIL  " + $Label.PadRight(32) + " => " + $m.Value)
        }
        else {
            Write-Host ("OK    " + $Label.PadRight(32) + " size=" + $r.Content.Length)
        }
    }
    catch {
        $script:fail++
        Write-Host ("ERR   " + $Label.PadRight(32) + " => " + $_.Exception.Message.Substring(0, [Math]::Min(80, $_.Exception.Message.Length)))
    }
}

Write-Host '===== 1. PHP lint ====='
$files = Get-ChildItem 'd:\xampp\htdocs\pesoma' -Recurse -Filter *.php
$lintFail = 0
foreach ($f in $files) {
    $out = & 'd:\xampp\php\php.exe' -l $f.FullName 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host ("LINT FAIL " + $f.FullName)
        Write-Host $out
        $lintFail++
    }
}
Write-Host ("PHP lint: " + $files.Count + " files, " + $lintFail + " errors")
Write-Host ''

Write-Host '===== 2. Public pages ====='
Test-Page 'Beranda'           '/pages/beranda.php'
Test-Page 'Cabang Lomba'      '/pages/cabang-lomba.php'
Test-Page 'Jadwal'            '/pages/jadwal.php'
Test-Page 'Pengumuman'        '/pages/pengumuman.php'
Test-Page 'Tentang'           '/pages/tentang.php'
Test-Page 'Kontak'            '/pages/kontak.php'
Test-Page 'Unduh Juknis'      '/pages/unduh-juknis.php'
Test-Page 'Detail Lomba'      '/pages/detail-lomba.php?id=15'
Write-Host ''

Write-Host '===== 3. Auth ====='
Test-Page 'Login'             '/src/auth/login.php'
Test-Page 'Register'          '/src/auth/register.php'
Test-Page 'Forgot Password'   '/src/auth/forgot-password.php'
Write-Host ''

Write-Host '===== 4. Design Gallery ====='
Test-Page 'design/index.html' '/design/index.html'
Test-Page 'design/style.css'  '/design/style.css'
Write-Host ''

Write-Host '===== 5. Dashboard via preview.php (auto-login) ====='
$adminPages = @('dashboard.php', 'kelola-user.php', 'kelola-cabang-lomba.php', 'kelola-aspek.php', 'kelola-jadwal.php', 'backup-database.php', 'log-aktivitas.php', 'pengaturan.php')
$panitiaPages = @('dashboard.php', 'verifikasi-peserta.php', 'daftar-karya.php', 'tentukan-finalis.php', 'input-pemenang.php', 'kelola-jadwal.php', 'buat-pengumuman.php', 'laporan.php')
$juriPages = @('dashboard.php', 'penilaian-penyisihan.php', 'penilaian-final.php', 'riwayat-penilaian.php')
$pesertaPages = @('dashboard.php', 'daftar-lomba.php', 'upload-karya.php', 'status-pendaftaran.php', 'tim-saya.php', 'pengumuman-saya.php')

foreach ($p in $adminPages) {
    $s = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    Test-Page ('admin/' + $p) ('/design/preview.php?role=admin&page=' + $p) $s
}
foreach ($p in $panitiaPages) {
    $s = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    Test-Page ('panitia/' + $p) ('/design/preview.php?role=panitia&page=' + $p) $s
}
foreach ($p in $juriPages) {
    $s = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    Test-Page ('juri/' + $p) ('/design/preview.php?role=juri&page=' + $p) $s
}
foreach ($p in $pesertaPages) {
    $s = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    Test-Page ('peserta/' + $p) ('/design/preview.php?role=peserta&page=' + $p) $s
}

Write-Host ''
Write-Host '===== SUMMARY ====='
Write-Host ("Total tested: " + $total + ", Failed: " + $fail + ", PHP lint errors: " + $lintFail)
if ($fail -eq 0 -and $lintFail -eq 0) { Write-Host 'STATUS: ALL CLEAR ✓' } else { Write-Host 'STATUS: HAS ISSUES ✗' }

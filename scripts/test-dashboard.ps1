$ErrorActionPreference = "SilentlyContinue"
$base = "http://localhost/pesoma"
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession

# Login as admin
$loginPage = Invoke-WebRequest -Uri "$base/src/auth/login.php" -UseBasicParsing -WebSession $session
$token = ([regex]::Match($loginPage.Content, 'name="csrf_token" value="([^"]+)"')).Groups[1].Value

$form = @{
    identifier = "admin@pesoma.local"
    password   = "AdminPesoma2026!"
    csrf_token = $token
}
$null = Invoke-WebRequest -Uri "$base/src/auth/login.php" -Method POST -Body $form -WebSession $session -UseBasicParsing -MaximumRedirection 5 -TimeoutSec 15

$urls = @(
    "/src/admin/dashboard.php",
    "/src/admin/kelola-user.php",
    "/src/admin/pengaturan.php",
    "/src/admin/kelola-cabang-lomba.php",
    "/src/admin/kelola-jadwal.php",
    "/src/admin/log-aktivitas.php"
)

foreach ($u in $urls) {
    try {
        $r = Invoke-WebRequest -Uri ($base + $u) -UseBasicParsing -WebSession $session -TimeoutSec 15
        if ($r.Content -match "Fatal error|Parse error|Uncaught|Warning:|Undefined") {
            $m = [regex]::Match($r.Content, "(Fatal error|Parse error|Uncaught|Warning:|Undefined)[^<]{0,140}")
            Write-Host ("ISSUE " + $u + " => " + $m.Value)
        }
        else {
            Write-Host ("OK    " + $u + " status=" + $r.StatusCode + " size=" + $r.Content.Length)
        }
    }
    catch {
        Write-Host ("ERR   " + $u + " => " + $_.Exception.Message.Substring(0, [Math]::Min(80, $_.Exception.Message.Length)))
    }
}

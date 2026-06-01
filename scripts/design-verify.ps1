$r = Invoke-WebRequest -Uri 'http://localhost/pesoma/design/index.html' -UseBasicParsing -TimeoutSec 10
$iframes = ([regex]::Matches($r.Content, '<iframe ')).Count
$previewIframes = ([regex]::Matches($r.Content, 'src="preview\.php')).Count
Write-Host ("design/index.html status=" + $r.StatusCode + " size=" + $r.Content.Length)
Write-Host ("total iframes=" + $iframes)
Write-Host ("preview iframes=" + $previewIframes)

# Test preview.php untuk tiap role + sample halaman
$urls = @{
    'admin'   = 'kelola-user.php'
    'panitia' = 'verifikasi-peserta.php'
    'juri'    = 'penilaian-penyisihan.php'
    'peserta' = 'daftar-lomba.php'
}

foreach ($role in $urls.Keys) {
    $page = $urls[$role]
    $session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $url = 'http://localhost/pesoma/design/preview.php?role=' + $role + '&page=' + $page
    try {
        $r2 = Invoke-WebRequest -Uri $url -UseBasicParsing -WebSession $session -MaximumRedirection 5 -TimeoutSec 15
        $isLogin = ($r2.Content -match 'name="csrf_token"' -and $r2.Content -match 'auth-form')
        $hasFatal = ($r2.Content -match 'Fatal error|Parse error|Uncaught')
        if ($hasFatal) {
            Write-Host ("ISSUE  " + $role + "/" + $page)
        }
        elseif ($isLogin) {
            Write-Host ("LOGIN  " + $role + "/" + $page + " (preview.php tidak set session)")
        }
        else {
            Write-Host ("OK     " + $role + "/" + $page + " size=" + $r2.Content.Length)
        }
    }
    catch {
        Write-Host ("ERR    " + $role + "/" + $page)
    }
}

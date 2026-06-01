$file = 'd:\xampp\htdocs\pesoma\design\index.html'
$content = Get-Content $file -Raw

# Untuk dashboard, ganti src="../src/{role}/{page}" -> src="preview.php?role={role}&page={page}"
foreach ($role in @('admin', 'panitia', 'juri', 'peserta')) {
    $pat = '\.\./src/' + $role + '/([a-zA-Z0-9_\-]+\.php)'
    $regex = [regex]::new($pat)
    $content = $regex.Replace($content, "preview.php?role=$role&page=`$1")
}

Set-Content -Path $file -Value $content -NoNewline -Encoding UTF8
Write-Host ("preview.php hits = " + ([regex]::Matches($content, 'preview\.php\?role=')).Count)

$file = 'd:\xampp\htdocs\pesoma\design\index.html'
$content = Get-Content $file -Raw

# Tambah link "Open" di tiap figcaption
$pattern = '<figcaption>([^<]+)</figcaption><iframe src="([^"]+)"'
$regex = [regex]::new($pattern)
$content = $regex.Replace($content, {
        param($m)
        $cap = $m.Groups[1].Value
        $src = $m.Groups[2].Value
        return ('<figcaption>' + $cap + '<a class="dg-frame-open" href="' + $src + '" target="_blank" rel="noopener">Open</a></figcaption><iframe src="' + $src + '"')
    })

# Tambah notice "Login dulu agar konten dashboard tampil" di group dashboard
$noticeAdmin = '<div class="dg-notice"><strong>Catatan:</strong> Login sebagai admin dulu di tab terpisah, lalu refresh halaman ini agar konten dashboard tampil di iframe.</div>'
$noticePanitia = ($noticeAdmin -replace 'admin', 'panitia')
$noticeJuri = ($noticeAdmin -replace 'admin', 'juri')
$noticePeserta = ($noticeAdmin -replace 'admin', 'peserta')

$content = $content -replace '(?s)(<section class="dg-group" id="group-admin">.*?</header>)', ('$1' + "`n      " + $noticeAdmin)
$content = $content -replace '(?s)(<section class="dg-group" id="group-panitia">.*?</header>)', ('$1' + "`n      " + $noticePanitia)
$content = $content -replace '(?s)(<section class="dg-group" id="group-juri">.*?</header>)', ('$1' + "`n      " + $noticeJuri)
$content = $content -replace '(?s)(<section class="dg-group" id="group-peserta">.*?</header>)', ('$1' + "`n      " + $noticePeserta)

Set-Content -Path $file -Value $content -NoNewline -Encoding UTF8
Write-Host ("Done. dg-frame-open count = " + ([regex]::Matches($content, 'dg-frame-open')).Count)
Write-Host ("dg-notice count = " + ([regex]::Matches($content, 'dg-notice')).Count)

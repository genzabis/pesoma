<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

$registrations = peserta_registrations();
$finalists = db_fetch_all('SELECT c.nama_lomba, f.announced_at FROM finalists f JOIN registrations r ON r.id = f.registration_id JOIN competitions c ON c.id = f.competition_id WHERE r.user_id = ? ORDER BY f.announced_at DESC', [current_user_id()]);
$winners = db_fetch_all('SELECT c.nama_lomba, w.juara_ke, w.announced_at FROM winners w JOIN registrations r ON r.id = w.registration_id JOIN competitions c ON c.id = w.competition_id WHERE r.user_id = ? ORDER BY w.announced_at DESC', [current_user_id()]);
$announcements = db_fetch_all('SELECT * FROM announcements WHERE is_published = 1 ORDER BY published_at DESC LIMIT 10');

peserta_header('Pengumuman Saya', 'pengumuman-saya.php');
?>
<div class="grid">
    <section class="card span-6">
        <h2>Notifikasi Verifikasi</h2><?php if (!$registrations): ?><p class="muted">Belum ada pendaftaran.</p><?php else: ?><table class="table">
                <tbody><?php foreach ($registrations as $reg): ?><tr>
                            <td><?= e($reg['nama_lomba']) ?></td>
                            <td><?= badge_status($reg['status_verifikasi']) ?></td>
                        </tr><?php endforeach; ?></tbody>
            </table><?php endif; ?>
    </section>
    <section class="card span-6">
        <h2>Finalis & Juara</h2><?php foreach ($finalists as $f): ?><p><span class="badge ok">Finalis</span> <?= e($f['nama_lomba']) ?> · <?= e(date('d M Y', strtotime($f['announced_at']))) ?></p><?php endforeach; ?><?php foreach ($winners as $w): ?><p><span class="badge ok">Juara <?= (int) $w['juara_ke'] ?></span> <?= e($w['nama_lomba']) ?> · <?= e(date('d M Y', strtotime($w['announced_at']))) ?></p><?php endforeach; ?><?php if (!$finalists && !$winners): ?><p class="muted">Belum ada pengumuman finalis/juara untuk Anda.</p><?php endif; ?>
    </section>
    <section class="card span-12">
        <h2>Pengumuman Publik</h2><?php foreach ($announcements as $a): ?><article style="border-bottom:1px solid var(--line);padding:12px 0"><strong><?= e($a['title']) ?></strong> <span class="badge pending"><?= e($a['type']) ?></span>
                <p><?= e($a['content']) ?></p><small class="muted"><?= e(date('d M Y H:i', strtotime($a['published_at']))) ?></small>
            </article><?php endforeach; ?>
    </section>
</div>
<?php peserta_footer(); ?>
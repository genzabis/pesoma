<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
$stats = [
    'Total User' => (int)(db_fetch('SELECT COUNT(*) total FROM users')['total'] ?? 0),
    'Peserta' => (int)(db_fetch('SELECT COUNT(*) total FROM users WHERE role=?', [ROLE_PESERTA])['total'] ?? 0),
    'Cabang Lomba' => (int)(db_fetch('SELECT COUNT(*) total FROM competitions')['total'] ?? 0),
    'Pendaftaran' => (int)(db_fetch('SELECT COUNT(*) total FROM registrations')['total'] ?? 0),
    'Karya' => (int)(db_fetch('SELECT COUNT(*) total FROM submissions')['total'] ?? 0),
    'Pengumuman' => (int)(db_fetch('SELECT COUNT(*) total FROM announcements')['total'] ?? 0),
];
$roles = db_fetch_all('SELECT role, COUNT(*) total FROM users GROUP BY role ORDER BY role');
$logs = db_fetch_all('SELECT l.*, u.nama FROM activity_logs l LEFT JOIN users u ON u.id=l.user_id ORDER BY l.created_at DESC LIMIT 8');
admin_header('Dashboard Admin', 'dashboard.php');
?>
<div class="grid"><?php foreach ($stats as $label => $value): ?><section class="card span-3">
            <h3><?= e($label) ?></h3>
            <div class="stat"><?= $value ?></div>
        </section><?php endforeach; ?><section class="card span-6">
        <h2>User per Role</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody><?php foreach ($roles as $r): ?><tr>
                        <td><span class="badge info"><?= e($r['role']) ?></span></td>
                        <td><?= e((string)$r['total']) ?></td>
                    </tr><?php endforeach; ?></tbody>
        </table>
    </section>
    <section class="card span-6">
        <h2>Log Terbaru</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Aksi</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody><?php foreach ($logs as $l): ?><tr>
                        <td><?= e($l['nama'] ?? '-') ?></td>
                        <td><?= e($l['action']) ?></td>
                        <td><?= e($l['created_at']) ?></td>
                    </tr><?php endforeach; ?></tbody>
        </table>
    </section>
</div>
<?php admin_footer(); ?>
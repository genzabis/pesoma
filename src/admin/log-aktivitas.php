<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

$role = $_GET['role'] ?? '';
$keyword = trim((string)($_GET['q'] ?? ''));
$where = [];
$params = [];
if ($role !== '') {
    $where[] = 'l.role = ?';
    $params[] = $role;
}
if ($keyword !== '') {
    $where[] = '(l.action LIKE ? OR l.description LIKE ? OR u.nama LIKE ?)';
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}
$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$logs = db_fetch_all("SELECT l.*, u.nama, u.email FROM activity_logs l LEFT JOIN users u ON u.id=l.user_id $sqlWhere ORDER BY l.created_at DESC LIMIT 200", $params);
admin_header('Log Aktivitas', 'log-aktivitas.php');
?>
<section class="card">
    <form class="filters" method="GET">
        <div class="field"><label>Role</label><select name="role">
                <option value="">Semua</option><?php foreach ([ROLE_ADMIN, ROLE_PANITIA, ROLE_JURI, ROLE_PESERTA] as $r): ?><option value="<?= e($r) ?>" <?= $role === $r ? 'selected' : '' ?>><?= e($r) ?></option><?php endforeach; ?>
            </select></div>
        <div class="field"><label>Kata Kunci</label><input name="q" value="<?= e($keyword) ?>" placeholder="aksi, deskripsi, nama user"></div>
        <button class="btn">Filter</button>
        <a class="btn secondary" href="log-aktivitas.php">Reset</a>
    </form>
</section>
<section class="card">
    <h2>Riwayat Aktivitas</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>User</th>
                <th>Role</th>
                <th>Aksi</th>
                <th>Deskripsi</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody><?php foreach ($logs as $l): ?><tr>
                    <td><?= e($l['created_at']) ?></td>
                    <td><?= e($l['nama'] ?? '-') ?><br><small class="muted"><?= e($l['email'] ?? '') ?></small></td>
                    <td><span class="badge info"><?= e($l['role'] ?? '-') ?></span></td>
                    <td><?= e($l['action']) ?></td>
                    <td><?= e($l['description'] ?? '-') ?></td>
                    <td><?= e($l['ip_address'] ?? '-') ?></td>
                </tr><?php endforeach; ?></tbody>
    </table>
</section>
<?php admin_footer(); ?>
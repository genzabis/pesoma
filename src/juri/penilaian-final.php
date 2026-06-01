<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
$juriId = juri_user_id();
$registrationId = (int)($_GET['registration_id'] ?? 0);
$rows = db_fetch_all('SELECT r.id registration_id,r.nomor_peserta,u.nama,u.nim,u.fakultas,c.id competition_id,c.nama_lomba,f.rank_penyisihan,sf.id score_id,sf.total FROM finalists f JOIN registrations r ON r.id=f.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=f.competition_id LEFT JOIN scores_final sf ON sf.registration_id=r.id AND sf.juri_id=? ORDER BY c.nama_lomba,f.rank_penyisihan,u.nama', [$juriId]);
$selected = $registrationId ? db_fetch('SELECT r.*,u.nama,u.nim,u.fakultas,c.id competition_id,c.nama_lomba FROM registrations r JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id JOIN finalists f ON f.registration_id=r.id WHERE r.id=?', [$registrationId]) : null;
$existing = $selected ? db_fetch('SELECT * FROM scores_final WHERE registration_id=? AND juri_id=?', [$registrationId, $juriId]) : null;
$aspects = $selected ? juri_aspects((int)$selected['competition_id'], 'final') : [];
$oldScores = [];
if ($existing) {
    foreach (json_decode((string)$existing['nilai_per_aspek'], true) ?: [] as $it) {
        $oldScores[] = (float)($it['nilai'] ?? 0);
    }
}
juri_header('Penilaian Final', 'penilaian-final.php');
?>
<div class="grid">
    <section class="card span-12">
        <h2 style="margin-top:0">Daftar Finalis</h2>
        <?php if (!$rows): ?>
            <div style="text-align:center;padding:40px;color:var(--text-secondary)">
                <i class="fas fa-inbox" style="font-size:48px;margin-bottom:12px;opacity:.3"></i>
                <p>Tidak ada finalis yang perlu dinilai</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>No Peserta</th>
                        <th>Nama Ketua</th>
                        <th>Cabang Lomba</th>
                        <th style="text-align:center">Status</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= e($r['nomor_peserta']) ?></strong></td>
                            <td>
                                <?= e($r['nama']) ?><br>
                                <small class="muted"><?= e($r['nim']) ?></small>
                            </td>
                            <td><?= e($r['nama_lomba']) ?></td>
                            <td style="text-align:center"><?= juri_status_badge((bool)$r['score_id']) ?></td>
                            <td>
                                <a class="btn small" href="penilaian-final.php?registration_id=<?= (int)$r['registration_id'] ?>" style="width:100%">Nilai</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <?php if ($selected): ?>
        <section class="card span-12">
            <h2 style="margin-top:0">Form Penilaian Final</h2>
            <div style="padding:12px;background:rgba(26,157,110,.04);border-radius:8px;margin-bottom:16px;font-size:14px">
                <strong><?= e($selected['nomor_peserta']) ?></strong> - <?= e($selected['nama']) ?> 
                <br><small class="muted"><?= e($selected['nama_lomba']) ?></small>
            </div>

            <form method="POST" action="proses-penilaian-final.php" id="scoreForm">
                <?= csrf_field() ?>
                <input type="hidden" name="registration_id" value="<?= (int)$registrationId ?>">

                <div style="display:grid;gap:16px;margin-bottom:16px">
                    <?php foreach ($aspects as $i => $a): 
                        $nilai = $oldScores[$i] ?? 0; 
                    ?>
                        <div style="display:grid;grid-template-columns:1fr 120px 80px;gap:12px;align-items:center;padding:12px;border:1px solid var(--border);border-radius:6px">
                            <div>
                                <strong><?= e($a['nama'] ?? $a['nama_aspek'] ?? 'Aspek') ?></strong><br>
                                <small class="muted">Bobot <?= e((string)($a['bobot'] ?? 0)) ?>%</small>
                            </div>
                            <input class="nilai" type="number" name="nilai[]" min="0" max="100" step="0.01" value="<?= e((string)$nilai) ?>" data-bobot="<?= e((string)($a['bobot'] ?? 0)) ?>" required style="padding:8px;border:1px solid var(--border);border-radius:4px">
                            <output style="text-align:right;font-weight:700;color:var(--primary)">0</output>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="field">
                    <label for="komentar">Catatan / Komentar</label>
                    <textarea id="komentar" name="komentar" rows="4"><?= e($existing['komentar'] ?? '') ?></textarea>
                </div>

                <div style="padding:12px;background:rgba(26,157,110,.08);border-radius:6px;margin-bottom:16px;text-align:right">
                    <strong style="font-size:18px">Total Skor: <span id="grandTotal" style="color:var(--primary)">0</span></strong>
                </div>

                <button class="btn" type="submit" style="width:100%">Simpan Nilai Final</button>
            </form>
        </section>
    <?php endif; ?>
</div>
<script>
    function hitung() {
        let t = 0;
        document.querySelectorAll('.nilai').forEach(i => {
            const n = Math.max(0, Math.min(100, parseFloat(i.value || 0)));
            const b = parseFloat(i.dataset.bobot || 0);
            const s = n * b / 100;
            t += s;
            i.parentElement.querySelector('output').textContent = s.toFixed(2)
        });
        const g = document.getElementById('grandTotal');
        if (g) g.textContent = t.toFixed(2)
    }
    document.querySelectorAll('.nilai').forEach(i => i.addEventListener('input', hitung));
    hitung();
</script>
<?php juri_footer(); ?>
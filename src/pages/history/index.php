<?php
// src/pages/history/index.php → route: /history

require_once __DIR__ . '/../../includes/db.php';

$appName = 'SLD Test';
$appDesc = 'Riwayat Hasil Analisis Gaya Belajar';
$title   = 'Riwayat';

// Fetch all analyses (latest first)
$stmt = $pdo->query("SELECT id, nama, nisn, kelas, sekolah, jenjang, jurusan, scores_json, percentages_json, created_at FROM analyses ORDER BY id DESC");
$analyses = $stmt->fetchAll();

$head = <<<HTML
<style>
    .dt-row:hover { background: #fff7ed; cursor: pointer; }
    .dt-search { border: 1px solid #cbd5e1; border-radius: 6px; padding: 5px 8px; width: 100%; }
    .dt-row { transition: background 0.15s; }
    .dt-row:hover { background: #ffedd5 !important; }
    .dt-header { background: #f1f5f9; }
    .dt-header th { cursor: pointer; position: relative; padding-right: 24px; }
    .dt-row.active { background: #ffedd5; }
</style>
HTML;

ob_start();
?>

<!-- History Datatable -->
<section class="glass-card rounded-2xl p-6 sm:p-8 transition-all">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex-1">
            <h2 class="text-2xl font-extrabold text-slate-800">Riwayat Analisis</h2>
            <p class="text-slate-600 text-xs sm:text-sm mt-1">Daftar semua hasil pemetaan gaya belajar yang telah disimpan.</p>
        </div>
    </div>

    <?php if (empty($analyses)): ?>
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 rounded-2xl shadow-sm mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-600">Belum ada analisis</h3>
            <p class="text-slate-500 text-sm mt-1">Mulai kuesioner di <a href="/" class="text-brand-600 font-semibold underline">halaman utama</a>.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <form id="historySearchForm" class="flex mb-4">
                <input type="text" id="dtSearch" class="dt-search px-3 py-2 w-full sm:w-3/4 sm:max-w-96 sm:mr-2" placeholder="Cari nama siswa atau NISN...">
                <button type="submit" class="px-3 py-1.5 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </form>
            
            <table class="w-full text-sm text-left text-slate-700">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 dt-header">
                        <th class="py-3 px-3 font-bold uppercase tracking-wider text-xs w-8">No</th>
                        <th class="py-3 px-3 font-bold uppercase tracking-wider text-xs">Nama</th>
                        <th class="py-3 px-3 font-bold uppercase tracking-wider text-xs hidden sm:table-cell">Sekolah</th>
                        <th class="py-3 px-3 font-bold uppercase tracking-wider text-xs">Jenjang</th>
                        <th class="py-3 px-3 font-bold uppercase tracking-wider text-xs hidden md:table-cell">Tanggal</th>
                        <th class="py-3 px-3 font-bold uppercase tracking-wider text-xs">Dominan</th>
                        <th class="py-3 px-3 font-bold uppercase tracking-wider text-xs text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="dtBody">
                    <?php foreach ($analyses as $a):
                        $pct = json_decode($a->percentages_json, true) ?: [];
                        $dom = 'Visual'; $domPct = (int)($pct['visual'] ?? 0);
                        if (($pct['auditori'] ?? 0) > $domPct) { $dom = 'Auditori'; $domPct = $pct['auditori']; }
                        if (($pct['kinestetik'] ?? 0) > $domPct) { $dom = 'Kinestetik'; $domPct = $pct['kinestetik']; }
                        $domColor = $dom === 'Visual' ? 'bg-blue-100 text-blue-700' : ($dom === 'Auditori' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700');
                    ?>
                        <tr class="border-b border-slate-100 dt-row hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-3 font-mono text-slate-400 text-xs"><?= $a->id ?></td>
                            <td class="py-3 px-3 font-semibold text-slate-800"><?= htmlspecialchars($a->nama) ?></td>
                            <td class="py-3 px-3 text-slate-600 hidden sm:table-cell"><?= htmlspecialchars($a->sekolah) ?></td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 bg-brand-100 text-brand-700 text-xs font-bold rounded-full"><?= htmlspecialchars($a->jenjang) ?></span>
                            </td>
                            <td class="py-3 px-3 text-slate-500 text-xs hidden md:table-cell">
                                <div class="flex flex-col">
                                    <span class="font-bold"><?= date('d M Y', strtotime($a->created_at)) ?></span>
                                    <span class="text-[10px] opacity-70"><?= date('H:i', strtotime($a->created_at)) ?> WIB</span>
                                </div>
                            </td>
                            <td class="py-3 px-3"><span class="px-2 py-0.5 text-[10px] font-bold rounded-full <?= $domColor ?>"><?= $dom ?> <?= $domPct ?>%</span></td>
                            <td class="py-3 px-3 text-right">
                                <a href="/analisis?id=<?= $a->id ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7z"/></svg>
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
$scripts = '<script>lucide.createIcons();</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("dtSearch");
    const rows = document.querySelectorAll("#dtBody tr");
    
    searchInput.addEventListener("input", function() {
        const searchTerm = this.value.toLowerCase();
        rows.forEach(row => {
            const name = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
            row.style.display = name.includes(searchTerm) ? "" : "none";
        });
    });
});
</script>';

include __DIR__ . '/../../components/template.php';
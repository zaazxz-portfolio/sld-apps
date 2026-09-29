<?php
// src/pages/analisis/index.php → route: /analisis?id=1

require_once __DIR__ . '/../../includes/db.php';

$id = (int)($_GET['id'] ?? 0);
$appName = 'SLD Test';
$appDesc = 'Laporan Analisis Gaya Belajar';

if ($id < 1) {
    http_response_code(400);
    $title = 'ID Tidak Valid';
    ob_start();
    ?>
    <div class="glass-card rounded-2xl p-12 text-center">
        <h1 class="text-2xl font-extrabold text-slate-800 mb-2">ID Analisis Tidak Valid</h1>
        <p class="text-slate-500 text-sm mb-6">Parameter <code class="bg-slate-100 px-1.5 py-0.5 rounded">id</code> pada URL tidak ditemukan.</p>
        <a href="/history" class="inline-block px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow transition">Lihat Riwayat</a>
    </div>
    <?php
    $content = ob_get_clean();
    $scripts = '<script>lucide.createIcons();</script>';
    include __DIR__ . '/../../components/template.php';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM analyses WHERE id = :id");
$stmt->execute(['id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    http_response_code(404);
    $title = 'Analisis Tidak Ditemukan';
    ob_start();
    ?>
    <div class="glass-card rounded-2xl p-12 text-center">
        <h1 class="text-2xl font-extrabold text-slate-800 mb-2">Analisis Tidak Ditemukan</h1>
        <p class="text-slate-500 text-sm mb-6">Data dengan ID <span class="font-mono font-bold"><?= $id ?></span> tidak ada di database.</p>
        <a href="/history" class="inline-block px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow transition">Lihat Riwayat</a>
    </div>
    <?php
    $content = ob_get_clean();
    $scripts = '<script>lucide.createIcons();</script>';
    include __DIR__ . '/../../components/template.php';
    exit;
}

$scores      = json_decode($row->scores_json, true) ?: [];
$percentages = json_decode($row->percentages_json, true) ?: [];
$answers     = json_decode($row->answers_json, true) ?: [];
$ai          = json_decode($row->ai_result_json, true) ?: [];

$e = fn($v) => htmlspecialchars((string)($v ?? ''));

// Dominan
$vak = [
    'Visual'     => (int)($percentages['visual'] ?? 0),
    'Auditori'   => (int)($percentages['auditori'] ?? 0),
    'Kinestetik' => (int)($percentages['kinestetik'] ?? 0),
];
$dom = array_key_first($vak);
$domPct = $vak[$dom];

$styles = [
    'visual'     => ['label' => 'Visual',     'text' => 'text-blue-800',  'num' => 'text-blue-700',    'bar' => 'bg-blue-600',     'track' => 'bg-blue-200',    'bg' => 'bg-blue-50/50',   'border' => 'border-blue-200',    'icon' => 'eye'],
    'auditori'   => ['label' => 'Auditori',   'text' => 'text-amber-800', 'num' => 'text-amber-700',   'bar' => 'bg-amber-600',    'track' => 'bg-amber-200',   'bg' => 'bg-amber-50/50',  'border' => 'border-amber-200',   'icon' => 'volume-2'],
    'kinestetik' => ['label' => 'Kinestetik', 'text' => 'text-emerald-800', 'num' => 'text-emerald-700', 'bar' => 'bg-emerald-600', 'track' => 'bg-emerald-200', 'bg' => 'bg-emerald-50/50', 'border' => 'border-emerald-200', 'icon' => 'activity'],
];

$totalSoal = count($answers);
$tanggal = date('j F Y', strtotime($row->created_at));
$list = fn($items) => is_array($items) && $items
    ? '<ul class="space-y-2 text-xs text-slate-700 list-disc list-inside">'
        . implode('', array_map(fn($i) => '<li>' . htmlspecialchars((string)$i) . '</li>', $items))
        . '</ul>'
    : '<p class="text-xs text-slate-400">-</p>';

$title = 'Laporan - ' . $row->nama;
$head = <<<HTML
<style>
    #reportDocument table { width: 100%; border-collapse: collapse; }
    @media print {
        .no-print { display: none !important; }
        #reportDocument { box-shadow: none !important; border: none !important; }
    }
</style>
HTML;

ob_start();
?>

<div class="glass-card p-4 rounded-2xl flex flex-wrap items-center justify-between gap-3 no-print mb-6">
    <div class="flex items-center gap-2 text-slate-700 font-bold text-sm">
        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
        <span>Laporan Analisis Gaya Belajar</span>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="/" class="px-3.5 py-2 text-xs font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-300 rounded-lg shadow-sm transition flex items-center gap-1.5">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tes Baru
        </a>
        <a href="/history" class="px-3.5 py-2 text-xs font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-300 rounded-lg shadow-sm transition flex items-center gap-1.5">
            <i data-lucide="history" class="w-3.5 h-3.5"></i> Riwayat
        </a>
        <button onclick="window.print()" class="px-4 py-2 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow transition flex items-center gap-1.5">
            <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak Laporan PDF
        </button>
    </div>
</div>

<div id="reportDocument" class="glass-card bg-white rounded-2xl p-6 sm:p-10 shadow-xl space-y-8">

    <!-- Header -->
    <div class="border-b-2 border-slate-800 pb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-2xl font-black tracking-tight text-slate-900">SLD Test</span>
                <span class="text-xs px-2.5 py-0.5 font-bold bg-slate-800 text-white rounded">Kurikulum Merdeka</span>
            </div>
            <p class="text-xs text-slate-600 font-medium">Laporan Lanjutan Diagnosis Gaya Belajar &amp; Rekomendasi Pedagogis Berbasis AI</p>
        </div>
        <div class="text-right text-xs text-slate-500 font-medium">
            <p>Tanggal: <?= $e($tanggal) ?></p>
            <p class="font-mono text-[11px] text-slate-400">ID: EDU-<?= $e($row->id) ?></p>
        </div>
    </div>

    <!-- Profil Siswa -->
    <div>
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <i data-lucide="user" class="w-4 h-4 text-brand-600"></i> Profil Siswa
        </h3>
        <table class="w-full text-xs text-left text-slate-700 border-collapse border border-slate-300">
            <tbody>
                <tr class="border-b border-slate-300">
                    <th class="py-2.5 px-4 bg-slate-100 font-bold w-1/4 border-r border-slate-300">Nama Siswa</th>
                    <td class="py-2.5 px-4 font-semibold w-1/4 border-r border-slate-300"><?= $e($row->nama) ?></td>
                    <th class="py-2.5 px-4 bg-slate-100 font-bold w-1/4 border-r border-slate-300">NISN</th>
                    <td class="py-2.5 px-4 font-mono w-1/4"><?= $e($row->nisn) ?></td>
                </tr>
                <tr class="border-b border-slate-300">
                    <th class="py-2.5 px-4 bg-slate-100 font-bold border-r border-slate-300">Sekolah</th>
                    <td class="py-2.5 px-4 border-r border-slate-300"><?= $e($row->sekolah) ?></td>
                    <th class="py-2.5 px-4 bg-slate-100 font-bold border-r border-slate-300">Kelas / Jenjang</th>
                    <td class="py-2.5 px-4"><?= $e($row->kelas) ?> (<?= $e($row->jenjang) ?>)</td>
                </tr>
                <?php if (!empty($row->jurusan)): ?>
                <tr class="border-b border-slate-300">
                    <th class="py-2.5 px-4 bg-slate-100 font-bold border-r border-slate-300">Kompetensi Keahlian</th>
                    <td colspan="3" class="py-2.5 px-4 font-bold text-brand-700"><?= $e($row->jurusan) ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- VAK -->
    <div>
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <i data-lucide="bar-chart-3" class="w-4 h-4 text-brand-600"></i> Hasil Persentase Modalitas Sensorik (VAK)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <?php foreach ($styles as $key => $s):
                $pct = (int)($percentages[$key] ?? 0);
                $jml = (int)($scores[$key] ?? 0);
            ?>
            <div class="p-4 rounded-xl border <?= $s['border'] ?> <?= $s['bg'] ?>">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-extrabold <?= $s['text'] ?> uppercase tracking-wider flex items-center gap-1">
                        <i data-lucide="<?= $s['icon'] ?>" class="w-4 h-4"></i> <?= $s['label'] ?>
                    </span>
                    <span class="text-xl font-black <?= $s['num'] ?>"><?= $pct ?>%</span>
                </div>
                <div class="w-full <?= $s['track'] ?> h-3 rounded-full overflow-hidden">
                    <div class="<?= $s['bar'] ?> h-full rounded-full" style="width: <?= $pct ?>%"></div>
                </div>
                <p class="text-[11px] text-slate-500 mt-2"><?= $jml ?> dari <?= $totalSoal ?> Jawaban</p>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="p-4 rounded-xl bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-brand-500 rounded-lg text-white">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[11px] uppercase tracking-wider text-slate-300 font-semibold block">Gaya Belajar Dominan</span>
                    <h4 class="text-lg font-extrabold text-brand-300">Tipe <?= $e($dom) ?> (<?= $domPct ?>%)</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Content -->
    <div class="space-y-6 pt-2">
        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
            <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <i data-lucide="file-text" class="w-4 h-4 text-brand-600"></i> Analisis Karakteristik &amp; Kendala
            </h4>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed"><?= $e($ai['analisis_karakteristik'] ?? '-') ?></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-slate-200 p-5 rounded-xl bg-white shadow-sm">
                <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-1.5 text-brand-700">
                    <i data-lucide="user-check" class="w-4 h-4"></i> Rekomendasi Strategi Belajar Mandiri
                </h4>
                <?= $list($ai['rekomendasi_mandiri'] ?? null) ?>
            </div>
            <div class="border border-slate-200 p-5 rounded-xl bg-white shadow-sm">
                <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-1.5 text-brand-700">
                    <i data-lucide="school" class="w-4 h-4"></i> Rekomendasi Lingkungan Sekolah &amp; Praktik
                </h4>
                <?= $list($ai['rekomendasi_sekolah'] ?? null) ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1 border border-slate-200 p-5 rounded-xl bg-white shadow-sm">
                <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-1.5 text-brand-700">
                    <i data-lucide="laptop" class="w-4 h-4"></i> Rekomendasi Media Digital
                </h4>
                <?= $list($ai['media_digital'] ?? null) ?>
            </div>
            <div class="md:col-span-2 border border-slate-200 p-5 rounded-xl bg-amber-50/60 shadow-sm">
                <h4 class="text-xs font-extrabold text-amber-900 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <i data-lucide="compass" class="w-4 h-4 text-amber-700"></i> Panduan Guru (Pembelajaran Berdiferensiasi)
                </h4>
                <p class="text-xs text-slate-700 leading-relaxed"><?= $e($ai['panduan_guru'] ?? '-') ?></p>
            </div>
        </div>
    </div>

    <!-- Jawaban Kuesioner -->
    <?php if ($answers): ?>
    <div>
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <i data-lucide="list-checks" class="w-4 h-4 text-brand-600"></i> Jawaban Kuesioner
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700 border-collapse border border-slate-300">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-300">
                        <th class="py-2 px-3 font-bold w-10">No</th>
                        <th class="py-2 px-3 font-bold">Jawaban Siswa</th>
                        <th class="py-2 px-3 font-bold w-28">Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($answers as $k => $ans): ?>
                    <tr class="border-b border-slate-200">
                        <td class="py-2 px-3 font-mono text-slate-400"><?= $k + 1 ?></td>
                        <td class="py-2 px-3"><?= $e($ans['answer'] ?? '') ?></td>
                        <td class="py-2 px-3">
                            <span class="px-2 py-0.5 bg-slate-200 text-slate-700 text-[10px] font-bold rounded"><?= $e($ans['type'] ?? '') ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Signatures -->
    <div class="pt-10 border-t border-slate-200 grid grid-cols-2 text-center text-xs text-slate-600">
        <div>
            <p class="mb-12">Guru Bimbingan Konseling / Wali Kelas</p>
            <p class="font-bold border-b border-slate-400 inline-block px-8 pb-1">(...................................................)</p>
            <p class="text-[10px] text-slate-400 mt-1">NIP. -</p>
        </div>
        <div>
            <p class="mb-12">Siswa Terdiagnosa</p>
            <p class="font-bold border-b border-slate-400 inline-block px-8 pb-1"><?= $e($row->nama) ?></p>
            <p class="text-[10px] text-slate-400 mt-1">Siswa Bersangkutan</p>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
$scripts = '<script>lucide.createIcons();</script>';

include __DIR__ . '/../../components/template.php';

<?php
// src/pages/about/index.php → route: /about

$appName = 'EduStyle AI';
$appDesc = 'Pemetaan & Analisis Gaya Belajar Siswa';
$title   = 'Tentang';

$head = <<<HTML
<style>
    .about-hero {
        background: linear-gradient(135deg, #ea580c 0%, #f97316 50%, #fbbf24 100%);
    }
    .step-num {
        font-variant-numeric: tabular-nums;
    }
</style>
HTML;

ob_start();
?>

<!-- Hero -->
<section class="glass-card rounded-2xl overflow-hidden mb-6">
    <div class="about-hero px-6 sm:px-10 py-10 sm:py-14 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl mb-4">
            <i data-lucide="graduation-cap" class="w-7 h-7 text-white"></i>
        </div>
        <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white mb-3">EduStyle AI</h1>
        <p class="text-white/90 text-sm sm:text-base max-w-2xl mx-auto font-medium">
            Aplikasi pemetaan gaya belajar siswa berbasis AI untuk mengetahui cara belajar terbaik bagi setiap siswa.
        </p>
    </div>
</section>

<!-- Masalah & Solusi -->
<section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="border border-slate-200 p-6 rounded-2xl bg-white/70 shadow-sm">
        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i data-lucide="circle-alert" class="w-4 h-4 text-red-500"></i> Masalah
        </h2>
        <p class="text-sm text-slate-600 leading-relaxed">
            Setiap siswa menyerap informasi dengan cara yang berbeda. Sebagian besar siswa menghafal dengan cara yang
            tidak cocok dengan gaya belajarnya, sehingga hasilnya suboptimal. Guru sering tidak tahu cara belajar
            yang paling efektif untuk masing-masing siswa tanpa assessment khusus.
        </p>
    </div>
    <div class="border border-slate-200 p-6 rounded-2xl bg-white/70 shadow-sm">
        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i data-lucide="lightbulb" class="w-4 h-4 text-brand-600"></i> Solusi
        </h2>
        <p class="text-sm text-slate-600 leading-relaxed">
            EduStyle AI melakukan asesmen diagnosis gaya belajar VAK (Visual, Auditori, Kinestetik) melalui kuesioner
            adaptif sesuai jenjang, lalu menganalisis hasilnya dengan AI untuk menghasilkan rekomendasi pembelajaran
            yang personal dan siap pakai oleh guru maupun siswa.
        </p>
    </div>
</section>

<!-- Fitur -->
<section class="glass-card rounded-2xl p-6 sm:p-8 mb-6">
    <h2 class="text-2xl font-extrabold text-slate-800 mb-1">Fitur Utama</h2>
    <p class="text-slate-600 text-xs sm:text-sm mb-6">Empat tahap sederhana dari asesmen hingga laporan.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="flex gap-3.5">
            <div class="step-num flex-shrink-0 w-9 h-9 rounded-xl bg-brand-100 text-brand-700 font-extrabold flex items-center justify-center text-sm">1</div>
            <div>
                <h3 class="text-sm font-bold text-slate-800 mb-1">Data Siswa</h3>
                <p class="text-xs text-slate-600 leading-relaxed">Isi identitas dan jenjang pendidikan (SMP/MTS atau SMK/MAK) untuk mendapat instrumen yang relevan.</p>
            </div>
        </div>
        <div class="flex gap-3.5">
            <div class="step-num flex-shrink-0 w-9 h-9 rounded-xl bg-brand-100 text-brand-700 font-extrabold flex items-center justify-center text-sm">2</div>
            <div>
                <h3 class="text-sm font-bold text-slate-800 mb-1">Kuesioner Adaptif</h3>
                <p class="text-xs text-slate-600 leading-relaxed">12 soal berbeda untuk jenjang vokasional maupun konseptual, mengukur kecenderungan VAK.</p>
            </div>
        </div>
        <div class="flex gap-3.5">
            <div class="step-num flex-shrink-0 w-9 h-9 rounded-xl bg-brand-100 text-brand-700 font-extrabold flex items-center justify-center text-sm">3</div>
            <div>
                <h3 class="text-sm font-bold text-slate-800 mb-1">Analisis AI</h3>
                <p class="text-xs text-slate-600 leading-relaxed">Data dioleh menjadi profil karakteristik, hambatan belajar, dan strategi pembelajaran berdiferensiasi.</p>
            </div>
        </div>
        <div class="flex gap-3.5">
            <div class="step-num flex-shrink-0 w-9 h-9 rounded-xl bg-brand-100 text-brand-700 font-extrabold flex items-center justify-center text-sm">4</div>
            <div>
                <h3 class="text-sm font-bold text-slate-800 mb-1">Laporan Siap Pakai</h3>
                <p class="text-xs text-slate-600 leading-relaxed">Laporan tersimpan otomatis dengan kolom tanda tangan, siap dicetak PDF untuk keperluan sekolah.</p>
            </div>
        </div>
    </div>
</section>

<!-- Output -->
<section class="glass-card rounded-2xl p-6 sm:p-8 mb-6">
    <h2 class="text-2xl font-extrabold text-slate-800 mb-1">Apa yang Anda Dapatkan</h2>
    <p class="text-slate-600 text-xs sm:text-sm mb-6">Enam komponen laporan untuk setiap siswa.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/50">
            <h3 class="text-xs font-extrabold text-blue-800 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Persentase VAK
            </h3>
            <p class="text-xs text-slate-600">Visual, Auditori, dan Kinestetik beserta gaya belajar dominan.</p>
        </div>
        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                <i data-lucide="file-text" class="w-4 h-4 text-brand-600"></i> Analisis Karakteristik
            </h3>
            <p class="text-xs text-slate-600">Cara siswa menyerap informasi dan potensi hambatannya.</p>
        </div>
        <div class="p-4 rounded-xl border border-slate-200 bg-white">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-1.5 flex items-center gap-1.5 text-brand-700">
                <i data-lucide="user-check" class="w-4 h-4"></i> Belajar Mandiri
            </h3>
            <p class="text-xs text-slate-600">Strategi belajar mandiri sesuai gaya belajar siswa.</p>
        </div>
        <div class="p-4 rounded-xl border border-slate-200 bg-white">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-1.5 flex items-center gap-1.5 text-brand-700">
                <i data-lucide="school" class="w-4 h-4"></i> Lingkungan Sekolah
            </h3>
            <p class="text-xs text-slate-600">Rekomendasi di kelas, bengkel, laboratorium, dan dunia kerja.</p>
        </div>
        <div class="p-4 rounded-xl border border-slate-200 bg-white">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-1.5 flex items-center gap-1.5 text-brand-700">
                <i data-lucide="laptop" class="w-4 h-4"></i> Media Digital
            </h3>
            <p class="text-xs text-slate-600">Rekomendasi media yang paling efektif untuk siswa.</p>
        </div>
        <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/50">
            <h3 class="text-xs font-extrabold text-amber-900 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                <i data-lucide="compass" class="w-4 h-4 text-amber-700"></i> Panduan Guru
            </h3>
            <p class="text-xs text-slate-600">Instruksi taktis penerapan pembelajaran berdiferensiasi.</p>
        </div>
    </div>
</section>

<!-- Metodologi -->
<section class="border border-slate-200 p-6 rounded-2xl bg-white/70 shadow-sm mb-6">
    <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
        <i data-lucide="info" class="w-4 h-4 text-brand-600"></i> Catatan Metodologis
    </h2>
    <p class="text-sm text-slate-600 leading-relaxed">
        Gaya belajar VAK (Visual, Auditori, Kinestetik) merupakan model klasifikasi yang umum digunakan dalam dunia
        pendidikan, dengan memetakan kecenderungan preferensi sensory siswa. Hasil EduStyle AI adalah
        <strong>alat bantu refleksi dan komunikasi guru–murid</strong>, bukan diagnosis klinis atau label tetap yang
        membatasi potensi siswa. Preferensi belajar dapat berubah seiring waktu, pengalaman, dan konteks pembelajaran.
        Gunakan hasil ini sebagai bahan diskusi bersama siswa, bukan sebagai label permanen.
    </p>
</section>

<!-- Developer -->
<section class="glass-card rounded-2xl p-6 sm:p-8">
    <h2 class="text-2xl font-extrabold text-slate-800 mb-6">Pengembang</h2>

    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <div class="flex-shrink-0 w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 to-orange-400 text-white flex items-center justify-center shadow-lg">
            <i data-lucide="code-2" class="w-8 h-8"></i>
        </div>
        <div class="flex-1">
            <h3 class="text-lg font-extrabold text-slate-800">Teman Ngoding ID</h3>
            <p class="text-sm text-slate-600 mb-3">Platform belajar pemrograman untuk membantu siswa menemukan cara belajar yang paling efektif.</p>
            <a href="https://temanngoding.id/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow transition">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                Kunjungi Teman Ngoding ID
            </a>
        </div>
    </div>

    <div class="mt-6 pt-6 border-t border-slate-200">
        <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Misi</h3>
        <p class="text-sm text-slate-600 leading-relaxed">
            Setiap siswa berhak mendapatkan cara belajar yang tepat. EduStyle AI dikembangkan untuk membantu guru,
            orang tua, dan siswa memahami cara belajar terbaik bagi masing-masing individu dengan cara yang praktis,
            terukur, dan mudah ditindaklanjuti di kelas.
        </p>
    </div>
</section>

<div class="mt-6 flex justify-center gap-3 no-print">
    <a href="/" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow transition">
        <i data-lucide="play" class="w-4 h-4"></i> Mulai Asesmen
    </a>
    <a href="/history" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-100 text-slate-700 text-sm font-bold border border-slate-300 rounded-xl shadow-sm transition">
        <i data-lucide="history" class="w-4 h-4"></i> Lihat Riwayat
    </a>
</div>

<?php
$content = ob_get_clean();
$scripts = '';

include __DIR__ . '/../../components/template.php';

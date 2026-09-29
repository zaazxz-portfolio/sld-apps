<?php
// src/pages/404.php → fallback semua URL yang tidak ditemukan
http_response_code(404);

$appName = 'SLD Test';
$title   = '404 - Halaman Tidak Ditemukan';

ob_start();
?>
<div class="glass-card rounded-2xl p-8 sm:p-12 text-center max-w-lg mx-auto">
    <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 rounded-3xl shadow-sm mb-6">
        <i data-lucide="shield-alert" class="w-10 h-10 text-slate-400"></i>
    </div>
    <h1 class="text-3xl font-black text-slate-800 mb-2">404</h1>
    <p class="text-xl font-bold text-slate-700 mb-3">Halaman Tidak Ditemukan</p>
    <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">
        Halaman yang kamu cari sudah hilang. Mungkin sudah dipindah, didelete, atau emang nggak pernah ada.
    </p>
    <a href="/" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Kembali ke Beranda
    </a>
</div>
<?php
$content = ob_get_clean();
$scripts = '';
include __DIR__ . '/../components/template.php';
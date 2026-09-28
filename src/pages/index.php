<?php
// src/pages/index.php → route: /

$appName = 'SLD Test';
$appDesc = 'Aplikasi pengujian metode belajar siswa';
$title   = 'Beranda';

$content = <<<HTML
<div class="glass-card rounded-2xl p-8 sm:p-12 text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-tr from-brand-600 to-orange-400 rounded-2xl shadow-lg mb-6">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
    </div>
    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800 mb-3">Selamat Datang!</h1>
    <p class="text-slate-500 text-sm sm:text-base max-w-md mx-auto">
        Project PHP Native berjalan di Docker dengan routing berbasis filesystem.
    </p>
</div>
HTML;

include __DIR__ . '/../components/template.php';

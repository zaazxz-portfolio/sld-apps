<?php
// src/pages/index.php → route: /

require_once __DIR__ . '/../includes/db.php';

// Handle Save API Key
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_api_key') {
    $apiKey  = trim($_POST['api_key'] ?? '');
    $apiUrl  = trim($_POST['api_url'] ?? '');
    $aiModel = trim($_POST['ai_model'] ?? 'coding');

    $stmt = $pdo->query("SELECT id FROM settings WHERE id = 1");
    $exists = $stmt->fetch();

    if ($exists) {
        $stmt = $pdo->prepare("UPDATE settings SET ai_api_key = :api_key, ai_api_url = :api_url, ai_model = :ai_model, updated_at = datetime('now','localtime') WHERE id = 1");
    } else {
        $stmt = $pdo->prepare("INSERT INTO settings (id, ai_api_key, ai_api_url, ai_model) VALUES (1, :api_key, :api_url, :ai_model)");
    }

    $stmt->execute([
        'api_key'  => $apiKey,
        'api_url'  => $apiUrl,
        'ai_model' => $aiModel,
    ]);

    header("Location: /");
    exit;
}

// Fetch current setting
$stmt = $pdo->query("SELECT ai_api_key, ai_api_url, ai_model FROM settings WHERE id = 1");
$settings = $stmt->fetch();
$hasApiKey = !empty($settings->ai_api_key) && !empty($settings->ai_api_url);

$appName = 'SLD Test';
$appDesc = 'Pemetaan & Analisis Gaya Belajar Siswa';
$title   = 'Beranda';

ob_start();
?>

<!-- Tombol Pengaturan API -->
<div class="flex justify-end mb-4 mt-2 no-print">
    <button onclick="openSettingsModal()" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg shadow-sm transition-all">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span>Pengaturan API 9Router</span>
    </button>
</div>

<?php if (!$hasApiKey): ?>
    <!-- State Belum Ada API Key -->
    <div class="glass-card rounded-2xl p-8 sm:p-12 text-center mt-10">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-2xl shadow-sm mb-6">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mb-3">Konfigurasi API Belum Lengkap</h1>
        <p class="text-slate-500 text-sm sm:text-base max-w-md mx-auto mb-6">
            Sistem membutuhkan 9Router API Key &amp; URL untuk melakukan analisis gaya belajar. Silakan masukkan konfigurasi Anda terlebih dahulu.
        </p>
        <button onclick="openSettingsModal()" class="px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg transition-all">
            Masukkan Konfigurasi Sekarang
        </button>
    </div>
<?php else: ?>
    <!-- STEP 1: INITIAL FORM SECTION -->
    <section id="sectionForm" class="glass-card rounded-2xl p-6 sm:p-8 transition-all">
        <div class="border-b border-slate-200/80 pb-4 mb-6">
            <h2 class="text-2xl font-extrabold text-slate-800">Formulir Identitas Siswa</h2>
            <p class="text-slate-600 text-xs sm:text-sm mt-1">Lengkapi data pribadi dan pilih jenjang pendidikan untuk menampilkan instrumen kuesioner yang relevan.</p>
        </div>

        <form id="studentForm" onsubmit="handleStartQuestionnaire(event)" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap Siswa *</label>
                    <input type="text" id="inputNama" required placeholder="Contoh: Rio Andrianto" class="w-full px-3.5 py-2.5 text-sm bg-white/80 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">NISN *</label>
                    <input type="text" id="inputNISN" required placeholder="Contoh: 0081234567" class="w-full px-3.5 py-2.5 text-sm bg-white/80 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:bg-white focus:outline-none transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kelas *</label>
                    <input type="text" id="inputKelas" required placeholder="Contoh: IX A / XI PPLG 1" class="w-full px-3.5 py-2.5 text-sm bg-white/80 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Sekolah *</label>
                    <input type="text" id="inputSekolah" required placeholder="Skye Digipreneur" class="w-full px-3.5 py-2.5 text-sm bg-white/80 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:bg-white focus:outline-none transition">
                </div>
            </div>

            <div class="pt-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenjang Pendidikan *</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex items-center p-4 rounded-xl border border-slate-300 bg-white/90 cursor-pointer hover:border-brand-500 transition shadow-sm group">
                        <input type="radio" name="jenjang" value="SMP" checked onchange="toggleJurusanField()" class="text-brand-600 focus:ring-brand-500 w-4 h-4">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-slate-800 group-hover:text-brand-600">SMP / Mts</span>
                            <span class="block text-xs text-slate-500">Kuesioner Konseptual &amp; Dasar</span>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 rounded-xl border border-slate-300 bg-white/90 cursor-pointer hover:border-brand-500 transition shadow-sm group">
                        <input type="radio" name="jenjang" value="SMK" onchange="toggleJurusanField()" class="text-brand-600 focus:ring-brand-500 w-4 h-4">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-slate-800 group-hover:text-brand-600">SMK / MAK</span>
                            <span class="block text-xs text-slate-500">Kuesioner Vokasi &amp; Praktik Lab</span>
                        </div>
                    </label>
                </div>
            </div>

            <div id="jurusanContainer" class="hidden transition-all duration-300 pt-1">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jurusan / Kompetensi Keahlian (Khusus SMK) *</label>
                <input type="text" id="inputJurusan" placeholder="Contoh: PPLG, Bisnis Retail, Teknik Otomotif" class="w-full px-3.5 py-2.5 text-sm bg-white/80 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:bg-white focus:outline-none transition">
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-brand-600 to-amber-500 hover:from-brand-700 hover:to-amber-600 text-white font-bold text-sm rounded-xl shadow-lg hover:shadow-xl transition flex items-center justify-center gap-2">
                    <span>Mulai Kuesioner Adaptif</span>
                </button>
            </div>
        </form>
    </section>

    <!-- STEP 2: QUESTIONNAIRE SECTION -->
    <section id="sectionQuestionnaire" class="glass-card rounded-2xl p-6 sm:p-8 hidden transition-all">
        <div class="border-b border-slate-200/80 pb-4 mb-6">
            <div class="flex items-center justify-between mb-2">
                <span id="badgeJenjang" class="px-3 py-1 bg-brand-100 text-brand-700 font-extrabold text-xs rounded-full border border-brand-200 uppercase tracking-wider">
                    Kuesioner
                </span>
                <span id="textProgress" class="text-xs font-bold text-slate-500">Pertanyaan 1 dari X</span>
            </div>

            <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden">
                <div id="progressBar" class="bg-gradient-to-r from-brand-500 to-amber-500 h-2.5 rounded-full transition-all duration-300" style="width: 0%;"></div>
            </div>
        </div>

        <form id="quizForm" onsubmit="handleQuestionnaireSubmit(event)">
            <div id="questionsContainer" class="space-y-6"></div>

            <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-between flex-wrap gap-4">
                <button type="button" onclick="backToForm()" class="px-5 py-2.5 text-xs font-bold text-slate-600 bg-white hover:bg-slate-100 border border-slate-300 rounded-xl transition flex items-center gap-1.5">
                    Kembalikan Data Siswa
                </button>

                <button type="submit" id="btnAnalyze" class="px-8 py-3.5 bg-gradient-to-r from-brand-600 to-amber-500 hover:from-brand-700 hover:to-amber-600 text-white font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
                    Analisis AI Gaya Belajar
                </button>
            </div>
        </form>
    </section>

    <!-- STEP 3: LOADING -->
    <section id="sectionLoading" class="glass-card rounded-2xl p-10 hidden flex-col items-center justify-center text-center my-12 transition-all">
        <div class="w-12 h-12 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin mb-4"></div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Memproses Pemetaan AI...</h3>
        <p class="text-slate-600 text-sm max-w-md font-medium">Sedang menghubungi 9Router API dan memproses gaya belajar VAK...</p>
    </section>
<?php endif; ?>

<!-- Gatekeeper Quiz Modal -->
<div id="quizModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4 no-print">
    <div class="glass-card bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
        <button onclick="closeQuizModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">✕</button>
        <div class="flex items-center gap-2.5 mb-3">
            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                <i data-lucide="terminal" class="w-4 h-4"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">Verifikasi Developer</h3>
        </div>
        <p class="text-xs text-slate-500 mb-4">Jawab pertanyaan JavaScript berikut untuk membuka akses konfigurasi API:</p>

        <div class="bg-slate-900 text-emerald-400 font-mono text-xs p-3.5 rounded-xl mb-3 shadow-inner">
            <span class="text-slate-500">// Apa output console dari kode berikut?</span><br>
            <span class="text-sky-300">console</span>.<span class="text-yellow-300">log</span>(<span class="text-pink-400">typeof</span> <span class="text-orange-300">null</span>);
        </div>

        <form onsubmit="handleQuizSubmit(event)" class="space-y-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Output / Return Value *</label>
                <input type="text" id="inputQuizAnswer" required autocomplete="off" placeholder="contoh: string" class="w-full px-3 py-2 text-xs font-mono bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                <p id="quizError" class="text-red-500 text-[11px] mt-1 hidden font-medium">Jawaban salah. Coba lagi!</p>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" onclick="closeQuizModal()" class="px-3.5 py-1.5 text-xs text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-1.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow">Verifikasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Settings Modal -->
<div id="settingsModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4 no-print">
    <div class="glass-card bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
        <button onclick="closeSettingsModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">✕</button>
        <h3 class="text-lg font-bold text-slate-800 mb-4">Pengaturan API</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_api_key">
            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">9Router API URL *</label>
                    <input type="text" name="api_url" id="settingsApiUrl" value="<?= htmlspecialchars($settings->ai_api_url ?? '') ?>" required placeholder="Contoh: https://api.9router.com" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">9Router API Key *</label>
                    <input type="password" name="api_key" id="settingsApiKey" value="<?= htmlspecialchars($settings->ai_api_key ?? '') ?>" required placeholder="Masukkan NINEROUTER_KEY" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-semibold text-slate-700">AI Model *</label>
                        <button type="button" id="btnReloadModels" onclick="fetchModels(true)" class="text-[11px] text-brand-600 hover:underline flex items-center gap-1 font-medium">
                            <i data-lucide="refresh-cw" class="w-3 h-3"></i> Muat Ulang Model
                        </button>
                    </div>
                    <select name="ai_model" id="aiModelSelect" required class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-white">
                        <option value="<?= htmlspecialchars($settings->ai_model ?? 'coding') ?>"><?= htmlspecialchars($settings->ai_model ?? 'coding') ?></option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeSettingsModal()" class="px-4 py-2 bg-slate-100 rounded-lg text-slate-700 hover:bg-slate-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 font-bold">Simpan Konfigurasi</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();

$scripts = '<script>' . "\n";
$scripts .= <<<'HTML'
    const QUIZ_PASSED_KEY = 'edustyle_quiz_passed';

    function openSettingsModal() {
        if (localStorage.getItem(QUIZ_PASSED_KEY) === 'true') {
            $('#settingsModal').removeClass('hidden');
            fetchModels();
        } else {
            $('#inputQuizAnswer').val('');
            $('#quizError').addClass('hidden');
            $('#quizModal').removeClass('hidden');
            setTimeout(() => $('#inputQuizAnswer').focus(), 100);
        }
    }

    function closeQuizModal() {
        $('#quizModal').addClass('hidden');
    }

    function handleQuizSubmit(e) {
        e.preventDefault();
        const ans = ($('#inputQuizAnswer').val() || '').trim().toLowerCase().replace(/['"`]/g, '');
        // Jawaban typeof null di JS adalah 'object'
        if (ans === 'object') {
            localStorage.setItem(QUIZ_PASSED_KEY, 'true');
            $('#quizModal').addClass('hidden');
            $('#settingsModal').removeClass('hidden');
            fetchModels();
        } else {
            $('#quizError').removeClass('hidden');
            $('#inputQuizAnswer').focus().select();
        }
    }

    function closeSettingsModal() {
        $('#settingsModal').addClass('hidden');
    }

    async function fetchModels(isManual = false) {
        const select = $('#aiModelSelect');
        const current = select.val();
        const btn = $('#btnReloadModels');

        if (isManual) {
            btn.addClass('opacity-50 pointer-events-none');
            select.html('<option value="">Memuat model dari API...</option>');
        }

        try {
            const resp = await fetch('/api/models');
            const data = await resp.json();
            if (data.error) throw new Error(data.error);

            const models = data.data || [];
            if (models.length > 0) {
                select.empty();
                models.forEach(m => {
                    const id = m.id || m.name;
                    select.append(`<option value="${id}">${id}</option>`);
                });
                if (current && models.some(m => (m.id || m.name) === current)) {
                    select.val(current);
                }
            }
        } catch (e) {
            console.warn('Gagal memuat model:', e.message);
            if (isManual) {
                alert('Gagal mengambil daftar model: ' + e.message);
            }
            if (!select.val()) {
                select.html('<option value="coding">coding</option>');
            }
        } finally {
            btn.removeClass('opacity-50 pointer-events-none');
            lucide.createIcons();
        }
    }

    $(document).ready(function() {
        lucide.createIcons();
        var hasApiKey = "HASAPIKEY_PLACEHOLDER";
        if (hasApiKey === 'no') {
            openSettingsModal();
        }
    });

    const QUESTIONNAIRE_SMP = [
        {
            id: 1,
            question: "Saat guru menjelaskan materi pelajaran baru di depan kelas (misalnya rumus Matematika atau IPS), apa yang membuatmu paling mudah paham?",
            options: [
                { label: "A. Melihat guru menulis di papan tulis, melihat slide presentasi, atau membaca buku paket.", type: "A" },
                { label: "B. Mendengarkan penjelasan guru dengan saksama atau mendengarkan rekaman penjelasan.", type: "B" },
                { label: "C. Mencoba menuliskan kembali rumusnya sendiri atau langsung mengerjakan latihan soal.", type: "C" }
            ]
        },
        {
            id: 2,
            question: "Ketika kamu harus menghafal kosakata baru bahasa Inggris atau istilah Biologi, apa yang biasanya kamu lakukan?",
            options: [
                { label: "A. Menulisnya berulang kali di buku catatan atau menandainya dengan spidol warna-warni.", type: "A" },
                { label: "B. Mengucapkannya keras-keras berulang kali atau membuat singkatan/lagu yang mudah diingat.", type: "B" },
                { label: "C. Menulisnya di kartu kecil lalu membolak-balik kartu tersebut sambil berjalan-jalan.", type: "C" }
            ]
        },
        {
            id: 3,
            question: "Saat kerja kelompok membuat mading atau tugas prakarya, peran apa yang paling kamu sukai?",
            options: [
                { label: "A. Mendesain tata letak, memilih warna, menggambar, atau menghias mading agar terlihat menarik.", type: "A" },
                { label: "B. Memimpin diskusi, membagi tugas secara lisan, atau mempresentasikan hasil kerja kelompok di depan kelas.", type: "B" },
                { label: "C. Menggunting kertas, menempelkan bahan, memotong kayu/karton, atau merakit bahan-bahan mading.", type: "C" }
            ]
        },
        {
            id: 4,
            question: "Apa yang paling mengganggu konsentrasimu saat sedang mengerjakan ujian di kelas?",
            options: [
                { label: "A. Kondisi kelas yang berantakan atau melihat teman-teman di sekitar yang bergerak gelisah.", type: "A" },
                { label: "B. Suara bisikan teman, ketukan bolpoin di meja, atau suara bising dari luar kelas.", type: "B" },
                { label: "C. Harus duduk diam di kursi dalam waktu yang sangat lama tanpa boleh berdiri.", type: "C" }
            ]
        },
        {
            id: 5,
            question: "Ketika kamu menceritakan liburan sekolah kepada temanmu, bagaimana caramu menyampaikannya?",
            options: [
                { label: "A. Menggambarkan pemandangan, tempat-tempat indah, atau foto-foto yang kamu ambil.", type: "A" },
                { label: "B. Menceritakan dengan detail percakapan seru atau suara-suara menarik yang kamu dengar.", type: "B" },
                { label: "C. Menggunakan banyak gerakan tangan (gestur) dan menceritakan aktivitas fisik yang kamu lakukan.", type: "C" }
            ]
        },
        {
            id: 6,
            question: "Saat kamu sedang menghafal pelajaran di kamar, posisi tubuh seperti apa yang paling sering kamu lakukan?",
            options: [
                { label: "A. Duduk rapi di meja belajar sambil menatap buku atau catatan secara fokus.", type: "A" },
                { label: "B. Duduk santai atau rebahan sambil membaca teks dengan suara pelan/bergumam.", type: "B" },
                { label: "C. Berjalan bolak-balik di dalam kamar sambil memegang buku atau mencoret-coret kertas coretan.", type: "C" }
            ]
        },
        {
            id: 7,
            question: "Saat kamu merasa jenuh di tengah-tengah jam pelajaran, apa yang biasanya secara tidak sadar kamu lakukan?",
            options: [
                { label: "A. Menggambar coretan di pojok kertas, mewarnai huruf, atau melihat pemandangan ke luar jendela.", type: "A" },
                { label: "B. Mengajak teman sebangku mengobrol berbisik atau memainkan ketukan jari berirama ke meja.", type: "B" },
                { label: "C. Memainkan pulpen (memutarnya), menggoyang-goyangkan kaki, atau izin ke kamar mandi agar bisa berjalan tegak.", type: "C" }
            ]
        },
        {
            id: 8,
            question: "Ketika kamu membeli buku cerita, komik, atau majalah baru, apa yang pertama kali menarik perhatianmu?",
            options: [
                { label: "A. Gambar sampulnya, warna-warni ilustrasi di dalam buku, atau kerapian layout-nya.", type: "A" },
                { label: "B. Membaca judul/sinopsis di bagian belakang keras-keras atau mendengarkan rekomendasi lisan dari teman.", type: "B" },
                { label: "C. Membolak-balik halamannya dengan cepat untuk merasakan tekstur kertasnya atau langsung membuka lembar tengah.", type: "C" }
            ]
        },
        {
            id: 9,
            question: "Jenis permainan (game) atau aplikasi di ponsel apa yang paling kamu sukai saat ini?",
            options: [
                { label: "A. Game strategi/teka-teki visual atau membaca komik digital (Webtoon).", type: "A" },
                { label: "B. Game yang membutuhkan komunikasi suara dengan tim (voice chat) atau mendengarkan musik/podcast.", type: "B" },
                { label: "C. Game aksi yang membutuhkan kecepatan tangan, refleks jari yang aktif, atau aplikasi edit video/dance.", type: "C" }
            ]
        },
        {
            id: 10,
            question: "Bagaimana caramu mengingat guru baru yang mengajar di kelasmu?",
            options: [
                { label: "A. Mengingat wajahnya, gaya berpakaiannya, atau warna baju/kacamata yang digunakannya.", type: "A" },
                { label: "B. Mengingat nada suaranya saat berbicara, logat bicaranya, atau kata-kata khas yang sering diucapkannya.", type: "B" },
                { label: "C. Mengingat ekspresi energinya saat bergerak di depan kelas atau caranya menyapa siswa dengan fisik.", type: "C" }
            ]
        },
        {
            id: 11,
            question: "Jika kamu tersesat di dalam gedung sekolah yang baru dan besar, bagaimana cara kamu mencari jalan keluar?",
            options: [
                { label: "A. Mencari papan denah gedung, petunjuk arah tertulis, atau menatap peta lokasi di dinding.", type: "A" },
                { label: "B. Bertanya langsung kepada guru, satpam, atau siswa lain yang kebetulan lewat di sana.", type: "B" },
                { label: "C. Berjalan terus menyusuri lorong sambil mencoba berbelok-belok sendiri sampai menemukan pintu keluar.", type: "C" }
            ]
        },
        {
            id: 12,
            question: "Saat kamu belajar menghadapi ujian besok pagi, teknik mana yang paling membantumu?",
            options: [
                { label: "A. Membaca rangkuman ringkas yang penuh dengan peta konsep (mind map) atau tabel materi.", type: "A" },
                { label: "B. Meminta orang tua atau teman memberikan pertanyaan lisan, lalu kamu menjawabnya dengan berbicara.", type: "B" },
                { label: "C. Menulis ulang inti pelajaran di kertas buram berkali-kali sampai tanganmu terbiasa mengingatnya.", type: "C" }
            ]
        }
    ];

    const QUESTIONNAIRE_SMK = [
        {
            id: 1,
            question: "Saat pertama kali diperkenalkan dengan alat, mesin, atau aplikasi baru di jurusanmu, apa tindakan pertamanya?",
            options: [
                { label: "A. Membaca buku manual, melihat diagram alur, atau menonton video tutorial langkah demi langkah.", type: "A" },
                { label: "B. Mendengarkan instruksi dan penjelasan lisan dari guru atau instruktur tentang cara kerjanya.", type: "B" },
                { label: "C. Langsung memegang alatnya, menekan tombol, dan mencoba mengoperasikannya sendiri secara langsung.", type: "C" }
            ]
        },
        {
            id: 2,
            question: "Ketika kamu sedang melakukan praktik di bengkel, laboratorium, atau studio, bagaimana caramu mengingat Standar Operasional Prosedur (SOP)?",
            options: [
                { label: "A. Membayangkan poster SOP atau urutan gambar alur kerja yang tertempel di dinding ruangan praktik.", type: "A" },
                { label: "B. Mengingat kembali kata-kata, aba-aba, atau peringatan lisan yang disampaikan oleh instruktur.", type: "B" },
                { label: "C. Mengandalkan ingatan otot (muscle memory) setelah berulang kali mempraktikkan gerakan kerja tersebut.", type: "C" }
            ]
        },
        {
            id: 3,
            question: "Saat kamu menghadapi masalah teknik atau error saat praktik (misal: mesin macet, kode program error, masakan gosong, kamera mati), apa yang kamu lakukan?",
            options: [
                { label: "A. Memeriksa kembali skema diagram, modul tertulis, atau mencari panduan visual di internet.", type: "A" },
                { label: "B. Bertanya langsung kepada teman ahli, guru, atau mendiskusikan solusinya bersama tim secara lisan.", type: "B" },
                { label: "C. Membongkar kembali komponennya, mencoba-coba tombol lain, atau mengotak-atik fisiknya sampai berhasil.", type: "C" }
            ]
        },
        {
            id: 4,
            question: "Model presentasi proyek akhir kelompok seperti apa yang menurutmu paling mudah dipahami audiens?",
            options: [
                { label: "A. Presentasi yang menggunakan infografis tajam, grafik data yang jelas, dan slide animasi yang menarik.", type: "A" },
                { label: "B. Presentasi berupa sesi tanya jawab yang aktif, debat interaktif, atau penjelasan lisan yang mengalir runtut.", type: "B" },
                { label: "C. Presentasi yang disertai simulasi langsung, demonstrasi alat, atau menunjukkan produk fisik hasil kerja ke depan.", type: "C" }
            ]
        },
        {
            id: 5,
            question: "Di lingkungan tempat magang (PKL) nanti, instruktur seperti apa yang paling kamu harapkan?",
            options: [
                { label: "A. Instruktur yang memberikan tugas tertulis dengan arahan berupa modul atau gambar instruksi yang jelas.", type: "A" },
                { label: "B. Instruktur yang sering mengajak berdiskusi, memberikan evaluasi lisan, dan menjelaskan lewat penjelasan suara.", type: "B" },
                { label: "C. Instruktur yang memberikan contoh tindakan secara langsung di depan mata dan membiarkanmu langsung ikut mencoba.", type: "C" }
            ]
        },
        {
            id: 6,
            question: "Ketika kamu harus mempelajari modul software baru untuk kebutuhan jurusan, metode apa yang paling efektif bagimu?",
            options: [
                { label: "A. Mengikuti panduan berbentuk tangkapan layar berpanah atau infografis skema alur.", type: "A" },
                { label: "B. Menonton video penjelasan yang memiliki narator yang menerangkan fungsi menu dengan suara jelas.", type: "B" },
                { label: "C. Membuka software-nya secara langsung lalu mencoba mengklik menu-menunya satu per satu sampai paham fungsinya.", type: "C" }
            ]
        },
        {
            id: 7,
            question: "Jika kamu bekerja dalam tim proyek tugas akhir jurusan, bagaimana caramu berkoordinasi yang paling nyaman?",
            options: [
                { label: "A. Menggunakan grup chat dengan instruksi tertulis dan memantau kemajuan lewat bagan pembagian tugas visual.", type: "A" },
                { label: "B. Mengadakan rapat lisan (baik langsung maupun via voice call) untuk berdiskusi bertukar ide bersama.", type: "B" },
                { label: "C. Bertemu langsung di tempat praktik untuk langsung membagi bahan kerja fisik dan merakit bersama-sama.", type: "C" }
            ]
        },
        {
            id: 8,
            question: "Saat kamu membaca sebuah artikel berita atau tren industri terbaru di jurusanmu, apa yang paling menarik perhatianmu?",
            options: [
                { label: "A. Tabel data perkembangan terbaru, grafik persentase, atau foto-foto produk inovasi baru.", type: "A" },
                { label: "B. Argumen wawancara tokoh industrinya, podcast analisis bisnis, atau diskusi kritis di kolom komentar.", type: "B" },
                { label: "C. Spesifikasi fisik komponen teknis, cara perakitan, atau demonstrasi pengujian ketahanan produknya.", type: "C" }
            ]
        },
        {
            id: 9,
            question: "Bayangkan kamu sedang magang (PKL) dan diberi tahu bahwa hasil pekerjaanmu salah. Bagaimana cara kamu ingin koreksi itu disampaikan?",
            options: [
                { label: "A. Diberikan catatan tertulis berisi coretan bagian mana yang salah beserta contoh gambar/teks yang benarnya.", type: "A" },
                { label: "B. Dipanggil langsung untuk mendengarkan penjelasan lisan dari pembimbing industri tentang letak kekeliruannya.", type: "B" },
                { label: "C. Ditunjukkan langsung kesalahannya pada objek kerja fisik, lalu dibimbing secara motorik untuk membetulkannya saat itu juga.", type: "C" }
            ]
        },
        {
            id: 10,
            question: "Ketika kamu merasa sangat lelah secara mental setelah seharian praktik berat, apa cara terbaikmu untuk pulih?",
            options: [
                { label: "A. Menonton film/series dengan visual yang bagus atau sekadar bersantai di tempat yang rapi dan bersih mata.", type: "A" },
                { label: "B. Mendengarkan musik favorit menggunakan earphone atau mengobrol santai melepas penat bersama teman dekat.", type: "B" },
                { label: "C. Melakukan aktivitas fisik ringan seperti berolahraga, berjalan kaki menghirup udara segar, atau membersihkan peralatan kerja.", type: "C" }
            ]
        },
        {
            id: 11,
            question: "Saat mengikuti seminar atau workshop industri, apa yang membuatmu tetap fokus menyimak sepanjang acara?",
            options: [
                { label: "A. Slide materi dari pembicara yang penuh dengan warna, video ilustrasi menarik, dan minim teks membosankan.", type: "A" },
                { label: "B. Retorika pembicara yang interaktif, penuh lelucon suara, cerita yang seru, dan mengajak audiens menjawab lisan.", type: "B" },
                { label: "C. Sesi ice breaking yang melibatkan gerakan fisik atau kesempatan live demo maju ke atas panggung untuk mencoba.", type: "C" }
            ]
        },
        {
            id: 12,
            question: "Mengapa kamu merasa yakin memilih jurusan SMK-mu yang sekarang ini?",
            options: [
                { label: "A. Karena setelah melihat prospek kerjanya di internet atau melihat lingkungan kerjanya, tampilannya terlihat keren dan teratur.", type: "A" },
                { label: "B. Karena sering mendengar cerita sukses dari alumni, saran orang tua, atau hasil diskusi dengan guru BK.", type: "B" },
                { label: "C. Karena kamu suka memegang material fisiknya, suka mengutak-atik sistemnya, atau suka bergerak aktif membuat sesuatu produk.", type: "C" }
            ]
        }
    ];

    let currentStudent = {};
    let currentActiveQuestions = [];

    function toggleJurusanField() {
        const jenjang = document.querySelector('input[name="jenjang"]:checked').value;
        if (jenjang === 'SMK') {
            $('#jurusanContainer').removeClass('hidden');
            $('#inputJurusan').prop('required', true);
        } else {
            $('#jurusanContainer').addClass('hidden');
            $('#inputJurusan').prop('required', false).val('');
        }
    }

    function handleStartQuestionnaire(e) {
        e.preventDefault();
        currentStudent = {
            nama: $('#inputNama').val().trim(),
            nisn: $('#inputNISN').val().trim(),
            kelas: $('#inputKelas').val().trim(),
            sekolah: $('#inputSekolah').val().trim(),
            jenjang: $('input[name="jenjang"]:checked').val(),
            jurusan: $('#inputJurusan').val().trim()
        };

        currentActiveQuestions = currentStudent.jenjang === 'SMK' ? QUESTIONNAIRE_SMK : QUESTIONNAIRE_SMP;
        renderQuestions();
        $('#sectionForm').addClass('hidden');
        $('#sectionQuestionnaire').removeClass('hidden');
        $('#badgeJenjang').text('Kuesioner ' + currentStudent.jenjang);
    }

    function renderQuestions() {
        const container = $('#questionsContainer');
        container.empty();

        currentActiveQuestions.forEach((q, index) => {
            let optionsHtml = q.options.map(opt => `
                <label class="flex items-start p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-orange-50/60 hover:border-brand-400 cursor-pointer transition text-xs sm:text-sm text-slate-700 font-medium group">
                    <input type="radio" name="q_${q.id}" value="${opt.type}" required class="mt-0.5 text-brand-600 focus:ring-brand-500 w-4 h-4 flex-shrink-0" onchange="updateProgress()">
                    <span class="ml-3 group-hover:text-slate-900 leading-relaxed">${opt.label}</span>
                </label>
            `).join('');

            container.append(`
                <div class="p-5 rounded-2xl bg-white/90 border border-slate-200 shadow-sm space-y-3">
                    <div class="flex items-start gap-2">
                        <span class="px-2.5 py-1 bg-slate-800 text-white font-extrabold text-xs rounded-lg flex-shrink-0">${index + 1}</span>
                        <h4 class="text-sm sm:text-base font-bold text-slate-800 leading-snug">${q.question}</h4>
                    </div>
                    <div class="space-y-2 pt-1">
                        ${optionsHtml}
                    </div>
                </div>
            `);
        });
        updateProgress();
        lucide.createIcons();
    }

    function updateProgress() {
        let answered = 0;
        currentActiveQuestions.forEach(q => {
            if ($(`input[name="q_${q.id}"]:checked`).length) answered++;
        });
        const pct = Math.round((answered / currentActiveQuestions.length) * 100) || 0;
        $('#progressBar').css('width', pct + '%');
        $('#textProgress').text(`Terjawab ${answered} dari ${currentActiveQuestions.length} Soal`);
    }

    function backToForm() {
        $('#sectionQuestionnaire').addClass('hidden');
        $('#sectionForm').removeClass('hidden');
    }

    async function handleQuestionnaireSubmit(e) {
        e.preventDefault();

        let scoreA = 0, scoreB = 0, scoreC = 0;
        let answers = [];
        currentActiveQuestions.forEach(q => {
            const el = $(`input[name="q_${q.id}"]:checked`);
            const val = el.val();
            if (val === 'A') scoreA++;
            if (val === 'B') scoreB++;
            if (val === 'C') scoreC++;

            const label = el.closest('label').find('span').text().trim();
            answers.push({ id: q.id, answer: label, type: val });
        });

        const total = currentActiveQuestions.length;
        const pctA = Math.round((scoreA / total) * 100);
        const pctB = Math.round((scoreB / total) * 100);
        const pctC = Math.round((scoreC / total) * 100);

        $('#sectionQuestionnaire').addClass('hidden');
        $('#sectionLoading').removeClass('hidden').css('display', 'flex');

        try {
            const response = await fetch('/api/analyze', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    student: currentStudent,
                    scores: { visual: scoreA, auditori: scoreB, kinestetik: scoreC },
                    percentages: { visual: pctA, auditori: pctB, kinestetik: pctC },
                    answers: answers
                })
            });

            if (!response.ok) throw new Error('API Error');
            const data = await response.json();

            if (data.error) throw new Error(data.error);
            window.location.href = '/analisis?id=' + data.id;

        } catch (err) {
            console.error(err);
            alert("Terjadi kesalahan saat memproses data ke AI: " + err.message);
            $('#sectionLoading').addClass('hidden').css('display', '');
            $('#sectionQuestionnaire').removeClass('hidden');
        }
    }
</script>
HTML;

$scripts = str_replace('HASAPIKEY_PLACEHOLDER', ($hasApiKey ? 'yes' : 'no'), $scripts);

include __DIR__ . '/../components/template.php';

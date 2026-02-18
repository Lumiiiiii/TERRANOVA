<?php
/**
 * Homepage - Dashboard principale (Tailwind + Bento Grid Redesign)
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Patient.php';

$patientManager = new Patient();
$recentPatients = $patientManager->getRecentPatients(10);
$totalPatients = $patientManager->countPatients();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TerraNova - Gestionale Naturopatia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2d8659',
                        secondary: '#f4a261',
                        accent: '#e76f51',
                        surface: '#ffffff',
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen text-gray-800">

    <!-- Top Navigation (Glassmorphic) -->
    <nav class="fixed tops-0 w-full z-50 glass px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-green-400 flex items-center justify-center text-white font-bold">
                    TN</div>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight">TerraNova</h1>
            </div>
            <div class="hidden md:flex gap-8 text-sm font-medium text-gray-600">
                <a href="index.php" class="text-primary font-semibold">Dashboard</a>
                <a href="paziente_nuovo.php" class="hover:text-primary transition-colors">Nuovo Paziente</a>
                <a href="medicinali_gestione.php" class="hover:text-primary transition-colors">Medicinali</a>
            </div>
            <!-- Mobile Menu Button (Placeholder) -->
            <button class="md:hidden text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-12">

        <!-- Welcome Section -->
        <header class="mb-10 animate-fade-in">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Benvenuta, Naturopata.</h2>
            <p class="text-gray-500">Ecco una panoramica della tua attività oggi.</p>
        </header>

        <!-- Bento Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 auto-rows-[minmax(180px,auto)]">

            <!-- 1. Stats Card (Large) - Redesigned (Vibrant Gradient) -->
            <div
                class="md:col-span-2 relative overflow-hidden rounded-2xl p-8 group animate-fade-in shadow-lg transition-transform hover:-translate-y-1">
                <!-- Background Gradient -->
                <div class="absolute inset-0 bg-gradient-to-br from-primary to-emerald-400 opacity-90"></div>

                <!-- Decorative Circles -->
                <div
                    class="absolute top-0 right-0 w-40 h-40 bg-white/20 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-32 h-32 bg-yellow-300/20 rounded-full blur-2xl -ml-10 -mb-10 pointer-events-none">
                </div>

                <!-- Content -->
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-white/90 text-sm font-bold uppercase tracking-wider mb-1">Totale Pazienti
                            </h3>
                            <div class="flex items-baseline gap-2">
                                <span
                                    class="text-5xl font-extrabold text-white tracking-tight"><?= $totalPatients ?></span>
                                <span class="text-green-100 text-sm font-medium">assistiti</span>
                            </div>
                        </div>
                        <div
                            class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-inner hidden sm:flex">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-2">
                        <div
                            class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-white text-xs font-semibold flex items-center gap-1.5 border border-white/10">
                            <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse"></span>
                            Database attivo
                        </div>
                        <a href="#"
                            class="px-3 py-1 bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-full text-white text-xs font-medium transition-colors border border-white/10 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Vedi report <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Action Card (Primary) -->
            <a href="paziente_nuovo.php"
                class="md:col-span-1 bg-gradient-to-br from-primary to-green-600 text-white rounded-2xl p-6 flex flex-col justify-between hover:shadow-xl hover:shadow-green-500/20 transition-all transform hover:-translate-y-1 animate-fade-in delay-100">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold">Nuovo Paziente</h3>
                    <p class="text-green-100 text-sm mt-1">Registra una nuova scheda</p>
                </div>
            </a>

            <!-- 3. Medicine Shortcut -->
            <a href="medicinali_gestione.php"
                class="md:col-span-1 glass rounded-2xl p-6 flex flex-col justify-between hover:border-secondary transition-colors group animate-fade-in delay-200">
                <div
                    class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-secondary group-hover:text-white transition-colors">
                    <svg class="w-6 h-6 text-secondary group-hover:text-white transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Medicinali</h3>
                    <p class="text-gray-500 text-sm mt-1">Gestisci archivio</p>
                </div>
            </a>

            <!-- 4. Search Bar (Full Width) -->
            <div class="md:col-span-4 glass rounded-2xl p-6 animate-fade-in delay-200">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="search-input"
                        class="block w-full pl-12 pr-4 py-4 bg-white/50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent transition-all"
                        placeholder="Cerca paziente per nome, email o telefono..." autocomplete="off">
                </div>
            </div>

            <!-- 5. Recent Patients List (Vertical, Taller) -->
            <div class="md:col-span-4 glass rounded-2xl p-0 overflow-hidden animate-fade-in delay-300">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white/30">
                    <h3 class="font-bold text-lg text-gray-800">Pazienti Recenti</h3>
                    <a href="#" class="text-sm text-primary font-medium hover:underline">Vedi tutti</a>
                </div>
                <div id="patients-list" class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                    <!-- Dynamic Content -->
                    <?php if (empty($recentPatients)): ?>
                        <div class="p-10 text-center text-gray-500">
                            <p>Nessun paziente trovato. Inizia aggiungendone uno!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentPatients as $patient): ?>
                            <div class="p-4 hover:bg-white/60 transition-colors cursor-pointer flex justify-between items-center group"
                                onclick="window.location.href='paziente_dettaglio.php?id=<?= $patient['id'] ?>'">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                        <?= strtoupper(substr($patient['nome_cognome'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">
                                            <?= htmlspecialchars($patient['nome_cognome']) ?></h4>
                                        <p class="text-xs text-gray-500">
                                            <?= $patient['eta'] ? $patient['eta'] . ' anni' : '' ?>
                                            <?= $patient['telefono'] ? ' • ' . htmlspecialchars($patient['telefono']) : '' ?>
                                        </p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-primary transition-colors" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-input');
            searchInput.addEventListener('input', function (e) {
                // Use the existing logic from main.js but we might need to adapt the display function for Tailwind
                searchPatients(e.target.value);
            });
        });

        // We need to override the displayPatients function from main.js to use Tailwind classes
        // This is a bit of a hack, normally we would refactor main.js, but for this step we can override it here or update main.js next.
        // Let's update main.js in the next step to support the new UI.
    </script>
</body>

</html>
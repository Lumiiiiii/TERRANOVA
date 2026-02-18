<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
/**
 * Dettaglio Paziente - Refactored
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Patient.php';
require_once __DIR__ . '/includes/Visit.php';
require_once __DIR__ . '/includes/FoodRestrictions.php';
require_once __DIR__ . '/includes/Prescription.php';

$id = $_GET['id'] ?? 0;
$patientManager = new Patient();
$visitManager = new Visit();
$patient = $patientManager->getPatient($id);

if (!$patient) {
    header('Location: index.php');
    exit;
}

$visits = $visitManager->getVisitHistory($id);
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($patient['nome_cognome']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#2d8659', secondary: '#f4a261' } } } }
    </script>
</head>

<body class="bg-gray-50 min-h-screen text-gray-800">
    <nav class="sticky top-0 z-50 glass px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="index.php"
                    class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-green-400 flex items-center justify-center text-white font-bold">TN</a>
                <h1 class="text-sm font-bold"><?= htmlspecialchars($patient['nome_cognome']) ?></h1>
            </div>
            <a href="index.php" class="text-sm font-medium text-gray-600 hover:text-primary">Dashboard</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: Profile -->
            <div class="lg:col-span-4 space-y-6">
                <div class="glass-card p-6 text-center relative overflow-hidden">
                    <div
                        class="w-20 h-20 rounded-full bg-gray-100 mx-auto flex items-center justify-center text-2xl font-bold text-primary mb-4">
                        <?= strtoupper(substr($patient['nome_cognome'], 0, 1)) ?>
                    </div>
                    <h2 class="text-xl font-bold"><?= htmlspecialchars($patient['nome_cognome']) ?></h2>
                    <p class="text-gray-500 text-sm mb-4">
                        <?= htmlspecialchars($patient['professione'] ?? 'Professione non indicata') ?>
                    </p>

                    <a href="visita_anamnesi.php?paziente_id=<?= $id ?>"
                        class="inline-block bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                        + Nuova Visita
                    </a>

                    <div class="mt-6 text-left space-y-3 text-sm border-t pt-4">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Età</span>
                            <span class="font-medium"><?= $patient['eta'] ?? '-' ?> anni</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Telefono</span>
                            <span class="font-medium"><?= htmlspecialchars($patient['telefono'] ?? '-') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Email</span>
                            <span class="font-medium"><?= htmlspecialchars($patient['email'] ?? '-') ?></span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-xs uppercase mb-1">Indirizzo</span>
                            <span class="font-medium block"><?= htmlspecialchars($patient['indirizzo'] ?? '-') ?></span>
                        </div>
                    </div>
                    <button onclick="document.getElementById('edit-modal').classList.remove('hidden')"
                        class="mt-4 text-xs text-gray-400 hover:text-primary underline">Modifica Dati</button>
                </div>
            </div>

            <!-- Right: Visits -->
            <div class="lg:col-span-8 space-y-6">
                <!-- History -->
                <div class="glass-card p-6">
                    <h3 class="font-bold text-lg mb-6 border-b pb-2">Storico Visite</h3>
                    <?php if (empty($visits)): ?>
                        <p class="text-gray-500 text-center py-8">Nessuna visita registrata.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($visits as $v): ?>
                                <div
                                    class="bg-white/50 p-4 rounded-xl border border-gray-100 flex justify-between items-center hover:shadow-md transition-shadow">
                                    <div>
                                        <h4 class="font-bold text-gray-800">Visita del
                                            <?= date('d/m/Y', strtotime($v['data_visita'])) ?>
                                        </h4>
                                        <p class="text-xs text-gray-500 truncate max-w-md">
                                            <?= htmlspecialchars($v['note_finali'] ?? '') ?>
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="visita_anamnesi.php?visita_id=<?= $v['id'] ?>&paziente_id=<?= $id ?>"
                                            class="px-3 py-1 text-xs bg-primary text-white rounded hover:bg-green-700">Vedi /
                                            Modifica</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="font-bold text-lg mb-4">Modifica Paziente</h3>
            <form id="edit-form" class="space-y-4">
                <input type="hidden" name="action" value="update_patient">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="text" name="nome_cognome" placeholder="Nome"
                    value="<?= htmlspecialchars($patient['nome_cognome']) ?>" class="w-full border rounded p-2 text-sm">
                <input type="date" name="data_nascita" value="<?= $patient['data_nascita'] ?>"
                    class="w-full border rounded p-2 text-sm">
                <input type="tel" name="telefono" placeholder="Telefono"
                    value="<?= htmlspecialchars($patient['telefono'] ?? '') ?>"
                    class="w-full border rounded p-2 text-sm">
                <input type="email" name="email" placeholder="Email"
                    value="<?= htmlspecialchars($patient['email'] ?? '') ?>" class="w-full border rounded p-2 text-sm">
                <input type="text" name="professione" placeholder="Professione"
                    value="<?= htmlspecialchars($patient['professione'] ?? '') ?>"
                    class="w-full border rounded p-2 text-sm">
                <textarea name="indirizzo" placeholder="Indirizzo"
                    class="w-full border rounded p-2 text-sm"><?= htmlspecialchars($patient['indirizzo'] ?? '') ?></textarea>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-200 rounded text-sm">Annulla</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded text-sm">Salva</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('edit-form').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const res = await fetch('ajax_handlers.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) location.reload();
            } catch (e) { console.error(e); }
        });
    </script>
</body>

</html>
<?php
/**
 * Pagina dettagli paziente - Refactored with Tailwind CSS
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__. '/includes/Patient.php';
require_once __DIR__ . '/includes/Visit.php';
require_once __DIR__ . '/includes/FoodRestrictions.php';
require_once __DIR__ . '/includes/Prescription.php';

$patientId = $_GET['id'] ?? 0;

if (!$patientId) {
    header('Location: index.php');
    exit;
}

$patientManager = new Patient();
$visitManager = new Visit();
$foodManager = new FoodRestrictions();
$prescriptionManager = new Prescription();

$patient = $patientManager->getPatient($patientId);

if (!$patient) {
    header('Location: index.php');
    exit;
}

$visitHistory = $visitManager->getVisitHistory($patientId);
$foodRestrictions = $foodManager->getFoodRestrictionsByCategory($patientId);
$prescriptions = $prescriptionManager->getPrescriptionsByPatient($patientId, true); // Solo attive
$totalVisits = count($visitHistory);
$totalPrescriptions = count($prescriptions);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($patient['nome_cognome']) ?> - TerraNova</title>
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
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">

    <!-- Top Navigation -->
    <nav class="sticky top-0 z-50 glass px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="index.php" class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-green-400 flex items-center justify-center text-white font-bold">TN</a>
                <div class="flex flex-col">
                    <span class="text-xs text-gray-500 font-medium">Cartella Paziente</span>
                    <h1 class="text-sm font-bold text-gray-800 leading-none"><?= htmlspecialchars($patient['nome_cognome']) ?></h1>
                </div>
            </div>
            <div class="flex gap-4">
                 <a href="index.php" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Dashboard</a>
                 <a href="medicinali_gestione.php" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Medicinali</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Column: Patient Profile (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Profile Card -->
                <div class="glass-card animate-fade-in relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-br from-primary to-green-600 opacity-20"></div>
                    <div class="relative pt-6 px-4">
                        <div class="w-20 h-20 rounded-full bg-white border-4 border-white shadow-md flex items-center justify-center text-2xl font-bold text-primary mx-auto mb-4">
                             <?= strtoupper(substr($patient['nome_cognome'], 0, 1)) ?>
                        </div>
                        <h2 class="text-xl font-bold text-center text-gray-900"><?= htmlspecialchars($patient['nome_cognome']) ?></h2>
                        <p class="text-center text-sm text-gray-500 mb-6"><?= $patient['professione'] ?: 'Professione non specificata' ?></p>
                        
                        <!-- Actions -->
                        <div class="flex justify-center gap-2 mb-6">
                             <a href="visita_anamnesi.php?paziente_id=<?= $patientId ?>" class="bg-primary hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-lg shadow-green-500/30 transition-all transform hover:-translate-y-0.5">
                                + Nuova Visita
                            </a>
                            <button onclick="toggleEditMode()" class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                ✎ Modifica
                            </button>
                        </div>

                        <!-- Details List -->
                        <div id="patient-display" class="space-y-3 text-sm pb-4 border-t border-gray-100 pt-4">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Età</span>
                                <span class="font-medium text-gray-800"><?= $patient['eta'] ?? '-' ?> anni</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Sesso</span>
                                <span class="font-medium text-gray-800"><?= $patient['sesso'] ?? '-' ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Telefono</span>
                                <span class="font-medium text-gray-800"><?= htmlspecialchars($patient['telefono'] ?? '-') ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Email</span>
                                <span class="font-medium text-gray-800"><?= htmlspecialchars($patient['email'] ?? '-') ?></span>
                            </div>
                            <div class="pt-2">
                                <span class="text-gray-500 block text-xs uppercase mb-1">Indirizzo</span>
                                <span class="font-medium text-gray-800 block"><?= htmlspecialchars($patient['indirizzo'] ?? '-') ?></span>
                            </div>
                        </div>

                        <!-- Edit Form (Hidden) -->
                        <form id="patient-edit-form" class="hidden space-y-3 pb-4 border-t border-gray-100 pt-4">
                             <input type="hidden" name="id" value="<?= $patientId ?>">
                             <div>
                                <label class="text-xs font-medium text-gray-500">Nome e Cognome</label>
                                <input type="text" name="nome_cognome" class="w-full text-sm border-gray-200 rounded p-2 bg-gray-50" value="<?= htmlspecialchars($patient['nome_cognome']) ?>" required>
                             </div>
                             <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-xs font-medium text-gray-500">Data Nascita</label>
                                    <input type="date" name="data_nascita" class="w-full text-sm border-gray-200 rounded p-2 bg-gray-50" value="<?= $patient['data_nascita'] ?>">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-500">Sesso</label>
                                    <select name="sesso" class="w-full text-sm border-gray-200 rounded p-2 bg-gray-50">
                                        <option value="F" <?= ($patient['sesso'] == 'F') ? 'selected' : '' ?>>F</option>
                                        <option value="M" <?= ($patient['sesso'] == 'M') ? 'selected' : '' ?>>M</option>
                                    </select>
                                </div>
                             </div>
                             <div>
                                <label class="text-xs font-medium text-gray-500">Telefono</label>
                                <input type="text" name="telefono" class="w-full text-sm border-gray-200 rounded p-2 bg-gray-50" value="<?= htmlspecialchars($patient['telefono'] ?? '') ?>">
                             </div>
                             <div>
                                <label class="text-xs font-medium text-gray-500">Email</label>
                                <input type="email" name="email" class="w-full text-sm border-gray-200 rounded p-2 bg-gray-50" value="<?= htmlspecialchars($patient['email'] ?? '') ?>">
                             </div>
                             <div>
                                <label class="text-xs font-medium text-gray-500">Indirizzo</label>
                                <textarea name="indirizzo" class="w-full text-sm border-gray-200 rounded p-2 bg-gray-50" rows="2"><?= htmlspecialchars($patient['indirizzo'] ?? '') ?></textarea>
                             </div>
                             <div>
                                <label class="text-xs font-medium text-gray-500">Professione</label>
                                <input type="text" name="professione" class="w-full text-sm border-gray-200 rounded p-2 bg-gray-50" value="<?= htmlspecialchars($patient['professione'] ?? '') ?>">
                             </div>
                             <div class="flex gap-2">
                                <button type="submit" class="flex-1 bg-green-600 text-white text-xs py-2 rounded">Salva</button>
                                <button type="button" onclick="toggleEditMode()" class="flex-1 bg-gray-200 text-gray-600 text-xs py-2 rounded">Annulla</button>
                             </div>
                        </form>
                    </div>
                </div>

                <!-- Food Restrictions (Mini) -->
                <div class="glass-card animate-fade-in delay-100 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <span class="text-lg">🍎</span> Alimenti
                        </h3>
                        <a href="alimenti_gestione.php?paziente_id=<?= $patientId ?>" class="text-xs text-primary font-medium hover:underline">Gestisci</a>
                    </div>
                    <?php if (empty($foodRestrictions)): ?>
                        <p class="text-sm text-gray-500 italic">Nessuna restrizione registrata.</p>
                    <?php else: ?>
                        <div class="flex flex-wrap gap-2 text-xs">
                             <div class="bg-red-50 text-red-600 px-3 py-1 rounded-full border border-red-100 font-medium">
                                <?= $foodManager->countFoodRestrictions($patientId) ?> da evitare
                             </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Middle Column: Prescriptions & History (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Active Prescriptions -->
                <div class="glass-card animate-fade-in delay-200 p-0 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white/30">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                            💊 Prescrizioni Attive
                            <span class="bg-primary/10 text-primary text-xs px-2 py-0.5 rounded-full"><?= $totalPrescriptions ?></span>
                        </h3>
                         <a href="prescrizioni_gestione.php?paziente_id=<?= $patientId ?>" class="btn-outline text-xs px-3 py-1.5 rounded-lg border border-gray-200 hover:border-primary hover:text-primary transition-colors">Gestisci Prescrizioni</a>
                    </div>
                    
                    <?php if (empty($prescriptions)): ?>
                        <div class="p-8 text-center bg-gray-50/50">
                            <p class="text-gray-500 text-sm">Nessuna prescrizione attiva.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-gray-50/50 text-xs uppercase text-gray-500 font-medium">
                                    <tr>
                                        <th class="px-6 py-3">Medicinale</th>
                                        <th class="px-6 py-3">Dosaggio</th>
                                        <th class="px-6 py-3">Frequenza</th>
                                        <th class="px-6 py-3">Dal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($prescriptions as $pr): ?>
                                        <tr class="hover:bg-white/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-gray-800"><?= htmlspecialchars($pr['medicinale_nome']) ?></div>
                                                <div class="text-xs text-gray-400"><?= htmlspecialchars($pr['medicinale_tipologia']) ?></div>
                                            </td>
                                            <td class="px-6 py-4"><?= htmlspecialchars($pr['dosaggio'] ?? '-') ?></td>
                                            <td class="px-6 py-4"><?= htmlspecialchars($pr['frequenza'] ?? '-') ?></td>
                                            <td class="px-6 py-4 text-gray-400"><?= date('d/m/Y', strtotime($pr['data_inizio'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Visit History (Timeline) -->
                <div class="glass-card animate-fade-in delay-300 p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-6 border-b border-gray-100 pb-2">Storico Visite</h3>
                    
                    <?php if (empty($visitHistory)): ?>
                         <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <p class="text-gray-500 mb-4">Nessuna visita registrata.</p>
                            <a href="visita_anamnesi.php?paziente_id=<?= $patientId ?>" class="text-primary font-medium hover:underline">Inizia la prima visita</a>
                        </div>
                    <?php else: ?>
                        <div class="relative border-l-2 border-primary/20 ml-3 space-y-8 pl-8 py-2">
                             <?php foreach ($visitHistory as $visit): ?>
                                <div class="relative group">
                                    <!-- Timeline Dot -->
                                    <div class="absolute -left-[39px] top-1 w-5 h-5 rounded-full border-4 border-white bg-primary shadow-sm group-hover:scale-110 transition-transform"></div>
                                    
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/50 p-4 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-1">Visita del <?= date('d/m/Y', strtotime($visit['data_visita'])) ?></h4>
                                            <div class="flex gap-2">
                                                <?php if ($visit['ha_anamnesi']): ?>
                                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Anamnesi Compilata</span>
                                                <?php else: ?>
                                                    <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-medium">Anamnesi Mancante</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="visita_storico.php?id=<?= $visit['id'] ?>" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:border-primary hover:text-primary transition-colors">
                                                Apri Scheda
                                            </a>
                                            <a href="visita_anamnesi.php?visita_id=<?= $visit['id'] ?>&paziente_id=<?= $patientId ?>" class="px-3 py-1.5 text-xs font-medium text-white bg-primary rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                                                Modifica
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        function toggleEditMode() {
            const displayDiv = document.getElementById('patient-display');
            const editForm = document.getElementById('patient-edit-form');
            
            if (displayDiv.classList.contains('hidden')) {
                displayDiv.classList.remove('hidden');
                editForm.classList.add('hidden');
            } else {
                displayDiv.classList.add('hidden');
                editForm.classList.remove('hidden');
            }
        }

        document.getElementById('patient-edit-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const success = await updatePatient(<?= $patientId ?>, formData);
            if (success) {
                setTimeout(() => location.reload(), 1000);
            }
        });
    </script>
</body>
</html>

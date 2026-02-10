<?php
/**
 * Form compilazione anamnesi per una nuova visita
 * Pagina Anamnesi - Refactored with Tailwind CSS
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Patient.php';
require_once __DIR__ . '/includes/Visit.php';
require_once __DIR__ . '/includes/Anamnesis.php';

// Parametri
$pazienteId = $_GET['paziente_id'] ?? 0;
$visitaId = $_GET['visita_id'] ?? null;

if (!$pazienteId) {
    header('Location: index.php');
    exit;
}

$patientManager = new Patient();
$visitManager = new Visit();
$anamnesisManager = new Anamnesis();

$patient = $patientManager->getPatient($pazienteId);

if (!$patient) {
    header('Location: index.php');
    exit;
}

// Se non c'è una visita, creane una nuova
if (!$visitaId) {
    $visitaId = $visitManager->createVisit($pazienteId);
}

$visit = $visitManager->getVisit($visitaId);
$existingAnamnesis = $anamnesisManager->getAnamnesis($visitaId);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anamnesi - TerraNova</title>
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
        <div class="max-w-5xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="index.php" class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-green-400 flex items-center justify-center text-white font-bold">TN</a>
                <span class="text-sm font-medium text-gray-500"> > Anamnesi</span>
            </div>
            <div>
                 <a href="paziente_dettaglio.php?id=<?= $pazienteId ?>" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Torna al Paziente</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 pb-20">
        
        <!-- Header & Patient Info -->
        <div class="mb-8 animate-fade-in">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Scheda Anamnestica</h1>
            <div class="flex items-center gap-4 text-sm text-gray-500">
                <span class="bg-primary/10 text-primary px-3 py-1 rounded-full font-medium"><?= htmlspecialchars($patient['nome_cognome']) ?></span>
                <span>Visita del <?= date('d/m/Y', strtotime($visit['data_visita'])) ?></span>
            </div>
        </div>

        <form id="anamnesis-form" method="POST" class="space-y-6">
            <input type="hidden" name="visita_id" value="<?= $visitaId ?>">

            <!-- Section: Dati Personali -->
            <section class="glass-card animate-fade-in delay-100">
                <h2 class="text-xl font-bold text-primary mb-6 border-b border-gray-100 pb-2">Dati Personali</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Nome e Cognome</label>
                        <input type="text" name="nome_cognome" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent transition-all" value="<?= htmlspecialchars($patient['nome_cognome']) ?>">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Sesso</label>
                        <select name="sesso" id="sesso-select" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent bg-white">
                            <option value="">Seleziona...</option>
                            <option value="F" <?= ($patient['sesso'] ?? '') == 'F' ? 'selected' : '' ?>>Femmina</option>
                            <option value="M" <?= ($patient['sesso'] ?? '') == 'M' ? 'selected' : '' ?>>Maschio</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Data di nascita</label>
                        <input type="date" name="data_nascita" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent" value="<?= $patient['data_nascita'] ?>">
                    </div>
                    <!-- Contact Info -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Telefono</label>
                        <input type="text" name="telefono" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent" value="<?= htmlspecialchars($patient['telefono'] ?? '') ?>">
                    </div>
                    <div class="space-y-1 md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Indirizzo</label>
                        <input type="text" name="indirizzo" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent" value="<?= htmlspecialchars($patient['indirizzo'] ?? '') ?>">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent" value="<?= htmlspecialchars($patient['email'] ?? '') ?>">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Professione</label>
                        <input type="text" name="professione" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent" value="<?= htmlspecialchars($patient['professione'] ?? '') ?>">
                    </div>
                </div>
            </section>

            <!-- Section: Anamnesi Personale -->
            <section class="glass-card animate-fade-in delay-200">
                <h2 class="text-xl font-bold text-primary mb-6 border-b border-gray-100 pb-2">Anamnesi Personale</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Vomito -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vomito</label>
                        <div class="flex gap-4 mb-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="vomito" value="Si" class="form-radio text-primary focus:ring-primary" <?= ($existingAnamnesis['vomito'] ?? '') == 'Si' ? 'checked' : '' ?>>
                                <span class="ml-2">Sì</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="vomito" value="No" class="form-radio text-primary focus:ring-primary" <?= ($existingAnamnesis['vomito'] ?? 'No') == 'No' ? 'checked' : '' ?>>
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                        <input type="text" name="vomito_dettagli" placeholder="Dettagli..." class="w-full px-3 py-2 text-sm rounded-lg bg-gray-50 border-0 focus:ring-1 focus:ring-primary transition-all" value="<?= htmlspecialchars($existingAnamnesis['vomito_dettagli'] ?? '') ?>">
                    </div>

                    <!-- Febbre -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Febbre</label>
                        <div class="flex gap-4 mb-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="febbre" value="Si" class="form-radio text-primary focus:ring-primary" <?= ($existingAnamnesis['febbre'] ?? '') == 'Si' ? 'checked' : '' ?>>
                                <span class="ml-2">Sì</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="febbre" value="No" class="form-radio text-primary focus:ring-primary" <?= ($existingAnamnesis['febbre'] ?? 'No') == 'No' ? 'checked' : '' ?>>
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                        <input type="text" name="febbre_dettagli" placeholder="Dettagli..." class="w-full px-3 py-2 text-sm rounded-lg bg-gray-50 border-0 focus:ring-1 focus:ring-primary transition-all" value="<?= htmlspecialchars($existingAnamnesis['febbre_dettagli'] ?? '') ?>">
                    </div>

                    <!-- Ciclo (Conditional) -->
                    <div id="ciclo-group" class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ciclo Mestruale / Flusso</label>
                        <div class="flex gap-4 mb-2">
                             <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="flusso" value="Si" class="form-radio text-primary focus:ring-primary" <?= ($existingAnamnesis['flusso'] ?? '') == 'Si' ? 'checked' : '' ?>>
                                <span class="ml-2">Sì</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="flusso" value="No" class="form-radio text-primary focus:ring-primary" <?= ($existingAnamnesis['flusso'] ?? 'No') == 'No' ? 'checked' : '' ?>>
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                        <input type="text" name="flusso_dettagli" placeholder="Dettagli..." class="w-full px-3 py-2 text-sm rounded-lg bg-gray-50 border-0 focus:ring-1 focus:ring-primary transition-all" value="<?= htmlspecialchars($existingAnamnesis['flusso_dettagli'] ?? '') ?>">
                    </div>

                    <!-- Alcol -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Consumo Alcolici</label>
                        <div class="flex gap-4 mb-2">
                             <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="alcol" value="Si" class="form-radio text-primary focus:ring-primary" <?= ($existingAnamnesis['alcol'] ?? '') == 'Si' ? 'checked' : '' ?>>
                                <span class="ml-2">Sì</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="alcol" value="No" class="form-radio text-primary focus:ring-primary" <?= ($existingAnamnesis['alcol'] ?? 'No') == 'No' ? 'checked' : '' ?>>
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                        <input type="text" name="alcol_dettagli" placeholder="Dettagli..." class="w-full px-3 py-2 text-sm rounded-lg bg-gray-50 border-0 focus:ring-1 focus:ring-primary transition-all" value="<?= htmlspecialchars($existingAnamnesis['alcol_dettagli'] ?? '') ?>">
                    </div>
                </div>

                <!-- Textareas -->
                <div class="mt-8 space-y-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Patologie</label>
                        <textarea name="patologie" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent"><?= htmlspecialchars($existingAnamnesis['patologie'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Interventi chirurgici</label>
                        <textarea name="interventi_chirurgici" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent"><?= htmlspecialchars($existingAnamnesis['interventi_chirurgici'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Fratture/Traumi fisici rilevanti</label>
                        <textarea name="fratture_traumi" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent"><?= htmlspecialchars($existingAnamnesis['fratture_traumi'] ?? '') ?></textarea>
                    </div>
                </div>
            </section>
            
            <!-- Section: Qualità del Sonno -->
            <section class="glass-card animate-fade-in delay-200">
                <h2 class="text-xl font-bold text-primary mb-6 border-b border-gray-100 pb-2">Qualità del Sonno</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Qualità</label>
                        <input type="text" name="qualita_sonno" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Es: Buona, Scarsa..." value="<?= htmlspecialchars($existingAnamnesis['qualita_sonno'] ?? '') ?>">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Ore di sonno</label>
                        <input type="number" name="ore_sonno" step="0.5" min="0" max="24" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Es: 7.5" value="<?= $existingAnamnesis['ore_sonno'] ?? '' ?>">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="risvegli_notturni" value="1" class="w-5 h-5 text-primary rounded focus:ring-primary" <?= !empty($existingAnamnesis['risvegli_notturni']) ? 'checked' : '' ?>>
                        <span class="text-gray-700">Risvegli notturni</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="difficolta_addormentarsi" value="1" class="w-5 h-5 text-primary rounded focus:ring-primary" <?= !empty($existingAnamnesis['difficolta_addormentarsi']) ? 'checked' : '' ?>>
                        <span class="text-gray-700">Difficoltà ad addormentarsi</span>
                    </div>
                    <div class="md:col-span-2 space-y-1">
                        <label class="text-sm font-medium text-gray-700">Qualità al risveglio</label>
                        <input type="text" name="qualita_risveglio" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Es: Riposato, Stanco..." value="<?= htmlspecialchars($existingAnamnesis['qualita_risveglio'] ?? '') ?>">
                    </div>
                </div>
            </section>

            <!-- Section: Livello di Stress -->
            <section class="glass-card animate-fade-in delay-300">
                <h2 class="text-xl font-bold text-primary mb-6 border-b border-gray-100 pb-2">Livello di Stress (1–10)</h2>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Seleziona il livello di stress</label>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <input type="radio" name="livello_stress" value="<?= $i ?>" id="stress-<?= $i ?>" class="hidden peer" <?= ($existingAnamnesis['livello_stress'] ?? '') == $i ? 'checked' : '' ?>>
                            <label for="stress-<?= $i ?>" class="cursor-pointer px-4 py-2 rounded-full border border-gray-300 text-gray-700 peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary transition-all hover:bg-gray-100">
                                <?= $i ?>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
            </section>

            <!-- Section: Stato Psico-Fisico -->
            <section class="glass-card animate-fade-in delay-400">
                <h2 class="text-xl font-bold text-primary mb-6 border-b border-gray-100 pb-2">Stato Psico-Fisico</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Appetito</label>
                        <input type="text" name="appetito" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Normale, Scarso, Eccessivo..." value="<?= htmlspecialchars($existingAnamnesis['appetito'] ?? '') ?>">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Ansia</label>
                        <input type="text" name="ansia" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Assente, Lieve, Moderata, Elevata..." value="<?= htmlspecialchars($existingAnamnesis['ansia'] ?? '') ?>">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Umore</label>
                        <input type="text" name="umore" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Stabile, Variabile..." value="<?= htmlspecialchars($existingAnamnesis['umore'] ?? '') ?>">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Motivazione</label>
                        <input type="text" name="motivazione" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Alta, Bassa..." value="<?= htmlspecialchars($existingAnamnesis['motivazione'] ?? '') ?>">
                    </div>
                    <div class="md:col-span-2 space-y-1">
                        <label class="text-sm font-medium text-gray-700">Concentrazione</label>
                        <input type="text" name="concentrazione" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Buona, Scarsa..." value="<?= htmlspecialchars($existingAnamnesis['concentrazione'] ?? '') ?>">
                    </div>
                </div>
            </section>

            <!-- Section: Stile di Vita - Attività Fisica -->
            <section class="glass-card animate-fade-in delay-500">
                <h2 class="text-xl font-bold text-primary mb-6 border-b border-gray-100 pb-2">Stile di Vita – Attività Fisica</h2>
                <div class="space-y-2 mb-6">
                    <label class="block text-sm font-medium text-gray-700">Fai attività fisica?</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="fa_attivita_fisica" value="Si" id="attivita_si" class="form-radio text-primary focus:ring-primary" <?= !empty($existingAnamnesis['attivita_fisica_frequenza']) ? 'checked' : '' ?>>
                            <span class="ml-2">Sì</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="fa_attivita_fisica" value="No" id="attivita_no" class="form-radio text-primary focus:ring-primary" <?= empty($existingAnamnesis['attivita_fisica_frequenza']) ? 'checked' : '' ?>>
                            <span class="ml-2">No</span>
                        </label>
                    </div>
                </div>

                <div id="attivita_details" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="<?= empty($existingAnamnesis['attivita_fisica_frequenza']) ? 'display:none;' : '' ?>">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Frequenza</label>
                        <input type="text" name="attivita_fisica_frequenza" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Es: 3 volte a settimana" value="<?= htmlspecialchars($existingAnamnesis['attivita_fisica_frequenza'] ?? '') ?>">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Tipo</label>
                        <input type="text" name="attivita_fisica_tipo" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Es: Corsa, Palestra, Yoga..." value="<?= htmlspecialchars($existingAnamnesis['attivita_fisica_tipo'] ?? '') ?>">
                    </div>
                </div>
            </section>

            <!-- Section: Stile di Vita - Alimentazione -->
            <section class="glass-card animate-fade-in delay-600">
                <h2 class="text-xl font-bold text-primary mb-6 border-b border-gray-100 pb-2">Stile di Vita – Alimentazione</h2>
                <div class="space-y-1 mb-6">
                    <label class="text-sm font-medium text-gray-700">Descrizione generale</label>
                    <textarea name="alimentazione_generale" rows="4" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent" placeholder="Descrivi le abitudini alimentari del paziente..."><?= htmlspecialchars($existingAnamnesis['alimentazione_generale'] ?? '') ?></textarea>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Fumo?</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="fumo_temp" value="Si" class="form-radio text-primary focus:ring-primary">
                            <span class="ml-2">Sì</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="fumo_temp" value="No" class="form-radio text-primary focus:ring-primary" checked>
                            <span class="ml-2">No</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500">(Verrà salvato nelle note finali)</p>
                </div>
            </section>

            <!-- Section: Supporti Utilizzati -->
            <section class="glass-card animate-fade-in delay-700">
                <h2 class="text-xl font-bold text-primary mb-6 border-b border-gray-100 pb-2">Supporti Utilizzati</h2>
                <div class="space-y-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Farmaci - Categoria</label>
                        <input type="text" name="farmaci_categoria" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Es: Antinfiammatori, Antibiotici..." value="<?= htmlspecialchars($existingAnamnesis['farmaci_categoria'] ?? '') ?>">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Farmaci - Specifiche</label>
                        <textarea name="farmaci_specifiche" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent" placeholder="Nomi farmaci e dosaggi..."><?= htmlspecialchars($existingAnamnesis['farmaci_specifiche'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Integratori - Categoria</label>
                        <input type="text" name="integratori_categoria" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Es: Vitamine, Minerali..." value="<?= htmlspecialchars($existingAnamnesis['integratori_categoria'] ?? '') ?>">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Integratori - Specifiche</label>
                        <textarea name="integratori_specifiche" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent" placeholder="Nomi integratori e dosaggi..."><?= htmlspecialchars($existingAnamnesis['integratori_specifiche'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Rimedi naturali</label>
                        <textarea name="rimedi_naturali" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent"><?= htmlspecialchars($existingAnamnesis['rimedi_naturali'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Terapie in corso</label>
                        <textarea name="terapie_corso" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent"><?= htmlspecialchars($existingAnamnesis['terapie_corso'] ?? '') ?></textarea>
                    </div>
                </div>
            </section>

            <!-- Section: Osservazioni Finali -->
            <section class="glass-card animate-fade-in delay-800">
                <h2 class="text-xl font-bold text-primary mb-6 border-b border-gray-100 pb-2">Osservazioni Finali</h2>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Note e osservazioni</label>
                    <textarea name="osservazioni_finali" id="osservazioni_finali" rows="6" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent" placeholder="Note della naturologa..."><?= htmlspecialchars($existingAnamnesis['osservazioni_finali'] ?? '') ?></textarea>
                </div>
            </section>

            <!-- Save Bar (Sticky Bottom) -->
            <div class="sticky bottom-0 z-40 bg-white/80 backdrop-blur-md border-t border-gray-200 p-4 -mx-4 md:rounded-t-2xl md:mx-0 flex justify-end gap-4 shadow-lg">
                <a href="paziente_dettaglio.php?id=<?= $pazienteId ?>" class="px-6 py-2 rounded-lg text-gray-600 font-medium hover:bg-gray-100 transition-colors">Annulla</a>
                <button type="submit" class="px-8 py-2 rounded-lg bg-gradient-to-r from-primary to-green-600 text-white font-bold shadow-lg shadow-green-500/30 hover:shadow-green-500/50 hover:scale-105 transition-all">Salva Anamnesi</button>
            </div>

        </form>
    </div>

    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestione Sesso -> Ciclo Mestruale
            const sessoSelect = document.getElementById('sesso-select');
            const cicloGroup = document.getElementById('ciclo-group');
            
            function toggleCiclo() {
                if (sessoSelect.value === 'M') {
                    cicloGroup.style.display = 'none';
                    // Reset fields if hidden
                    const radios = cicloGroup.querySelectorAll('input[type="radio"]');
                    radios.forEach(r => r.checked = false);
                    const inputField = cicloGroup.querySelector('input[type="text"]');
                    if (inputField) inputField.value = '';
                } else {
                    cicloGroup.style.display = 'block';
                }
            }
            
            sessoSelect.addEventListener('change', toggleCiclo);
            toggleCiclo(); // Init on load

            // Gestione Attività Fisica
            const attivitaSi = document.getElementById('attivita_si');
            const attivitaNo = document.getElementById('attivita_no');
            const attivitaDetails = document.getElementById('attivita_details');

            function toggleAttivita() {
                if (attivitaSi.checked) {
                    attivitaDetails.style.display = 'grid'; // restoring grid layout
                } else {
                    attivitaDetails.style.display = 'none';
                    attivitaDetails.querySelectorAll('input').forEach(i => i.value = '');
                }
            }

            attivitaSi.addEventListener('change', toggleAttivita);
            attivitaNo.addEventListener('change', toggleAttivita);
            toggleAttivita(); // Init on load
        });

        document.getElementById('anamnesis-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Gestione Fumo (Append to Note Finali)
            const fumo = this.querySelector('input[name="fumo_temp"]:checked');
            if (fumo && fumo.value === 'Si') {
                let note = formData.get('osservazioni_finali') || '';
                if (!note.includes('[Fumatore]')) {
                    note = '[Fumatore] ' + note;
                    formData.set('osservazioni_finali', note);
                }
            }

            try {
                const data = Object.fromEntries(formData);
                const result = await ajaxRequest('<?= $existingAnamnesis ? 'update_anamnesis' : 'save_anamnesis' ?>', data);
                
                if (result.success) {
                    showMessage('Anamnesi salvata con successo!', 'success');
                    setTimeout(() => {
                        window.location.href = 'paziente_dettaglio.php?id=<?= $pazienteId ?>';
                    }, 1500);
                } else {
                    showMessage('Errore nel salvataggio dell\'anamnesi: ' + (result.message || 'Errore sconosciuto'), 'error');
                }
            } catch (error) {
                console.error('Fetch error:', error);
                showMessage('Errore nel salvataggio dell\'anamnesi', 'error');
            }
        });
    </script>
</body>
</html>

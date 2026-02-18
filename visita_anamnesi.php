<?php
/**
 * Anamnesi e Visita - Semplificato
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Patient.php';
require_once __DIR__ . '/includes/Visit.php';
require_once __DIR__ . '/includes/Anamnesis.php';

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
if (!$visitaId) {
    // Create new visit immediately
    $visitaId = $visitManager->createVisit($pazienteId);
}
$visit = $visitManager->getVisit($visitaId);
$anamnesi = $anamnesisManager->getAnamnesis($pazienteId);
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Visita - <?= htmlspecialchars($patient['nome_cognome']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/style.css">
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#2d8659', secondary: '#f4a261' } } } }</script>
</head>

<body class="bg-gray-50 min-h-screen text-gray-800">
    <nav class="sticky top-0 z-50 glass px-6 py-4 mb-8">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="index.php"
                    class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-green-400 flex items-center justify-center text-white font-bold">TN</a>
                <span class="text-sm font-medium text-gray-500"> > Visita del
                    <?= date('d/m/Y', strtotime($visit['data_visita'])) ?></span>
            </div>
            <a href="paziente_dettaglio.php?id=<?= $pazienteId ?>"
                class="text-sm font-medium text-gray-600 hover:text-primary">Torna al Paziente</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 pb-24">
        <h1 class="text-3xl font-bold mb-2">Visita: <?= htmlspecialchars($patient['nome_cognome']) ?></h1>

        <form id="full-form" class="space-y-8">
            <input type="hidden" name="paziente_id" value="<?= $pazienteId ?>">
            <input type="hidden" name="visita_id" value="<?= $visitaId ?>">

            <!-- 1. ANAMNESI GENERALE (Fissa) -->
            <div class="glass-card p-6 border-l-4 border-secondary">
                <h2 class="text-xl font-bold text-secondary mb-4">Anamnesi Generale (Storia)</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Allergie / Intolleranze</label>
                        <textarea name="allergie_intolleranze" class="w-full border rounded p-2 text-sm"
                            rows="3"><?= htmlspecialchars($anamnesi['allergie_intolleranze'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Patologie Pregresse</label>
                        <textarea name="patologie_pregresse" class="w-full border rounded p-2 text-sm"
                            rows="3"><?= htmlspecialchars($anamnesi['patologie_pregresse'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Interventi Chirurgici</label>
                        <textarea name="interventi_chirurgici" class="w-full border rounded p-2 text-sm"
                            rows="2"><?= htmlspecialchars($anamnesi['interventi_chirurgici'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Esami Clinici Recenti</label>
                        <textarea name="esami_clinici_recenti" class="w-full border rounded p-2 text-sm"
                            rows="2"><?= htmlspecialchars($anamnesi['esami_clinici_recenti'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-1 md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Terapie Farmacologiche Croniche</label>
                        <textarea name="terapie_farmacologiche_croniche" class="w-full border rounded p-2 text-sm"
                            rows="2"><?= htmlspecialchars($anamnesi['terapie_farmacologiche_croniche'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Traumi / Fratture</label>
                        <textarea name="traumi_o_fratture" class="w-full border rounded p-2 text-sm"
                            rows="2"><?= htmlspecialchars($anamnesi['traumi_o_fratture'] ?? '') ?></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Fumo</label>
                            <input type="text" name="fumo" value="<?= htmlspecialchars($anamnesi['fumo'] ?? '') ?>"
                                class="w-full border rounded p-2 text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Alcol</label>
                            <input type="text" name="alcol" value="<?= htmlspecialchars($anamnesi['alcol'] ?? '') ?>"
                                class="w-full border rounded p-2 text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. LA VISITA DI OGGI -->
            <div class="glass-card p-6 border-l-4 border-primary">
                <h2 class="text-xl font-bold text-primary mb-4">Dettagli Visita Odierna</h2>

                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Motivazione Visita</label>
                        <textarea name="motivazione" class="w-full border rounded p-2 text-sm"
                            rows="2"><?= htmlspecialchars($visit['motivazione'] ?? '') ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Sintomi Acuti (Febbre, Vomito, etc)</label>
                            <textarea name="sintomi_acuti" class="w-full border rounded p-2 text-sm"
                                rows="2"><?= htmlspecialchars($visit['sintomi_acuti'] ?? '') ?></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Stato Emotivo</label>
                            <textarea name="stato_emotivo" class="w-full border rounded p-2 text-sm"
                                rows="2"><?= htmlspecialchars($visit['stato_emotivo'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Stress (1-10)</label>
                            <input type="number" name="livello_stress" value="<?= $visit['livello_stress'] ?? '' ?>"
                                class="w-full border rounded p-2 text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Energia (1-10)</label>
                            <input type="number" name="livello_energia" value="<?= $visit['livello_energia'] ?? '' ?>"
                                class="w-full border rounded p-2 text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Ore Sonno</label>
                            <input type="number" step="0.5" name="ore_sonno" value="<?= $visit['ore_sonno'] ?? '' ?>"
                                class="w-full border rounded p-2 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Qualità Sonno</label>
                            <input type="text" name="qualita_sonno_percepita"
                                value="<?= htmlspecialchars($visit['qualita_sonno_percepita'] ?? '') ?>"
                                class="w-full border rounded p-2 text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Difficoltà Sonno</label>
                            <input type="text" name="difficolta_addormentarsi_risvegli_notturni"
                                value="<?= htmlspecialchars($visit['difficolta_addormentarsi_risvegli_notturni'] ?? '') ?>"
                                class="w-full border rounded p-2 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Appetito e Digestione</label>
                            <textarea name="appetito_e_digestione" class="w-full border rounded p-2 text-sm"
                                rows="2"><?= htmlspecialchars($visit['appetito_e_digestione'] ?? '') ?></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Regolarità Intestinale</label>
                            <input type="text" name="regolarita_intestinale"
                                value="<?= htmlspecialchars($visit['regolarita_intestinale'] ?? '') ?>"
                                class="w-full border rounded p-2 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Alimentazione Recente</label>
                            <textarea name="alimentazione_recente" class="w-full border rounded p-2 text-sm"
                                rows="2"><?= htmlspecialchars($visit['alimentazione_recente'] ?? '') ?></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Supporti in uso
                                (Integratori/Farmaci)</label>
                            <textarea name="supporti_in_uso" class="w-full border rounded p-2 text-sm"
                                rows="2"><?= htmlspecialchars($visit['supporti_in_uso'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Attività Fisica</label>
                            <input type="text" name="attivita_fisica"
                                value="<?= htmlspecialchars($visit['attivita_fisica'] ?? '') ?>"
                                class="w-full border rounded p-2 text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Idratazione</label>
                            <input type="text" name="idratazione"
                                value="<?= htmlspecialchars($visit['idratazione'] ?? '') ?>"
                                class="w-full border rounded p-2 text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Concentrazione</label>
                            <input type="text" name="concentrazione"
                                value="<?= htmlspecialchars($visit['concentrazione'] ?? '') ?>"
                                class="w-full border rounded p-2 text-sm">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Note Finali Visita</label>
                        <textarea name="note_finali" class="w-full border rounded p-2 text-sm"
                            rows="4"><?= htmlspecialchars($visit['note_finali'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Sticky Save -->
            <div class="sticky bottom-4 z-40">
                <button type="submit"
                    class="w-full md:w-auto ml-auto block bg-primary text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:bg-green-700 transition-all">
                    SALVA TUTTO
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('full-form').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            // 1. Save Anamnesi
            formData.append('action', 'save_general_anamnesis');
            await fetch('ajax_handlers.php', { method: 'POST', body: formData });

            // 2. Save Visit
            formData.set('action', 'update_visit'); // Switch action
            formData.append('id', '<?= $visitaId ?>'); // Ensure ID is passed for update logic
            const res = await fetch('ajax_handlers.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                alert('Salvataggio completato!');
                window.location.href = 'paziente_dettaglio.php?id=<?= $pazienteId ?>';
            } else {
                alert('Errore salvataggio visita');
            }
        });
    </script>
</body>

</html>
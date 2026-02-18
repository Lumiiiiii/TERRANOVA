<?php
/**
 * Pagina gestione prescrizioni per un paziente - Refactored with Tailwind CSS
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Patient.php';
require_once __DIR__ . '/includes/Medicine.php';
require_once __DIR__ . '/includes/Prescription.php';

$patientId = $_GET['paziente_id'] ?? 0;

if (!$patientId) {
    header('Location: index.php');
    exit;
}

$patientManager = new Patient();
$medicineManager = new Medicine();
$prescriptionManager = new Prescription();

$patient = $patientManager->getPatient($patientId);
if (!$patient) {
    header('Location: index.php');
    exit;
}

$prescriptions = $prescriptionManager->getPrescriptionsByPatient($patientId);
$medicines = $medicineManager->getAllMedicines();
$activePrescriptions = array_filter($prescriptions, fn($p) => $p['attivo']);
$inactivePrescriptions = array_filter($prescriptions, fn($p) => !$p['attivo']);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescrizioni - <?= htmlspecialchars($patient['nome_cognome']) ?> - TerraNova</title>
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
                    <span class="text-xs text-gray-500 font-medium">Gestionale Naturopatia</span>
                    <h1 class="text-sm font-bold text-gray-800 leading-none">Piano Terapeutico</h1>
                </div>
            </div>
            <div>
                 <a href="paziente_dettaglio.php?id=<?= $patientId ?>" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Torna al Paziente
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 pb-20">
        
        <!-- Header & Patient Info -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Prescrizioni</h1>
                <p class="text-gray-500">Paziente: <span class="font-semibold text-primary"><?= htmlspecialchars($patient['nome_cognome']) ?></span></p>
            </div>
            <a href="export_pdf.php?type=prescriptions&patient_id=<?= $patientId ?>" target="_blank" class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium shadow-sm flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Stampa Piano
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Add Prescription Form -->
            <div class="lg:col-span-1">
                <div class="glass-card sticky top-24 animate-fade-in delay-100">
                    <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-2">
                        <h2 class="text-lg font-bold text-primary">➕ Nuova Prescrizione</h2>
                    </div>
                    
                    <form id="add-prescription-form" class="space-y-4">
                        <input type="hidden" name="paziente_id" value="<?= $patientId ?>">
                        
                        <div class="space-y-1">
                            <div class="flex justify-between items-center">
                                <label class="text-xs font-medium text-gray-700">Medicinale *</label>
                                <button type="button" id="btn-new-medicine" class="text-xs text-primary hover:underline font-medium">+ Nuovo Medicinale</button>
                            </div>
                            <select name="medicinale_id" id="medicinale-select" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 bg-white text-sm">
                                <option value="">Seleziona...</option>
                                <?php
                                $currentType = '';
                                foreach ($medicines as $medicine):
                                    if ($currentType !== $medicine['tipologia']):
                                        if ($currentType !== '') echo '</optgroup>';
                                        echo '<optgroup label="' . htmlspecialchars($medicine['tipologia']) . '">';
                                        $currentType = $medicine['tipologia'];
                                    endif;
                                ?>
                                    <option value="<?= $medicine['id'] ?>" 
                                            data-dosaggio="<?= htmlspecialchars($medicine['dosaggio_standard'] ?? '') ?>"
                                            data-formato="<?= htmlspecialchars($medicine['formato'] ?? '') ?>">
                                        <?= htmlspecialchars($medicine['nome']) ?>
                                        <?php if ($medicine['formato']): ?>
                                            (<?= htmlspecialchars($medicine['formato']) ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php if ($currentType !== ''): ?></optgroup><?php endif; ?>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-700">Dosaggio</label>
                                <input type="text" name="dosaggio" id="dosaggio-input" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" placeholder="Es: 5 gocce">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-700">Frequenza</label>
                                <input type="text" name="frequenza" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" placeholder="Es: 3 volte al dì">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-700">Durata</label>
                                <input type="text" name="durata" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" placeholder="Es: 1 mese">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-700">Data Inizio</label>
                                <input type="date" name="data_inizio" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-700">Note (Opzionale)</label>
                            <textarea name="note_prescrizione" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" placeholder="Note aggiuntive..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-primary to-green-600 text-white font-bold py-2 rounded-lg shadow-md hover:shadow-lg hover:scale-[1.02] transition-all">Aggiungi al Piano</button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Active & History (2 cols) -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Active List -->
                <div class="glass-card p-0 overflow-hidden animate-fade-in delay-200">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white/30">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            💊 Prescrizioni Attive 
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full"><?= count($activePrescriptions) ?></span>
                        </h2>
                    </div>

                    <?php if (empty($activePrescriptions)): ?>
                        <div class="p-10 text-center">
                            <p class="text-gray-500">Nessuna prescrizione attiva al momento.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-gray-50/50 text-xs uppercase text-gray-500 font-medium">
                                    <tr>
                                        <th class="px-6 py-3">Medicinale</th>
                                        <th class="px-6 py-3">Dettagli</th>
                                        <th class="px-6 py-3">Durata</th>
                                        <th class="px-6 py-3 text-right">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($activePrescriptions as $pr): ?>
                                        <tr class="hover:bg-white/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-gray-800"><?= htmlspecialchars($pr['medicinale_nome']) ?></div>
                                                <div class="text-xs text-primary"><?= htmlspecialchars($pr['medicinale_tipologia']) ?></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-gray-800"><?= htmlspecialchars($pr['dosaggio'] ?? '-') ?></div>
                                                <div class="text-xs text-gray-500"><?= htmlspecialchars($pr['frequenza'] ?? '-') ?></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div><?= htmlspecialchars($pr['durata'] ?? '-') ?></div>
                                                <div class="text-xs text-gray-400">Dal <?= date('d/m/Y', strtotime($pr['data_inizio'])) ?></div>
                                            </td>
                                            <td class="px-6 py-4 text-right space-x-2">
                                                <button onclick="endPrescription(<?= $pr['prescrizione_id'] ?>)" class="text-xs bg-red-50 text-red-600 px-3 py-1 rounded-lg border border-red-100 hover:bg-red-100 transition-colors">Termina</button>
                                                <button onclick="deletePrescription(<?= $pr['prescrizione_id'] ?>)" class="text-gray-400 hover:text-red-500 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Inactive / History List -->
                <?php if (!empty($inactivePrescriptions)): ?>
                    <div class="glass-card p-6 animate-fade-in delay-300 opacity-80">
                        <h3 class="font-bold text-gray-700 mb-4 border-b border-gray-100 pb-2">Archivio Storico</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($inactivePrescriptions as $pr): ?>
                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 flex justify-between items-center">
                                    <div>
                                        <div class="font-medium text-gray-600"><?= htmlspecialchars($pr['medicinale_nome']) ?></div>
                                        <div class="text-xs text-gray-400">
                                            <?= date('d/m/Y', strtotime($pr['data_inizio'])) ?> - 
                                            <?= $pr['data_fine'] ? date('d/m/Y', strtotime($pr['data_fine'])) : 'Terminata' ?>
                                        </div>
                                    </div>
                                    <button onclick="reactivatePrescription(<?= $pr['prescrizione_id'] ?>)" class="text-xs text-primary hover:bg-green-50 px-2 py-1 rounded border border-transparent hover:border-green-100 transition-colors">
                                        Riattiva
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Modal Nuovo Medicinale (Tailwind Style) -->
    <div id="new-medicine-modal" class="fixed inset-0 z-[100] hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="modal-backdrop"></div>
        
        <!-- Modal Content -->
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md p-4 transition-all scale-95 opacity-0" id="modal-content">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden glass">
                <div class="bg-gradient-to-r from-primary to-green-600 p-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg">Nuovo Medicinale</h3>
                    <button class="close-modal hover:bg-white/20 rounded-full p-1 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form id="new-medicine-form" class="p-6 space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-700">Nome Medicinale *</label>
                        <input type="text" name="nome" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" required>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-700">Tipologia</label>
                        <select name="tipologia" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm bg-white">
                            <option value="Omeopatico">Omeopatico</option>
                            <option value="Fitoterapico">Fitoterapico</option>
                            <option value="Integratore">Integratore</option>
                            <option value="Altro">Altro</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-700">Formato</label>
                            <input type="text" name="formato" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" placeholder="Es: Gocce">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-700">Dosaggio Std.</label>
                            <input type="text" name="dosaggio_standard" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" placeholder="Es: 10ml">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-primary text-white font-bold py-2 rounded-lg shadow-md hover:bg-green-700 transition-colors">Salva e Seleziona</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        // Modal Logic Refactored for Tailwind
        const modal = document.getElementById('new-medicine-modal');
        const modalBackdrop = document.getElementById('modal-backdrop');
        const modalContent = document.getElementById('modal-content');
        const btnNewMed = document.getElementById('btn-new-medicine');
        const closeButtons = document.querySelectorAll('.close-modal');

        function openModal() {
            modal.classList.remove('hidden');
            // Trigger reflow
            void modal.offsetWidth; 
            modalBackdrop.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95', 'opacity-0');
        }

        function closeModal() {
            modalBackdrop.classList.add('opacity-0');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        if (btnNewMed) btnNewMed.onclick = openModal;
        
        closeButtons.forEach(btn => btn.onclick = closeModal);
        modalBackdrop.onclick = closeModal;

        // Auto-fill Dosage on Medicine Select
        document.getElementById('medicinale-select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const dosaggio = selectedOption.getAttribute('data-dosaggio');
            if (dosaggio) {
                document.getElementById('dosaggio-input').value = dosaggio;
            } else {
                document.getElementById('dosaggio-input').value = ''; // Clear if no standard dosage
            }
        });

        // Handle New Medicine Submit
        document.getElementById('new-medicine-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const result = await ajaxRequest('add_medicine', Object.fromEntries(formData));
                if (result.success) {
                    // Update Select
                    const select = document.getElementById('medicinale-select');
                    const opt = document.createElement('option');
                    opt.value = result.medicine.id;
                    opt.text = result.medicine.nome + (result.medicine.formato ? ' (' + result.medicine.formato + ')' : '');
                    opt.setAttribute('data-dosaggio', result.medicine.dosaggio_standard || '');
                    opt.selected = true;
                    
                    // Find the correct optgroup or append to select directly
                    let targetOptgroup = null;
                    const tipologia = formData.get('tipologia');
                    const optgroups = select.querySelectorAll('optgroup');
                    for (const og of optgroups) {
                        if (og.label === tipologia) {
                            targetOptgroup = og;
                            break;
                        }
                    }

                    if (targetOptgroup) {
                        targetOptgroup.appendChild(option);
                    } else {
                        // If optgroup doesn't exist, create it
                        const newOptgroup = document.createElement('optgroup');
                        newOptgroup.label = tipologia;
                        newOptgroup.appendChild(option);
                        select.appendChild(newOptgroup);
                    }

                    // Trigger change event to update dosage input
                    select.dispatchEvent(new Event('change'));
                } else {
                    showNotification('Errore creazione medicinale: ' + (result.message || 'Errore sconosciuto'), 'error');
                }
            } catch (error) {
                showNotification('Errore creazione medicinale: ' + error.message, 'error');
            }
        });

        // Auto-riempi dosaggio quando si seleziona un medicinale
        document.getElementById('medicinale-select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const dosaggio = selectedOption.dataset.dosaggio;
            if (dosaggio) {
                document.getElementById('dosaggio-input').value = dosaggio;
            } else {
                document.getElementById('dosaggio-input').value = ''; // Clear if no standard dosage
            }
        });

        // Form aggiunta prescrizione
        document.getElementById('add-prescription-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const success = await addPrescription(formData);
            if (success) {
                this.reset();
                document.querySelector('[name="data_inizio"]').value = '<?= date('Y-m-d') ?>';
                setTimeout(() => location.reload(), 1000);
            }
        });

        async function endPrescription(id) {
            if (confirm('Terminare questa prescrizione?')) {
                const success = await window.endPrescription(id);
                if (success) {
                    setTimeout(() => location.reload(), 1000);
                }
            }
        }

        async function reactivatePrescription(id) {
            const formData = new FormData();
            formData.append('id', id);
            
            const response = await fetch('ajax_handlers.php?action=reactivate_prescription', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            if (result.success) {
                showNotification('Prescrizione riattivata', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Errore riattivazione prescrizione', 'error');
            }
        }
    </script>
</body>
</html>

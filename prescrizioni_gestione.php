<?php
/**
 * Pagina gestione prescrizioni per un paziente
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
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescrizioni - <?= htmlspecialchars($patient['nome_cognome']) ?> - TerraNova</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <h1>TerraNova - Gestionale Naturopatia</h1>
            <nav class="header-nav">
                <a href="index.php">Home</a>
                <a href="medicinali_gestione.php">Medicinali</a>
                <a href="paziente_dettaglio.php?id=<?= $patientId ?>">← Torna al Paziente</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Patient Info -->
        <div class="card">
            <div class="flex-between flex-center">
                <h2 class="card-header" style="margin-bottom: 0;">Prescrizioni: <?= htmlspecialchars($patient['nome_cognome']) ?></h2>
                <a href="export_pdf.php?type=prescriptions&patient_id=<?= $patientId ?>" target="_blank" class="btn btn-outline btn-small">Stampa Piano</a>
            </div>
        </div>

        <!-- Form Nuova Prescrizione -->
        <div class="card">
            <h3 class="card-header">➕ Nuova Prescrizione</h3>
            <form id="add-prescription-form">
                <input type="hidden" name="paziente_id" value="<?= $patientId ?>">
                
                <div class="form-group">
                    <label class="form-label">Medicinale *</label>
                    <select name="medicinale_id" class="form-input" required id="medicinale-select">
                        <option value="">Seleziona un medicinale...</option>
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
                                    - <?= htmlspecialchars($medicine['formato']) ?>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                        <?php if ($currentType !== ''): ?></optgroup><?php endif; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Dosaggio</label>
                        <input type="text" name="dosaggio" id="dosaggio-input" class="form-input" placeholder="es: 5 granuli">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Frequenza</label>
                        <input type="text" name="frequenza" class="form-input" placeholder="es: 3 volte al giorno">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Durata</label>
                        <input type="text" name="durata" class="form-input" placeholder="es: 15 giorni">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Data Inizio</label>
                        <input type="date" name="data_inizio" class="form-input" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Data Fine (opzionale)</label>
                        <input type="date" name="data_fine" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Note Prescrizione</label>
                    <textarea name="note_prescrizione" class="form-textarea" rows="2" placeholder="Note specifiche per questa prescrizione..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">💾 Aggiungi Prescrizione</button>
            </form>
        </div>

        <!-- Prescrizioni Attive -->
        <?php if (!empty($activePrescriptions)): ?>
        <div class="card">
            <h3 class="card-header">💊 Prescrizioni Attive (<?= count($activePrescriptions) ?>)</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Medicinale</th>
                        <th>Dosaggio</th>
                        <th>Frequenza</th>
                        <th>Durata</th>
                        <th>Dal</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activePrescriptions as $pr): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($pr['medicinale_nome']) ?></strong><br>
                                <small><?= htmlspecialchars($pr['medicinale_tipologia']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($pr['dosaggio'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($pr['frequenza'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($pr['durata'] ?? '-') ?></td>
                            <td><?= date('d/m/Y', strtotime($pr['data_inizio'])) ?></td>
                            <td>
                                <button onclick="endPrescription(<?= $pr['prescrizione_id'] ?>)" class="btn btn-small btn-secondary">🔴 Termina</button>
                                <button onclick="deletePrescription(<?= $pr['prescrizione_id'] ?>)" class="btn btn-small btn-outline">🗑️</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Storico Prescrizioni -->
        <?php
        $inactivePrescriptions = array_filter($prescriptions, fn($p) => !$p['attivo']);
        if (!empty($inactivePrescriptions)):
        ?>
        <div class="card">
            <h3 class="card-header">📋 Storico Prescrizioni (<?= count($inactivePrescriptions) ?>)</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Medicinale</th>
                        <th>Dosaggio</th>
                        <th>Periodo</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inactivePrescriptions as $pr): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($pr['medicinale_nome']) ?></strong><br>
                                <small><?= htmlspecialchars($pr['medicinale_tipologia']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($pr['dosaggio'] ?? '-') ?></td>
                            <td>
                                <?= date('d/m/Y', strtotime($pr['data_inizio'])) ?>
                                <?php if ($pr['data_fine']): ?>
                                    - <?= date('d/m/Y', strtotime($pr['data_fine'])) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button onclick="reactivatePrescription(<?= $pr['prescrizione_id'] ?>)" class="btn btn-small btn-primary">🟢 Riattiva</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <script src="js/main.js"></script>
    <script>
        // Auto-riempi dosaggio quando si seleziona un medicinale
        document.getElementById('medicinale-select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const dosaggio = selectedOption.dataset.dosaggio;
            if (dosaggio) {
                document.getElementById('dosaggio-input').value = dosaggio;
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

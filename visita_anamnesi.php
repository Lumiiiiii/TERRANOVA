<?php
/**
 * Form compilazione anamnesi per una nuova visita
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
    <title>Anamnesi Visita - TerraNova</title>
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
                <a href="paziente_dettaglio.php?id=<?= $pazienteId ?>">← Torna al Paziente</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="card">
            <h2 class="card-header">Scheda Anamnestica e Stile di Vita</h2>
            
            <!-- Patient Info -->
            <div style="background: var(--primary-light); padding: 15px; border-radius: 8px; margin-bottom: 30px;">
                <p><strong>Paziente:</strong> <?= htmlspecialchars($patient['nome_cognome']) ?></p>
                <p><strong>Data Visita:</strong> <?= date('d/m/Y', strtotime($visit['data_visita'])) ?></p>
            </div>

            <form id="anamnesis-form" method="POST">
                <input type="hidden" name="visita_id" value="<?= $visitaId ?>">

                <!-- Dati Personali -->
                <div class="card-header">DATI PERSONALI</div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nome e Cognome</label>
                        <input type="text" class="form-input" value="<?= htmlspecialchars($patient['nome_cognome']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Data di nascita</label>
                        <input type="text" class="form-input" value="<?= $patient['data_nascita'] ? date('d/m/Y', strtotime($patient['data_nascita'])) : '' ?>" readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Indirizzo</label>
                        <input type="text" class="form-input" value="<?= htmlspecialchars($patient['indirizzo'] ?? '') ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Telefono</label>
                        <input type="text" class="form-input" value="<?= htmlspecialchars($patient['telefono'] ?? '') ?>" readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input" value="<?= htmlspecialchars($patient['email'] ?? '') ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Professione</label>
                        <input type="text" class="form-input" value="<?= htmlspecialchars($patient['professione'] ?? '') ?>" readonly>
                    </div>
                </div>

                <!-- Anamnesi Personale -->
                <div class="card-header" style="margin-top: 30px;">ANAMNESI PERSONALE</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Vomito</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="vomito" value="Si" <?= ($existingAnamnesis['vomito'] ?? '') == 'Si' ? 'checked' : '' ?>> Sì
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="vomito" value="No" <?= ($existingAnamnesis['vomito'] ?? 'No') == 'No' ? 'checked' : '' ?>> No
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="vomito" value="Dettagli" <?= ($existingAnamnesis['vomito'] ?? '') == 'Dettagli' ? 'checked' : '' ?>> Dettagli
                            </label>
                        </div>
                        <input type="text" name="vomito_dettagli" class="form-input" placeholder="Dettagli..." value="<?= htmlspecialchars($existingAnamnesis['vomito_dettagli'] ?? '') ?>" style="margin-top: 8px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Febbre</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="febbre" value="Si" <?= ($existingAnamnesis['febbre'] ?? '') == 'Si' ? 'checked' : '' ?>> Sì
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="febbre" value="No" <?= ($existingAnamnesis['febbre'] ?? 'No') == 'No' ? 'checked' : '' ?>> No
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="febbre" value="Dettagli" <?= ($existingAnamnesis['febbre'] ?? '') == 'Dettagli' ? 'checked' : '' ?>> Dettagli
                            </label>
                        </div>
                        <input type="text" name="febbre_dettagli" class="form-input" placeholder="Dettagli..." value="<?= htmlspecialchars($existingAnamnesis['febbre_dettagli'] ?? '') ?>" style="margin-top: 8px;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Flusso</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="flusso" value="Si" <?= ($existingAnamnesis['flusso'] ?? '') == 'Si' ? 'checked' : '' ?>> Sì
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="flusso" value="No" <?= ($existingAnamnesis['flusso'] ?? 'No') == 'No' ? 'checked' : '' ?>> No
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="flusso" value="Dettagli" <?= ($existingAnamnesis['flusso'] ?? '') == 'Dettagli' ? 'checked' : '' ?>> Dettagli
                            </label>
                        </div>
                        <input type="text" name="flusso_dettagli" class="form-input" placeholder="Dettagli..." value="<?= htmlspecialchars($existingAnamnesis['flusso_dettagli'] ?? '') ?>" style="margin-top: 8px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alcol</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="alcol" value="Si" <?= ($existingAnamnesis['alcol'] ?? '') == 'Si' ? 'checked' : '' ?>> Sì
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="alcol" value="No" <?= ($existingAnamnesis['alcol'] ?? 'No') == 'No' ? 'checked' : '' ?>> No
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="alcol" value="Dettagli" <?= ($existingAnamnesis['alcol'] ?? '') == 'Dettagli' ? 'checked' : '' ?>> Dettagli
                            </label>
                        </div>
                        <input type="text" name="alcol_dettagli" class="form-input" placeholder="Dettagli..." value="<?= htmlspecialchars($existingAnamnesis['alcol_dettagli'] ?? '') ?>" style="margin-top: 8px;">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Patologie</label>
                    <textarea name="patologie" class="form-textarea"><?= htmlspecialchars($existingAnamnesis['patologie'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Interventi chirurgici</label>
                    <textarea name="interventi_chirurgici" class="form-textarea"><?= htmlspecialchars($existingAnamnesis['interventi_chirurgici'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Fratture/Traumi fisici rilevanti</label>
                    <textarea name="fratture_traumi" class="form-textarea"><?= htmlspecialchars($existingAnamnesis['fratture_traumi'] ?? '') ?></textarea>
                </div>

                <!-- Qualità del Sonno -->
                <div class="card-header" style="margin-top: 30px;">QUALITÀ DEL SONNO</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Qualità</label>
                        <input type="text" name="qualita_sonno" class="form-input" placeholder="Es: Buona, Scarsa..." value="<?= htmlspecialchars($existingAnamnesis['qualita_sonno'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ore di sonno</label>
                        <input type="number" name="ore_sonno" class="form-input" step="0.5" min="0" max="24" placeholder="Es: 7.5" value="<?= $existingAnamnesis['ore_sonno'] ?? '' ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="risvegli_notturni" value="1" <?= !empty($existingAnamnesis['risvegli_notturni']) ? 'checked' : '' ?>>
                            <span>Risvegli notturni</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="difficolta_addormentarsi" value="1" <?= !empty($existingAnamnesis['difficolta_addormentarsi']) ? 'checked' : '' ?>>
                            <span>Difficoltà ad addormentarsi</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Qualità al risveglio</label>
                    <input type="text" name="qualita_risveglio" class="form-input" placeholder="Es: Riposato, Stanco..." value="<?= htmlspecialchars($existingAnamnesis['qualita_risveglio'] ?? '') ?>">
                </div>

                <!-- Livello di Stress -->
                <div class="card-header" style="margin-top: 30px;">LIVELLO DI STRESS (1–10)</div>
                
                <div class="form-group">
                    <label class="form-label">Seleziona il livello di stress</label>
                    <div class="stress-scale">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <input type="radio" name="livello_stress" value="<?= $i ?>" id="stress-<?= $i ?>" <?= ($existingAnamnesis['livello_stress'] ?? '') == $i ? 'checked' : '' ?>>
                            <label for="stress-<?= $i ?>"><?= $i ?></label>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Stato Psico-Fisico -->
                <div class="card-header" style="margin-top: 30px;">STATO PSICO-FISICO</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Appetito</label>
                        <input type="text" name="appetito" class="form-input" placeholder="Normale, Scarso, Eccessivo..." value="<?= htmlspecialchars($existingAnamnesis['appetito'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ansia</label>
                        <input type="text" name="ansia" class="form-input" placeholder="Assente, Lieve, Moderata, Elevata..." value="<?= htmlspecialchars($existingAnamnesis['ansia'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Umore</label>
                        <input type="text" name="umore" class="form-input" placeholder="Stabile, Variabile..." value="<?= htmlspecialchars($existingAnamnesis['umore'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Motivazione</label>
                        <input type="text" name="motivazione" class="form-input" placeholder="Alta, Bassa..." value="<?= htmlspecialchars($existingAnamnesis['motivazione'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Concentrazione</label>
                    <input type="text" name="concentrazione" class="form-input" placeholder="Buona, Scarsa..." value="<?= htmlspecialchars($existingAnamnesis['concentrazione'] ?? '') ?>">
                </div>

                <!-- Stile di Vita - Attività Fisica -->
                <div class="card-header" style="margin-top: 30px;">STILE DI VITA – ATTIVITÀ FISICA</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Frequenza</label>
                        <input type="text" name="attivita_fisica_frequenza" class="form-input" placeholder="Es: 3 volte a settimana" value="<?= htmlspecialchars($existingAnamnesis['attivita_fisica_frequenza'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipo</label>
                        <input type="text" name="attivita_fisica_tipo" class="form-input" placeholder="Es: Corsa, Palestra, Yoga..." value="<?= htmlspecialchars($existingAnamnesis['attivita_fisica_tipo'] ?? '') ?>">
                    </div>
                </div>

                <!-- Stile di Vita - Alimentazione -->
                <div class="card-header" style="margin-top: 30px;">STILE DI VITA – ALIMENTAZIONE</div>
                
                <div class="form-group">
                    <label class="form-label">Descrizione generale</label>
                    <textarea name="alimentazione_generale" class="form-textarea" rows="4" placeholder="Descrivi le abitudini alimentari del paziente..."><?= htmlspecialchars($existingAnamnesis['alimentazione_generale'] ?? '') ?></textarea>
                </div>

                <!-- Supporti Utilizzati -->
                <div class="card-header" style="margin-top: 30px;">SUPPORTI UTILIZZATI</div>
                
                <div class="form-group">
                    <label class="form-label">Farmaci - Categoria</label>
                    <input type="text" name="farmaci_categoria" class="form-input" placeholder="Es: Antinfiammatori, Antibiotici..." value="<?= htmlspecialchars($existingAnamnesis['farmaci_categoria'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Farmaci - Specifiche</label>
                    <textarea name="farmaci_specifiche" class="form-textarea" placeholder="Nomi farmaci e dosaggi..."><?= htmlspecialchars($existingAnamnesis['farmaci_specifiche'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Integratori - Categoria</label>
                    <input type="text" name="integratori_categoria" class="form-input" placeholder="Es: Vitamine, Minerali..." value="<?= htmlspecialchars($existingAnamnesis['integratori_categoria'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Integratori - Specifiche</label>
                    <textarea name="integratori_specifiche" class="form-textarea" placeholder="Nomi integratori e dosaggi..."><?= htmlspecialchars($existingAnamnesis['integratori_specifiche'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Rimedi naturali</label>
                    <textarea name="rimedi_naturali" class="form-textarea"><?= htmlspecialchars($existingAnamnesis['rimedi_naturali'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Terapie in corso</label>
                    <textarea name="terapie_corso" class="form-textarea"><?= htmlspecialchars($existingAnamnesis['terapie_corso'] ?? '') ?></textarea>
                </div>

                <!-- Osservazioni Finali -->
                <div class="card-header" style="margin-top: 30px;">OSSERVAZIONI FINALI</div>
                
                <div class="form-group">
                    <label class="form-label">Note e osservazioni</label>
                    <textarea name="osservazioni_finali" class="form-textarea" rows="6" placeholder="Note della naturologa..."><?= htmlspecialchars($existingAnamnesis['osservazioni_finali'] ?? '') ?></textarea>
                </div>

                <div class="form-row" style="margin-top: 30px;">
                <div class="flex gap-10">
                    <button type="submit" class="btn btn-primary">Salva Anamnesi</button>
                    <a href="paziente_dettaglio.php?id=<?= $pazienteId ?>" class="btn btn-outline">↶ Annulla</a>
                </div>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        document.getElementById('anamnesis-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const data = Object.fromEntries(formData);
                const result = await ajaxRequest('<?= $existingAnamnesis ? 'update_anamnesis' : 'save_anamnesis' ?>', data);
                
                if (result.success) {
                    showMessage('Anamnesi salvata con successo!', 'success');
                    setTimeout(() => {
                        window.location.href = 'paziente_dettaglio.php?id=<?= $pazienteId ?>';
                    }, 1500);
                }
            } catch (error) {
                showMessage('Errore nel salvataggio dell\'anamnesi', 'error');
            }
        });
    </script>
</body>
</html>

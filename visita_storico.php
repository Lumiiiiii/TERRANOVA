<?php
/**
 * Visualizzazione storico di una visita
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Visit.php';
require_once __DIR__ . '/includes/Anamnesis.php';

$visitaId = $_GET['id'] ?? 0;

if (!$visitaId) {
    header('Location: index.php');
    exit;
}

$visitManager = new Visit();
$anamnesisManager = new Anamnesis();

$visit = $visitManager->getVisit($visitaId);

if (!$visit) {
    header('Location: index.php');
    exit;
}

$anamnesis = $anamnesisManager->getAnamnesis($visitaId);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visita del <?= date('d/m/Y', strtotime($visit['data_visita'])) ?> - <?= htmlspecialchars($visit['nome_cognome']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <h1>🌿 Gestionale Naturologa</h1>
            <nav class="header-nav">
                <a href="index.php">Home</a>
                <a href="medicinali_gestione.php">Medicinali</a>
                <a href="paziente_dettaglio.php?id=<?= $visit['paziente_id'] ?>">← Torna al Paziente</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="card">
            <div class="flex-between flex-center" style="margin-bottom: 20px;">
                <h2 class="card-header" style="margin-bottom: 0;">📋 Dettaglio Visita</h2>
                <a href="export_pdf.php?type=visit&id=<?= $visitaId ?>" target="_blank" class="btn btn-primary btn-small">📄 Esporta PDF</a>
            </div>
            
            <div style="background: var(--primary-light); padding: 15px; border-radius: 8px; margin-bottom: 30px;">
                <p><strong>Paziente:</strong> <?= htmlspecialchars($visit['nome_cognome']) ?></p>
                <p><strong>Data Visita:</strong> <?= date('d/m/Y', strtotime($visit['data_visita'])) ?></p>
                <?php if ($visit['note_finali']): ?>
                    <p><strong>Note Finali:</strong> <?= nl2br(htmlspecialchars($visit['note_finali'])) ?></p>
                <?php endif; ?>
            </div>

            <?php if ($anamnesis): ?>
                <!-- Anamnesi Personale -->
                <div class="card-header">ANAMNESI PERSONALE</div>
                
                <div class="form-row">
                    <p><strong>Vomito:</strong> <?= $anamnesis['vomito'] ?? 'Non specificato' ?> 
                        <?= $anamnesis['vomito_dettagli'] ? '(' . htmlspecialchars($anamnesis['vomito_dettagli']) . ')' : '' ?></p>
                    <p><strong>Febbre:</strong> <?= $anamnesis['febbre'] ?? 'Non specificato' ?> 
                        <?= $anamnesis['febbre_dettagli'] ? '(' . htmlspecialchars($anamnesis['febbre_dettagli']) . ')' : '' ?></p>
                </div>

                <div class="form-row">
                    <p><strong>Flusso:</strong> <?= $anamnesis['flusso'] ?? 'Non specificato' ?> 
                        <?= $anamnesis['flusso_dettagli'] ? '(' . htmlspecialchars($anamnesis['flusso_dettagli']) . ')' : '' ?></p>
                    <p><strong>Alcol:</strong> <?= $anamnesis['alcol'] ?? 'Non specificato' ?> 
                        <?= $anamnesis['alcol_dettagli'] ? '(' . htmlspecialchars($anamnesis['alcol_dettagli']) . ')' : '' ?></p>
                </div>

                <?php if ($anamnesis['patologie']): ?>
                    <p><strong>Patologie:</strong> <?= nl2br(htmlspecialchars($anamnesis['patologie'])) ?></p>
                <?php endif; ?>

                <?php if ($anamnesis['interventi_chirurgici']): ?>
                    <p><strong>Interventi chirurgici:</strong> <?= nl2br(htmlspecialchars($anamnesis['interventi_chirurgici'])) ?></p>
                <?php endif; ?>

                <?php if ($anamnesis['fratture_traumi']): ?>
                    <p><strong>Fratture/Traumi:</strong> <?= nl2br(htmlspecialchars($anamnesis['fratture_traumi'])) ?></p>
                <?php endif; ?>

                <!-- Qualità del Sonno -->
                <div class="card-header" style="margin-top: 30px;">QUALITÀ DEL SONNO</div>
                
                <div class="form-row">
                    <p><strong>Qualità:</strong> <?= htmlspecialchars($anamnesis['qualita_sonno'] ?? 'Non specificato') ?></p>
                    <p><strong>Ore di sonno:</strong> <?= $anamnesis['ore_sonno'] ?? 'Non specificato' ?></p>
                </div>

                <div class="form-row">
                    <p><strong>Risvegli notturni:</strong> <?= $anamnesis['risvegli_notturni'] ? 'Sì' : 'No' ?></p>
                    <p><strong>Difficoltà ad addormentarsi:</strong> <?= $anamnesis['difficolta_addormentarsi'] ? 'Sì' : 'No' ?></p>
                </div>

                <?php if ($anamnesis['qualita_risveglio']): ?>
                    <p><strong>Qualità al risveglio:</strong> <?= htmlspecialchars($anamnesis['qualita_risveglio']) ?></p>
                <?php endif; ?>

                <!-- Livello di Stress -->
                <div class="card-header" style="margin-top: 30px;">LIVELLO DI STRESS</div>
                
                <p><strong>Livello (1-10):</strong> <?= $anamnesis['livello_stress'] ?? 'Non specificato' ?></p>

                <!-- Stato Psico-Fisico -->
                <div class="card-header" style="margin-top: 30px;">STATO PSICO-FISICO</div>
                
                <div class="form-row">
                    <p><strong>Appetito:</strong> <?= htmlspecialchars($anamnesis['appetito'] ?? 'Non specificato') ?></p>
                    <p><strong>Ansia:</strong> <?= htmlspecialchars($anamnesis['ansia'] ?? 'Non specificato') ?></p>
                </div>

                <div class="form-row">
                    <p><strong>Umore:</strong> <?= htmlspecialchars($anamnesis['umore'] ?? 'Non specificato') ?></p>
                    <p><strong>Motivazione:</strong> <?= htmlspecialchars($anamnesis['motivazione'] ?? 'Non specificato') ?></p>
                </div>

                <p><strong>Concentrazione:</strong> <?= htmlspecialchars($anamnesis['concentrazione'] ?? 'Non specificato') ?></p>

                <!-- Attività Fisica -->
                <div class="card-header" style="margin-top: 30px;">ATTIVITÀ FISICA</div>
                
                <div class="form-row">
                    <p><strong>Frequenza:</strong> <?= htmlspecialchars($anamnesis['attivita_fisica_frequenza'] ?? 'Non specificato') ?></p>
                    <p><strong>Tipo:</strong> <?= htmlspecialchars($anamnesis['attivita_fisica_tipo'] ?? 'Non specificato') ?></p>
                </div>

                <!-- Alimentazione -->
                <div class="card-header" style="margin-top: 30px;">ALIMENTAZIONE</div>
                
                <?php if ($anamnesis['alimentazione_generale']): ?>
                    <p><?= nl2br(htmlspecialchars($anamnesis['alimentazione_generale'])) ?></p>
                <?php else: ?>
                    <p>Non specificato</p>
                <?php endif; ?>

                <!-- Supporti Utilizzati -->
                <div class="card-header" style="margin-top: 30px;">SUPPORTI UTILIZZATI</div>
                
                <?php if ($anamnesis['farmaci_categoria'] || $anamnesis['farmaci_specifiche']): ?>
                    <p><strong>Farmaci:</strong></p>
                    <?php if ($anamnesis['farmaci_categoria']): ?>
                        <p>Categoria: <?= htmlspecialchars($anamnesis['farmaci_categoria']) ?></p>
                    <?php endif; ?>
                    <?php if ($anamnesis['farmaci_specifiche']): ?>
                        <p>Specifiche: <?= nl2br(htmlspecialchars($anamnesis['farmaci_specifiche'])) ?></p>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($anamnesis['integratori_categoria'] || $anamnesis['integratori_specifiche']): ?>
                    <p style="margin-top: 15px;"><strong>Integratori:</strong></p>
                    <?php if ($anamnesis['integratori_categoria']): ?>
                        <p>Categoria: <?= htmlspecialchars($anamnesis['integratori_categoria']) ?></p>
                    <?php endif; ?>
                    <?php if ($anamnesis['integratori_specifiche']): ?>
                        <p>Specifiche: <?= nl2br(htmlspecialchars($anamnesis['integratori_specifiche'])) ?></p>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($anamnesis['rimedi_naturali']): ?>
                    <p style="margin-top: 15px;"><strong>Rimedi naturali:</strong> <?= nl2br(htmlspecialchars($anamnesis['rimedi_naturali'])) ?></p>
                <?php endif; ?>

                <?php if ($anamnesis['terapie_corso']): ?>
                    <p style="margin-top: 15px;"><strong>Terapie in corso:</strong> <?= nl2br(htmlspecialchars($anamnesis['terapie_corso'])) ?></p>
                <?php endif; ?>

                <!-- Osservazioni Finali -->
                <?php if ($anamnesis['osservazioni_finali']): ?>
                    <div class="card-header" style="margin-top: 30px;">OSSERVAZIONI FINALI</div>
                    <p><?= nl2br(htmlspecialchars($anamnesis['osservazioni_finali'])) ?></p>
                <?php endif; ?>

                <div class="mt-20">
                    <a href="visita_anamnesi.php?visita_id=<?= $visitaId ?>" class="btn btn-secondary">✏️ Modifica Anamnesi</a>
                </div>

            <?php else: ?>
                <div class="alert alert-warning">
                    Anamnesi non ancora compilata per questa visita.
                </div>
                <a href="visita_anamnesi.php?visita_id=<?= $visitaId ?>" class="btn btn-primary">➕ Compila Anamnesi</a>
            <?php endif; ?>

            <div class="mt-20">
                <a href="paziente_dettaglio.php?id=<?= $visit['paziente_id'] ?>" class="btn btn-outline">
                    ← Torna al Paziente
                </a>
            </div>
        </div>
    </div>
</body>
</html>

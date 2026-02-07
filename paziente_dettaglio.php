<?php
/**
 * Pagina dettagli paziente con storico visite
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__. '/includes/Patient.php';
require_once __DIR__ . '/includes/Visit.php';
require_once __DIR__ . '/includes/FoodRestrictions.php';

$patientId = $_GET['id'] ?? 0;

if (!$patientId) {
    header('Location: index.php');
    exit;
}

$patientManager = new Patient();
$visitManager = new Visit();
$foodManager = new FoodRestrictions();

$patient = $patientManager->getPatient($patientId);

if (!$patient) {
    header('Location: index.php');
    exit;
}

$visitHistory = $visitManager->getVisitHistory($patientId);
$foodRestrictions = $foodManager->getFoodRestrictionsByCategory($patientId);
$totalVisits = count($visitHistory);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($patient['nome_cognome']) ?> - Gestionale Naturologa</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <h1>🌿 Gestionale Naturologa</h1>
            <nav class="header-nav">
                <a href="index.php">Home</a>
                <a href="paziente_nuovo.php">Nuovo Paziente</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Patient Info Card -->
        <div class="card">
            <div class="flex-between flex-center" style="margin-bottom: 20px;">
                <h2 class="card-header" style="margin-bottom: 0;">
                    👤 <?= htmlspecialchars($patient['nome_cognome']) ?>
                </h2>
                <div class="flex gap-10">
                    <button onclick="toggleEditMode()" class="btn btn-secondary btn-small">✏️ Modifica</button>
                    <a href="alimenti_gestione.php?paziente_id=<?= $patientId ?>" class="btn btn-outline btn-small">🍎 Alimenti</a>
                </div>
            </div>

            <div id="patient-display">
                <div class="form-row">
                    <p><strong>Data di Nascita:</strong> <?= $patient['data_nascita'] ? date('d/m/Y', strtotime($patient['data_nascita'])) : 'Non specificata' ?></p>
                    <p><strong>Età:</strong> <?= $patient['eta'] ?? 'N/D' ?> anni</p>
                </div>
                <div class="form-row">
                    <p><strong>Telefono:</strong> <?= htmlspecialchars($patient['telefono'] ?? 'Non specificato') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($patient['email'] ?? 'Non specificata') ?></p>
                </div>
                <p><strong>Indirizzo:</strong> <?= htmlspecialchars($patient['indirizzo'] ?? 'Non specificato') ?></p>
                <p><strong>Professione:</strong> <?= htmlspecialchars($patient['professione'] ?? 'Non specificata') ?></p>
            </div>

            <form id="patient-edit-form" style="display: none;">
                <input type="hidden" name="id" value="<?= $patientId ?>">
                
                <div class="form-group">
                    <label class="form-label">Nome e Cognome *</label>
                    <input type="text" name="nome_cognome" class="form-input" value="<?= htmlspecialchars($patient['nome_cognome']) ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Data di Nascita</label>
                        <input type="date" name="data_nascita" class="form-input" value="<?= $patient['data_nascita'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Telefono</label>
                        <input type="tel" name="telefono" class="form-input" value="<?= htmlspecialchars($patient['telefono'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($patient['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Indirizzo</label>
                    <textarea name="indirizzo" class="form-textarea" rows="2"><?= htmlspecialchars($patient['indirizzo'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Professione</label>
                    <input type="text" name="professione" class="form-input" value="<?= htmlspecialchars($patient['professione'] ?? '') ?>">
                </div>

                <div class="flex gap-10">
                    <button type="submit" class="btn btn-primary">💾 Salva Modifiche</button>
                    <button type="button" onclick="toggleEditMode()" class="btn btn-outline">↶ Annulla</button>
                </div>
            </form>
        </div>

        <!-- Visit History -->
        <div class="card">
            <div class="flex-between flex-center" style="margin-bottom: 20px;">
                <h3 class="card-header" style="margin-bottom: 0;">
                    📋 Storico Visite (<?= $totalVisits ?>)
                </h3>
                <a href="visita_anamnesi.php?paziente_id=<?= $patientId ?>" class="btn btn-primary">
                    ➕ Nuova Visita
                </a>
            </div>

            <div id="visit-history">
                <?php if (empty($visitHistory)): ?>
                    <div class="empty-state">
                        <p>Nessuna visita registrata</p>
                        <p style="margin-top: 20px;">
                            <a href="visita_anamnesi.php?paziente_id=<?= $patientId ?>" class="btn btn-primary">
                                Aggiungi Prima Visita
                            </a>
                        </p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Data Visita</th>
                                <th>Anamnesi</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visitHistory as $visit): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($visit['data_visita'])) ?></td>
                                    <td>
                                        <?php if ($visit['ha_anamnesi']): ?>
                                            <span class="badge badge-success">Compilata</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Non compilata</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="visita_storico.php?id=<?= $visit['id'] ?>" class="btn btn-small btn-outline">
                                            Visualizza
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Food Restrictions Summary -->
        <?php if (!empty($foodRestrictions)): ?>
            <div class="card">
                <h3 class="card-header">🍎 Alimenti da Evitare</h3>
                <p style="margin-bottom: 15px;">
                    Totale: <strong><?= $foodManager->countFoodRestrictions($patientId) ?></strong> alimenti
                    • <a href="alimenti_gestione.php?paziente_id=<?= $patientId ?>">Gestisci lista completa</a>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <script src="js/main.js"></script>
    <script>
        function toggleEditMode() {
            const displayDiv = document.getElementById('patient-display');
            const editForm = document.getElementById('patient-edit-form');
            
            if (displayDiv.style.display === 'none') {
                displayDiv.style.display = 'block';
                editForm.style.display = 'none';
            } else {
                displayDiv.style.display = 'none';
                editForm.style.display = 'block';
            }
        }

        document.getElementById('patient-edit-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const success = await updatePatient(<?= $patientId ?>, formData);
            
            if (success) {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        });
    </script>
</body>
</html>

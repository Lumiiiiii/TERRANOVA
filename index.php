<?php
/**
 * Homepage - Dashboard principale
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Patient.php';

$patientManager = new Patient();
$recentPatients = $patientManager->getRecentPatients(15);
$totalPatients = $patientManager->countPatients();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TerraNova - Gestionale Naturopatia</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <h1>TerraNova - Gestionale Naturopatia</h1>
            <nav class="header-nav">
                <a href="index.php">Home</a>
                <a href="paziente_nuovo.php">Nuovo Paziente</a>
                <a href="medicinali_gestione.php">Medicinali</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Quick Stats -->
        <div class="card">
            <div class="flex-between flex-center">
                <div>
                    <h2 class="card-header" style="margin-bottom: 10px;">Dashboard</h2>
                    <p style="color: var(--text-light);">Totale pazienti registrati: <strong><?= $totalPatients ?></strong></p>
                </div>
                <a href="paziente_nuovo.php" class="btn btn-primary">Aggiungi Paziente</a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="card">
            <h3 class="card-header">Cerca Paziente</h3>
            <div class="search-bar">
                <input 
                    type="text" 
                    id="search-input" 
                    class="search-input" 
                    placeholder="Cerca per nome, telefono o email..."
                    autocomplete="off"
                >
                <span class="search-icon">🔍</span>
            </div>
        </div>

        <!-- Patient List -->
        <div class="card">
            <h3 class="card-header">Pazienti Recenti</h3>
            <div id="patients-list">
                <?php if (empty($recentPatients)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">User</div>
                        <p>Nessun paziente registrato</p>
                        <p style="margin-top: 20px;">
                            <a href="paziente_nuovo.php" class="btn btn-primary">Aggiungi il primo paziente</a>
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentPatients as $patient): ?>
                        <div class="patient-list-item" onclick="window.location.href='paziente_dettaglio.php?id=<?= $patient['id'] ?>'">
                            <div class="patient-info">
                                <h3><?= htmlspecialchars($patient['nome_cognome']) ?></h3>
                                <p>
                                    <?php if ($patient['eta']): ?>
                                        <?= $patient['eta'] ?> anni
                                    <?php endif; ?>
                                    <?php if ($patient['telefono']): ?>
                                        • Tel: <?= htmlspecialchars($patient['telefono']) ?>
                                    <?php endif; ?>
                                    <?php if ($patient['email']): ?>
                                        • <?= htmlspecialchars($patient['email']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div>
                                <span class="badge badge-info">Visualizza</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        // Carica pazienti recenti al caricamento della pagina
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            searchInput.addEventListener('input', function(e) {
                searchPatients(e.target.value);
            });
        });
    </script>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
/**
 * Dettaglio Paziente - Bootstrap 5
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
    <title><?= htmlspecialchars($patient['nome_cognome']) ?> - TerraNova</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-light glass sticky-top px-3 py-2">
        <div class="container-xl">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="index.php">
                <div class="avatar-circle"
                    style="background-color:var(--color-primary); color:white; font-size:.85rem;">TN</div>
                <?= htmlspecialchars($patient['nome_cognome']) ?>
            </a>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">Dashboard</a>
        </div>
    </nav>

    <div class="container-xl py-4">
        <div class="row g-4">

            <!-- Colonna Sinistra: Profilo -->
            <div class="col-lg-4">
                <div class="card glass-card border-0 text-center animate-fade-in">
                    <div class="avatar-circle-lg mx-auto mb-3">
                        <?= strtoupper(substr($patient['nome_cognome'], 0, 1)) ?>
                    </div>
                    <h2 class="h5 fw-bold"><?= htmlspecialchars($patient['nome_cognome']) ?></h2>
                    <p class="text-muted small mb-3">
                        <?= htmlspecialchars($patient['professione'] ?? 'Professione non indicata') ?>
                    </p>

                    <a href="visita_anamnesi.php?paziente_id=<?= $id ?>" class="btn btn-primary btn-sm mb-4">+ Nuova
                        Visita</a>

                    <hr>
                    <div class="text-start small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Età</span>
                            <span class="fw-semibold"><?= $patient['eta'] ?? '-' ?> anni</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Telefono</span>
                            <span class="fw-semibold"><?= htmlspecialchars($patient['telefono'] ?? '-') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Email</span>
                            <span class="fw-semibold"><?= htmlspecialchars($patient['email'] ?? '-') ?></span>
                        </div>
                        <div class="mt-2">
                            <span class="text-muted d-block"
                                style="font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;">Indirizzo</span>
                            <span class="fw-semibold"><?= htmlspecialchars($patient['indirizzo'] ?? '-') ?></span>
                        </div>
                    </div>

                    <button class="btn btn-link btn-sm text-muted mt-3 p-0" data-bs-toggle="modal"
                        data-bs-target="#editModal">
                        Modifica Dati
                    </button>
                </div>
            </div>

            <!-- Colonna Destra: Storico Visite -->
            <div class="col-lg-8">
                <div class="card glass-card border-0 animate-fade-in delay-100">
                    <h3 class="h5 fw-bold border-bottom pb-2 mb-4">Storico Visite</h3>

                    <?php if (empty($visits)): ?>
                        <p class="text-muted text-center py-4">Nessuna visita registrata.</p>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($visits as $v): ?>
                                <div
                                    class="card border rounded-3 p-3 d-flex flex-row justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            Visita del <?= date('d/m/Y', strtotime($v['data_visita'])) ?>
                                        </h6>
                                        <p class="text-muted small mb-0"
                                            style="max-width:400px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            <?= htmlspecialchars($v['note_finali'] ?? '') ?>
                                        </p>
                                    </div>
                                    <a href="visita_anamnesi.php?visita_id=<?= $v['id'] ?>&paziente_id=<?= $id ?>"
                                        class="btn btn-primary btn-sm ms-3 text-nowrap">
                                        Vedi / Modifica
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Modifica -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Modifica Paziente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-form">
                        <input type="hidden" name="action" value="update_patient">
                        <input type="hidden" name="id" value="<?= $id ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nome e Cognome</label>
                            <input type="text" name="nome_cognome" class="form-control"
                                value="<?= htmlspecialchars($patient['nome_cognome']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Data di Nascita</label>
                            <input type="date" name="data_nascita" class="form-control"
                                value="<?= $patient['data_nascita'] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Telefono</label>
                            <input type="tel" name="telefono" class="form-control"
                                value="<?= htmlspecialchars($patient['telefono'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($patient['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Professione</label>
                            <input type="text" name="professione" class="form-control"
                                value="<?= htmlspecialchars($patient['professione'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Indirizzo</label>
                            <textarea name="indirizzo"
                                class="form-control"><?= htmlspecialchars($patient['indirizzo'] ?? '') ?></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-primary" id="save-edit-btn">Salva</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('save-edit-btn').addEventListener('click', async function () {
            const form = document.getElementById('edit-form');
            const formData = new FormData(form);
            try {
                const res = await fetch('ajax_handlers.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) location.reload();
            } catch (e) { console.error(e); }
        });
    </script>
</body>

</html>
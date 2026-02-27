<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
/**
 * Form per aggiungere un nuovo paziente
 */
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Paziente - TerraNova</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-light glass sticky-top px-3 py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="index.php">
                <div class="avatar-circle"
                    style="background-color:var(--color-primary); color:white; font-size:.85rem;">TN</div>
                Nuovo Paziente
            </a>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">Torna alla Home</a>
        </div>
    </nav>

    <div class="container py-5" style="max-width: 700px;">
        <h1 class="fw-bold mb-4">Registrazione Paziente</h1>

        <div class="card glass-card animate-fade-in border-0">
            <form id="patient-form">
                <input type="hidden" name="action" value="create_patient">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome e Cognome *</label>
                    <input type="text" name="nome_cognome" class="form-control" required placeholder="Es: Mario Rossi">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Data di Nascita</label>
                        <input type="date" name="data_nascita" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Telefono</label>
                        <input type="tel" name="telefono" class="form-control" placeholder="Es: 333 1234567">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Es: mario.rossi@email.com">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Indirizzo</label>
                    <textarea name="indirizzo" class="form-control" rows="2" placeholder="Es: Via Roma 123"></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Professione</label>
                    <input type="text" name="professione" class="form-control" placeholder="Es: Impiegato">
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="index.php" class="btn btn-outline-secondary">Annulla</a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Crea Paziente</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        document.getElementById('patient-form').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('ajax_handlers.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    window.location.href = 'paziente_dettaglio.php?id=' + result.id;
                } else {
                    alert('Errore: ' + (result.error || 'Sconosciuto'));
                }
            } catch (e) { console.error(e); alert('Errore di connessione'); }
        });
    </script>
</body>

</html>
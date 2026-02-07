<?php
/**
 * Gestione alimenti da evitare per paziente
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Patient.php';
require_once __DIR__ . '/includes/FoodRestrictions.php';

$pazienteId = $_GET['paziente_id'] ?? 0;

if (!$pazienteId) {
    header('Location: index.php');
    exit;
}

$patientManager = new Patient();
$foodManager = new FoodRestrictions();

$patient = $patientManager->getPatient($pazienteId);

if (!$patient) {
    header('Location: index.php');
    exit;
}

$foodRestrictions = $foodManager->getFoodRestrictionsByCategory($pazienteId);
$categories = $foodManager->getAllCategories();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alimenti da Evitare - <?= htmlspecialchars($patient['nome_cognome']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <h1>🌿 Gestionale Naturologa</h1>
            <nav class="header-nav">
                <a href="index.php">Home</a>
                <a href="paziente_dettaglio.php?id=<?= $pazienteId ?>">← Torna al Paziente</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="card">
            <h2 class="card-header">🍎 Alimenti da Evitare</h2>
            
            <div style="background: var(--primary-light); padding: 15px; border-radius: 8px; margin-bottom: 30px;">
                <p><strong>Paziente:</strong> <?= htmlspecialchars($patient['nome_cognome']) ?></p>
                <p><strong>Totale alimenti:</strong> <?= $foodManager->countFoodRestrictions($pazienteId) ?></p>
            </div>

            <!-- Add New Food -->
            <div class="card-header">Aggiungi Nuovo Alimento</div>
            
            <form id="add-food-form" style="background: var(--bg-light); padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Categoria</label>
                        <select name="categoria" class="form-select" required>
                            <option value="">Seleziona categoria...</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['nome']) ?>">
                                    <?= htmlspecialchars($category['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="Altro">Altro...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alimento</label>
                        <input type="text" name="alimento" class="form-input" placeholder="Nome dell'alimento" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">➕ Aggiungi Alimento</button>
            </form>

            <!-- Food List by Category -->
            <div class="card-header">Lista Alimenti</div>
            
            <div id="food-list">
                <?php if (empty($foodRestrictions)): ?>
                    <div class="empty-state">
                        <p>Nessun alimento da evitare registrato</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($foodRestrictions as $categoria => $foods): ?>
                        <div class="food-category">
                            <h4><?= htmlspecialchars($categoria) ?></h4>
                            <?php foreach ($foods as $food): ?>
                                <div class="food-item">
                                    <span><?= htmlspecialchars($food['alimento']) ?></span>
                                    <button 
                                        class="btn btn-danger btn-small" 
                                        onclick="removeFood(<?= $food['id'] ?>)"
                                    >
                                        ✕ Rimuovi
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mt-20">
                <a href="paziente_dettaglio.php?id=<?= $pazienteId ?>" class="btn btn-outline">
                    ← Torna al Paziente
                </a>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        const pazienteId = <?= $pazienteId ?>;

        document.getElementById('add-food-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const categoria = formData.get('categoria');
            const alimento = formData.get('alimento');
            
            const success = await addFoodRestriction(pazienteId, categoria, alimento);
            
            if (success) {
                this.reset();
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        });

        async function removeFood(restrictionId) {
            const success = await removeFoodRestriction(restrictionId);
            
            if (success) {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        }
    </script>
</body>
</html>

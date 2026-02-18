<?php
/**
 * Gestione alimenti da evitare per paziente - Refactored with Tailwind CSS
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
$totalFoods = $foodManager->countFoodRestrictions($pazienteId);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alimenti - <?= htmlspecialchars($patient['nome_cognome']) ?> - TerraNova</title>
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
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="index.php" class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-green-400 flex items-center justify-center text-white font-bold">TN</a>
                <div class="flex flex-col">
                    <span class="text-xs text-gray-500 font-medium">Gestionale Naturopatia</span>
                    <h1 class="text-sm font-bold text-gray-800 leading-none">Alimenti da Evitare</h1>
                </div>
            </div>
            <div>
                 <a href="paziente_dettaglio.php?id=<?= $pazienteId ?>" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Torna al Paziente
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 pb-20">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Alimentazione</h1>
                <p class="text-gray-500">Paziente: <span class="font-semibold text-primary"><?= htmlspecialchars($patient['nome_cognome']) ?></span></p>
            </div>
            <div class="bg-red-50 text-red-600 px-4 py-2 rounded-lg border border-red-100 font-medium flex items-center gap-2">
                <span class="text-xl">🚫</span>
                <span><?= $totalFoods ?> Alimenti da evitare</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Left Column: Add Food Form -->
            <div class="md:col-span-1">
                <div class="glass-card sticky top-24 animate-fade-in delay-100">
                    <h2 class="text-lg font-bold text-primary mb-4 border-b border-gray-100 pb-2">➕ Aggiungi Alimento</h2>
                    
                    <form id="add-food-form" class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-700">Categoria</label>
                            <select name="categoria" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm bg-white" required>
                                <option value="">Seleziona...</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['nome']) ?>">
                                        <?= htmlspecialchars($category['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="Altro">Altro...</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-700">Alimento</label>
                            <input type="text" name="alimento" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" placeholder="Es: Pomodori" required>
                        </div>

                        <button type="submit" class="w-full bg-primary text-white font-bold py-2 rounded-lg shadow-md hover:bg-green-700 transition-colors">Aggiungi</button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Food List -->
            <div class="md:col-span-2">
                <div class="glass-card animate-fade-in delay-200 min-h-[400px]">
                    <h2 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-2">Lista Alimenti Vietati</h2>

                    <div id="food-list" class="space-y-6">
                        <?php if (empty($foodRestrictions)): ?>
                            <div class="text-center py-10">
                                <span class="text-4xl block mb-2">🥗</span>
                                <p class="text-gray-500">Nessun alimento registrato.</p>
                                <p class="text-xs text-gray-400 mt-1">Usa il modulo a sinistra per aggiungere.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($foodRestrictions as $categoria => $foods): ?>
                                <div class="food-category relative">
                                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3 pl-2 border-l-4 border-secondary/50"><?= htmlspecialchars($categoria) ?></h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <?php foreach ($foods as $food): ?>
                                            <div class="group flex justify-between items-center bg-white/50 p-3 rounded-lg border border-gray-100 hover:shadow-sm transition-all hover:bg-white">
                                                <span class="font-medium text-gray-800"><?= htmlspecialchars($food['alimento']) ?></span>
                                                <button 
                                                    class="text-gray-300 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50"
                                                    onclick="removeFood(<?= $food['id'] ?>)"
                                                    title="Rimuovi"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
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
            
            // Add subtle loading state
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            btn.innerText = 'Aggiungendo...';
            btn.disabled = true;

            const success = await addFoodRestriction(pazienteId, categoria, alimento);
            
            if (success) {
                location.reload();
            } else {
                btn.innerText = originalText;
                btn.disabled = false;
                alert('Errore durante l\'aggiunta.');
            }
        });

        async function removeFood(restrictionId) {
            if(!confirm('Rimuovere questo alimento dalla lista?')) return;
            
            const success = await removeFoodRestriction(restrictionId);
            if (success) {
                location.reload();
            }
        }
    </script>
</body>
</html>

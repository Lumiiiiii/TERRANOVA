<?php
/**
 * Pagina gestione medicinali - Catalogo prodotti prescrivibili - Refactored with Tailwind CSS
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Medicine.php';

$medicineManager = new Medicine();
$medicines = $medicineManager->getAllMedicines(true); // Include anche inattivi
$countByType = $medicineManager->countByType();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicinali - TerraNova</title>
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
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="index.php" class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-green-400 flex items-center justify-center text-white font-bold">TN</a>
                <div class="flex flex-col">
                    <span class="text-xs text-gray-500 font-medium">Gestionale Naturopatia</span>
                    <h1 class="text-sm font-bold text-gray-800 leading-none">Catalogo Medicinali</h1>
                </div>
            </div>
            <div>
                 <a href="index.php" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Torna alla Home
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 pb-20">
        
        <!-- Header & Stats -->
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4 animate-fade-in">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestione Medicinali</h1>
                <p class="text-gray-500">Catalogo completo di omeopatici, fitoterapici e integratori.</p>
            </div>
            
            <div class="flex gap-2">
                <?php foreach ($countByType as $stat): ?>
                    <div class="bg-white/80 backdrop-blur px-3 py-1 rounded-lg border border-gray-100 shadow-sm text-xs font-medium text-gray-600">
                        <span class="text-primary font-bold"><?= $stat['count'] ?></span> <?= htmlspecialchars($stat['tipologia']) ?>
                    </div>
                <?php endforeach; ?>
                <div class="bg-primary/10 px-3 py-1 rounded-lg border border-primary/20 shadow-sm text-xs font-medium text-primary">
                    <span class="font-bold"><?= count($medicines) ?></span> Totali
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Column: Add Medicine Form (4 cols) -->
            <div class="lg:col-span-4">
                <div class="glass-card sticky top-24 animate-fade-in delay-100">
                    <h2 class="text-lg font-bold text-primary mb-4 border-b border-gray-100 pb-2">➕ Aggiungi Medicinale</h2>
                    <form id="add-medicine-form" class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-700">Nome Medicinale *</label>
                            <input type="text" name="nome" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" required placeholder="Es: Arnica Montana">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-700">Tipologia *</label>
                                <select name="tipologia" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm bg-white" required>
                                    <option value="Omeopatico">Omeopatico</option>
                                    <option value="Fitoterapico">Fitoterapico</option>
                                    <option value="Integratore">Integratore</option>
                                    <option value="Altro">Altro</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-700">Formato</label>
                                <input type="text" name="formato" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" placeholder="Es: Gocce">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-700">Dosaggio Standard</label>
                            <input type="text" name="dosaggio_standard" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" placeholder="Es: 5g 3x/die">
                        </div>
        
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-700">Note</label>
                            <textarea name="note" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm" rows="2" placeholder="Indicazioni opzionali..."></textarea>
                        </div>
        
                        <button type="submit" class="w-full bg-primary text-white font-bold py-2 rounded-lg shadow-md hover:bg-green-700 transition-colors">Salva Medicinale</button>
                    </form>
                </div>
            </div>

            <!-- Right Column: List & Filters (8 cols) -->
            <div class="lg:col-span-8">
                
                <!-- Filters -->
                <div class="glass flex flex-col md:flex-row gap-4 p-4 rounded-xl mb-6 animate-fade-in delay-200">
                    <div class="flex-1">
                        <input type="text" id="search-medicine" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm bg-white/50" placeholder="🔍 Cerca medicinale...">
                    </div>
                    <div>
                        <select id="filter-type" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm bg-white/50">
                            <option value="">Tutte le tipologie</option>
                            <option value="Omeopatico">Omeopatici</option>
                            <option value="Fitoterapico">Fitoterapici</option>
                            <option value="Integratore">Integratori</option>
                            <option value="Altro">Altri</option>
                        </select>
                    </div>
                    <div>
                        <select id="filter-status" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm bg-white/50">
                            <option value="all">Tutti</option>
                            <option value="active" selected>Solo attivi</option>
                            <option value="inactive">Solo inattivi</option>
                        </select>
                    </div>
                </div>

                <!-- List -->
                <div class="glass-card animate-fade-in delay-300 min-h-[500px]">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pl-2 border-l-4 border-secondary">Elenco Prodotti</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3">Nome</th>
                                    <th class="px-4 py-3">Tipologia</th>
                                    <th class="px-4 py-3">Formato</th>
                                    <th class="px-4 py-3">Dosaggio Std</th>
                                    <th class="px-4 py-3 text-center">Stato</th>
                                    <th class="px-4 py-3 text-right">Azioni</th>
                                </tr>
                            </thead>
                            <tbody id="medicine-table-body" class="divide-y divide-gray-100">
                                <?php foreach ($medicines as $medicine): ?>
                                    <tr class="medicine-row hover:bg-gray-50/50 transition-colors group" 
                                        data-id="<?= $medicine['id'] ?>"
                                        data-type="<?= htmlspecialchars($medicine['tipologia']) ?>"
                                        data-status="<?= $medicine['attivo'] ? 'active' : 'inactive' ?>"
                                        data-name="<?= htmlspecialchars(strtolower($medicine['nome'])) ?>">
                                        <td class="px-4 py-3 font-medium text-gray-900"><?= htmlspecialchars($medicine['nome']) ?></td>
                                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($medicine['tipologia']) ?></td>
                                        <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($medicine['formato'] ?? '-') ?></td>
                                        <td class="px-4 py-3 text-gray-500 truncate max-w-[150px]" title="<?= htmlspecialchars($medicine['dosaggio_standard'] ?? '') ?>"><?= htmlspecialchars($medicine['dosaggio_standard'] ?? '-') ?></td>
                                        <td class="px-4 py-3 text-center">
                                            <?php if ($medicine['attivo']): ?>
                                                <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">Attivo</span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">Inattivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-right flex justify-end gap-2">
                                            <button onclick="editMedicine(<?= $medicine['id'] ?>)" class="text-gray-400 hover:text-primary transition-colors p-1" title="Modifica">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                            <?php if ($medicine['attivo']): ?>
                                                <button onclick="toggleMedicine(<?= $medicine['id'] ?>, 0)" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Disattiva">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                </button>
                                            <?php else: ?>
                                                <button onclick="toggleMedicine(<?= $medicine['id'] ?>, 1)" class="text-gray-400 hover:text-green-500 transition-colors p-1" title="Riattiva">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        // Form aggiunta medicinale
        document.getElementById('add-medicine-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // UI Feedback
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            btn.innerText = 'Salvataggio...';
            btn.disabled = true;

            const success = await addMedicine(formData);
            if (success) {
                this.reset();
                setTimeout(() => location.reload(), 1000);
            } else {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        });

        // Ricerca
        document.getElementById('search-medicine').addEventListener('input', filterMedicines);
        document.getElementById('filter-type').addEventListener('change', filterMedicines);
        document.getElementById('filter-status').addEventListener('change', filterMedicines);

        function filterMedicines() {
            const searchTerm = document.getElementById('search-medicine').value.toLowerCase();
            const filterType = document.getElementById('filter-type').value;
            const filterStatus = document.getElementById('filter-status').value;
            
            const rows = document.querySelectorAll('.medicine-row');
            rows.forEach(row => {
                const name = row.dataset.name;
                const type = row.dataset.type;
                const status = row.dataset.status;
                
                let show = true;
                
                if (searchTerm && !name.includes(searchTerm)) {
                    show = false;
                }
                
                if (filterType && type !== filterType) {
                    show = false;
                }
                
                if (filterStatus === 'active' && status !== 'active') {
                    show = false;
                } else if (filterStatus === 'inactive' && status !== 'inactive') {
                    show = false;
                }
                
                row.style.display = show ? '' : 'none';
            });
        }

        async function editMedicine(id) {
            // TODO: Implementare modal di modifica
            // Per ora un semplice prompt o alert
            alert('Funzione modifica in sviluppo. ID: ' + id);
        }

        async function toggleMedicine(id, newStatus) {
            const action = newStatus === 1 ? 'update_medicine' : 'delete_medicine';
            const formData = new FormData();
            formData.append('id', id);
            
            if (action === 'update_medicine') {
                // Riattiva
                const response = await fetch('ajax_handlers.php?action=reactivate_medicine', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    // showNotification('Medicinale riattivato', 'success'); // if available
                    location.reload();
                }
            } else {
                // Disattiva
                if (confirm('Disattivare questo medicinale?')) {
                    const success = await deleteMedicine(id);
                    if (success) {
                        location.reload();
                    }
                }
            }
        }

        // Applica filtro iniziale
        filterMedicines();
    </script>
</body>
</html>

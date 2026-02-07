<?php
/**
 * Pagina gestione medicinali - Catalogo prodotti prescrivibili
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
    <title>Gestione Medicinali - TerraNova</title>
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
                <a href="medicinali_gestione.php" class="active">Medicinali</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Statistiche -->
        <div class="card">
            <h2 class="card-header">Gestione Medicinali</h2>
            <div class="form-row">
                <?php foreach ($countByType as $stat): ?>
                    <div class="info-box">
                        <div class="info-label"><?= htmlspecialchars($stat['tipologia']) ?></div>
                        <div class="info-value"><?= $stat['count'] ?></div>
                    </div>
                <?php endforeach; ?>
                <div class="info-box">
                    <div class="info-label">Totale</div>
                    <div class="info-value"><?= count($medicines) ?></div>
                </div>
            </div>
        </div>

        <!-- Form Nuovo Medicinale -->
        <div class="card">
            <h3 class="card-header">➕ Aggiungi Nuovo Medicinale</h3>
            <form id="add-medicine-form">
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label class="form-label">Nome Medicinale *</label>
                        <input type="text" name="nome" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipologia *</label>
                        <select name="tipologia" class="form-input" required>
                            <option value="Omeopatico">Omeopatico</option>
                            <option value="Fitoterapico">Fitoterapico</option>
                            <option value="Integratore">Integratore</option>
                            <option value="Altro">Altro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Formato</label>
                        <input type="text" name="formato" class="form-input" placeholder="es: Granuli, Gocce...">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dosaggio Standard</label>
                    <input type="text" name="dosaggio_standard" class="form-input" placeholder="es: 5 granuli 3 volte al giorno">
                </div>

                <div class="form-group">
                    <label class="form-label">Note</label>
                    <textarea name="note" class="form-textarea" rows="2" placeholder="Indicazioni, controindicazioni, etc."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">💾 Aggiungi Medicinale</button>
            </form>
        </div>

        <!-- Filtri -->
        <div class="card">
            <div class="flex-between flex-center" style="gap: 15px;">
                <div class="form-group" style="margin: 0; flex: 1;">
                    <input type="text" id="search-medicine" class="form-input" placeholder="🔍 Cerca medicinale per nome...">
                </div>
                <div class="form-group" style="margin: 0;">
                    <select id="filter-type" class="form-input">
                        <option value="">Tutte le tipologie</option>
                        <option value="Omeopatico">Omeopatici</option>
                        <option value="Fitoterapico">Fitoterapici</option>
                        <option value="Integratore">Integratori</option>
                        <option value="Altro">Altri</option>
                    </select>
                </div>
                <div class="form-group" style="margin: 0;">
                    <select id="filter-status" class="form-input">
                        <option value="all">Tutti</option>
                        <option value="active" selected>Solo attivi</option>
                        <option value="inactive">Solo inattivi</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Lista Medicinali -->
        <div class="card">
            <h3 class="card-header">📋 Elenco Medicinali</h3>
            <div id="medicine-list">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tipologia</th>
                            <th>Formato</th>
                            <th>Dosaggio Standard</th>
                            <th>Stato</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody id="medicine-table-body">
                        <?php foreach ($medicines as $medicine): ?>
                            <tr class="medicine-row" 
                                data-id="<?= $medicine['id'] ?>"
                                data-type="<?= htmlspecialchars($medicine['tipologia']) ?>"
                                data-status="<?= $medicine['attivo'] ? 'active' : 'inactive' ?>"
                                data-name="<?= htmlspecialchars(strtolower($medicine['nome'])) ?>">
                                <td><strong><?= htmlspecialchars($medicine['nome']) ?></strong></td>
                                <td><?= htmlspecialchars($medicine['tipologia']) ?></td>
                                <td><?= htmlspecialchars($medicine['formato'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($medicine['dosaggio_standard'] ?? '-') ?></td>
                                <td>
                                    <?php if ($medicine['attivo']): ?>
                                        <span class="badge badge-success">Attivo</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Disattivato</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button onclick="editMedicine(<?= $medicine['id'] ?>)" class="btn btn-small btn-outline">✏️</button>
                                    <?php if ($medicine['attivo']): ?>
                                        <button onclick="toggleMedicine(<?= $medicine['id'] ?>, 0)" class="btn btn-small btn-secondary">🔴</button>
                                    <?php else: ?>
                                        <button onclick="toggleMedicine(<?= $medicine['id'] ?>, 1)" class="btn btn-small btn-primary">🟢</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        // Form aggiunta medicinale
        document.getElementById('add-medicine-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const success = await addMedicine(formData);
            if (success) {
                this.reset();
                setTimeout(() => location.reload(), 1000);
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
                    showNotification('Medicinale riattivato', 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                // Disattiva
                if (confirm('Disattivare questo medicinale?')) {
                    const success = await deleteMedicine(id);
                    if (success) {
                        setTimeout(() => location.reload(), 1000);
                    }
                }
            }
        }

        // Applica filtro iniziale
        filterMedicines();
    </script>
</body>
</html>

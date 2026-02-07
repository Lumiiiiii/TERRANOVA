/**
 * JavaScript principale per il Gestionale Naturologa
 */

// Configurazione
const API_URL = 'ajax_handlers.php';

// Utility: mostra messaggio di feedback
function showMessage(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;

    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);

    setTimeout(() => {
        alertDiv.remove();
    }, 4000);
}

// Utility: formatta data per visualizzazione
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

// Utility: richiesta AJAX
async function ajaxRequest(action, data = {}, method = 'POST') {
    try {
        const payload = { action, ...data };
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        };

        if (method === 'POST') {
            options.body = new URLSearchParams(payload);
        } else {
            const queryString = new URLSearchParams(payload).toString();
            return fetch(`${API_URL}?${queryString}`, { method: 'GET' });
        }

        const response = await fetch(API_URL, options);
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.error || 'Errore sconosciuto');
        }

        return result;
    } catch (error) {
        console.error('Errore AJAX:', error);
        showMessage('Errore di comunicazione con il server', 'error');
        throw error;
    }
}

// Ricerca pazienti con debounce
let searchTimeout;
function searchPatients(query) {
    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(async () => {
        if (query.length < 2) {
            loadRecentPatients();
            return;
        }

        try {
            const result = await ajaxRequest('search_patients', { query }, 'GET');
            displayPatients(result.data);
        } catch (error) {
            console.error('Errore nella ricerca:', error);
        }
    }, 300);
}

// Carica pazienti recenti
async function loadRecentPatients() {
    try {
        const result = await ajaxRequest('get_recent_patients', {}, 'GET');
        displayPatients(result.data);
    } catch (error) {
        console.error('Errore nel caricamento pazienti:', error);
    }
}

// Visualizza lista pazienti
function displayPatients(patients) {
    const listContainer = document.getElementById('patients-list');

    if (!listContainer) return;

    if (patients.length === 0) {
        listContainer.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">👤</div>
                <p>Nessun paziente trovato</p>
            </div>
        `;
        return;
    }

    listContainer.innerHTML = patients.map(patient => `
        <div class="patient-list-item" onclick="window.location.href='paziente_dettaglio.php?id=${patient.id}'">
            <div class="patient-info">
                <h3>${patient.nome_cognome}</h3>
                <p>
                    ${patient.eta ? `${patient.eta} anni` : ''} 
                    ${patient.telefono ? `• Tel: ${patient.telefono}` : ''}
                    ${patient.email ? `• ${patient.email}` : ''}
                </p>
            </div>
            <div>
                <span class="badge badge-info">Visualizza</span>
            </div>
        </div>
    `).join('');
}

// Validazione form paziente
function validatePatientForm(formData) {
    if (!formData.get('nome_cognome') || formData.get('nome_cognome').trim() === '') {
        showMessage('Il nome e cognome sono obbligatori', 'error');
        return false;
    }
    return true;
}

// Salva nuovo paziente
async function savePatient(formData, redirectToDetail = true) {
    if (!validatePatientForm(formData)) {
        return false;
    }

    try {
        const data = Object.fromEntries(formData);
        const result = await ajaxRequest('create_patient', data);

        if (result.success) {
            showMessage('Paziente aggiunto con successo!', 'success');

            if (redirectToDetail && result.id) {
                setTimeout(() => {
                    window.location.href = `paziente_dettaglio.php?id=${result.id}`;
                }, 1000);
            }

            return result.id;
        }
    } catch (error) {
        showMessage('Errore nel salvataggio del paziente', 'error');
        return false;
    }
}

// Aggiorna paziente esistente
async function updatePatient(patientId, formData) {
    if (!validatePatientForm(formData)) {
        return false;
    }

    try {
        const data = Object.fromEntries(formData);
        data.id = patientId;

        const result = await ajaxRequest('update_patient', data);

        if (result.success) {
            showMessage('Paziente aggiornato con successo!', 'success');
            return true;
        }
    } catch (error) {
        showMessage('Errore nell\'aggiornamento del paziente', 'error');
        return false;
    }
}

// Elimina paziente
async function deletePatient(patientId) {
    if (!confirm('Sei sicuro di voler eliminare questo paziente? Verranno eliminate anche tutte le visite associate.')) {
        return false;
    }

    try {
        const result = await ajaxRequest('delete_patient', { id: patientId });

        if (result.success) {
            showMessage('Paziente eliminato con successo', 'success');
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 1000);
            return true;
        }
    } catch (error) {
        showMessage('Errore nell\'eliminazione del paziente', 'error');
        return false;
    }
}

// Carica storico visite
async function loadVisitHistory(patientId) {
    try {
        const result = await ajaxRequest('get_visit_history', { paziente_id: patientId }, 'GET');
        displayVisitHistory(result.data);
    } catch (error) {
        console.error('Errore nel caricamento dello storico:', error);
    }
}

// Visualizza storico visite
function displayVisitHistory(visits) {
    const historyContainer = document.getElementById('visit-history');

    if (!historyContainer) return;

    if (visits.length === 0) {
        historyContainer.innerHTML = `
            <div class="empty-state">
                <p>Nessuna visita registrata per questo paziente</p>
            </div>
        `;
        return;
    }

    historyContainer.innerHTML = `
        <table class="table">
            <thead>
                <tr>
                    <th>Data Visita</th>
                    <th>Anamnesi</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                ${visits.map(visit => `
                    <tr>
                        <td>${formatDate(visit.data_visita)}</td>
                        <td>
                            ${visit.ha_anamnesi ?
            '<span class="badge badge-success">Compilata</span>' :
            '<span class="badge badge-warning">Non compilata</span>'}
                        </td>
                        <td>
                            <a href="visita_storico.php?id=${visit.id}" class="btn btn-small btn-outline">
                                Visualizza
                            </a>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

// Salva anamnesi
async function saveAnamnesis(visitId, formData) {
    try {
        const data = Object.fromEntries(formData);
        data.visita_id = visitId;

        const result = await ajaxRequest('save_anamnesis', data);

        if (result.success) {
            showMessage('Anamnesi salvata con successo!', 'success');
            return true;
        }
    } catch (error) {
        showMessage('Errore nel salvataggio dell\'anamnesi', 'error');
        return false;
    }
}

// Gestione alimenti da evitare
async function addFoodRestriction(patientId, categoria, alimento) {
    if (!alimento || alimento.trim() === '') {
        showMessage('Inserisci il nome dell\'alimento', 'error');
        return false;
    }

    try {
        const result = await ajaxRequest('add_food_restriction', {
            paziente_id: patientId,
            categoria: categoria,
            alimento: alimento
        });

        if (result.success) {
            showMessage('Alimento aggiunto', 'success');
            return true;
        }
    } catch (error) {
        showMessage('Errore nell\'aggiunta dell\'alimento', 'error');
        return false;
    }
}

async function removeFoodRestriction(restrictionId) {
    if (!confirm('Rimuovere questo alimento dalla lista?')) {
        return false;
    }

    try {
        const result = await ajaxRequest('remove_food_restriction', { id: restrictionId });

        if (result.success) {
            showMessage('Alimento rimosso', 'success');
            return true;
        }
    } catch (error) {
        showMessage('Errore nella rimozione', 'error');
        return false;
    }
}

// ============================================
// MEDICINALI
// ============================================

async function addMedicine(formData) {
    try {
        const data = Object.fromEntries(formData);
        const result = await ajaxRequest('add_medicine', data);

        if (result.success) {
            showMessage('Medicinale aggiunto con successo!', 'success');
            return true;
        }
    } catch (error) {
        showMessage('Errore nell\'aggiunta del medicinale', 'error');
        return false;
    }
}

async function updateMedicine(medicineId, formData) {
    try {
        const data = Object.fromEntries(formData);
        data.id = medicineId;
        const result = await ajaxRequest('update_medicine', data);

        if (result.success) {
            showMessage('Medicinale aggiornato!', 'success');
            return true;
        }
    } catch (error) {
        showMessage('Errore nell\'aggiornamento', 'error');
        return false;
    }
}

async function deleteMedicine(medicineId) {
    try {
        const result = await ajaxRequest('delete_medicine', { id: medicineId });

        if (result.success) {
            showMessage('Medicinale disattivato', 'success');
            return true;
        }
    } catch (error) {
        showMessage('Errore nella disattivazione', 'error');
        return false;
    }
}

// ============================================
// PRESCRIZIONI
// ============================================

async function addPrescription(formData) {
    try {
        const data = Object.fromEntries(formData);
        const result = await ajaxRequest('add_prescription', data);

        if (result.success) {
            showMessage('Prescrizione aggiunta con successo!', 'success');
            return true;
        }
    } catch (error) {
        showMessage('Errore nell\'aggiunta della prescrizione', 'error');
        return false;
    }
}

async function updatePrescription(prescriptionId, formData) {
    try {
        const data = Object.fromEntries(formData);
        data.id = prescriptionId;
        const result = await ajaxRequest('update_prescription', data);

        if (result.success) {
            showMessage('Prescrizione aggiornata!', 'success');
            return true;
        }
    } catch (error) {
        showMessage('Errore nell\'aggiornamento', 'error');
        return false;
    }
}

async function endPrescription(prescriptionId) {
    try {
        const result = await ajaxRequest('end_prescription', { id: prescriptionId });

        if (result.success) {
            showMessage('Prescrizione terminata', 'success');
            return true;
        }
    } catch (error) {
        showMessage('Errore', 'error');
        return false;
    }
}

async function deletePrescription(prescriptionId) {
    if (!confirm('Eliminare definitivamente questa prescrizione?')) {
        return false;
    }

    try {
        const result = await ajaxRequest('delete_prescription', { id: prescriptionId });

        if (result.success) {
            showMessage('Prescrizione eliminata', 'success');
            return true;
        }
    } catch (error) {
        showMessage('Errore nell\'eliminazione', 'error');
        return false;
    }
}

// Utility: mostra notifica
function showNotification(message, type = 'success') {
    showMessage(message, type);
}

// Event listeners globali
document.addEventListener('DOMContentLoaded', function () {
    // Search input
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchPatients(e.target.value);
        });
    }
});

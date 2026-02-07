<?php
/**
 * AJAX Handlers per tutte le operazioni del gestionale
 */

header('Content-Type: application/json');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Patient.php';
require_once __DIR__ . '/includes/Visit.php';
require_once __DIR__ . '/includes/Anamnesis.php';
require_once __DIR__ . '/includes/FoodRestrictions.php';
require_once __DIR__ . '/includes/Medicine.php';
require_once __DIR__ . '/includes/Prescription.php';

// Inizializza le classi
$patientManager = new Patient();
$visitManager = new Visit();
$anamnesisManager = new Anamnesis();
$foodManager = new FoodRestrictions();
$medicineManager = new Medicine();
$prescriptionManager = new Prescription();

// Ottieni l'azione richiesta
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        // ============================================
        // PAZIENTI
        // ============================================
        case 'create_patient':
            $result = $patientManager->createPatient($_POST);
            echo json_encode(['success' => (bool)$result, 'id' => $result]);
            break;
            
        case 'get_patient':
            $patient = $patientManager->getPatient($_GET['id']);
            echo json_encode(['success' => (bool)$patient, 'data' => $patient]);
            break;
            
        case 'update_patient':
            $result = $patientManager->updatePatient($_POST['id'], $_POST);
            echo json_encode(['success' => (bool)$result]);
            break;
            
        case 'delete_patient':
            $result = $patientManager->deletePatient($_POST['id']);
            echo json_encode(['success' => (bool)$result]);
            break;
            
        case 'search_patients':
            $patients = $patientManager->searchPatients($_GET['query'] ?? '');
            echo json_encode(['success' => true, 'data' => $patients]);
            break;
            
        case 'get_all_patients':
            $patients = $patientManager->getAllPatients();
            echo json_encode(['success' => true, 'data' => $patients]);
            break;
            
        case 'get_recent_patients':
            $patients = $patientManager->getRecentPatients();
            echo json_encode(['success' => true, 'data' => $patients]);
            break;
            
        // ============================================
        // VISITE
        // ============================================
        case 'create_visit':
            $visitId = $visitManager->createVisit(
                $_POST['paziente_id'],
                $_POST['data_visita'] ?? null,
                $_POST['note_finali'] ?? null
            );
            echo json_encode(['success' => (bool)$visitId, 'id' => $visitId]);
            break;
            
        case 'get_visit':
            $visit = $visitManager->getVisit($_GET['id']);
            echo json_encode(['success' => (bool)$visit, 'data' => $visit]);
            break;
            
        case 'update_visit':
            $result = $visitManager->updateVisit(
                $_POST['id'],
                $_POST['data_visita'],
                $_POST['note_finali']
            );
            echo json_encode(['success' => (bool)$result]);
            break;
            
        case 'delete_visit':
            $result = $visitManager->deleteVisit($_POST['id']);
            echo json_encode(['success' => (bool)$result]);
            break;
            
        case 'get_visit_history':
            $visits = $visitManager->getVisitHistory($_GET['paziente_id']);
            echo json_encode(['success' => true, 'data' => $visits]);
            break;
            
        // ============================================
        // ANAMNESI
        // ============================================
        case 'save_anamnesis':
            $result = $anamnesisManager->saveAnamnesis($_POST['visita_id'], $_POST);
            echo json_encode(['success' => (bool)$result, 'id' => $result]);
            break;
            
        case 'get_anamnesis':
            $anamnesis = $anamnesisManager->getAnamnesis($_GET['visita_id']);
            echo json_encode(['success' => (bool)$anamnesis, 'data' => $anamnesis]);
            break;
            
        case 'update_anamnesis':
            $result = $anamnesisManager->updateAnamnesis($_POST['visita_id'], $_POST);
            echo json_encode(['success' => (bool)$result]);
            break;
            
        case 'has_anamnesis':
            $hasAnamnesis = $anamnesisManager->hasAnamnesis($_GET['visita_id']);
            echo json_encode(['success' => true, 'has_anamnesis' => $hasAnamnesis]);
            break;
            
        // ============================================
        // ALIMENTI DA EVITARE
        // ============================================
        case 'add_food_restriction':
            $result = $foodManager->addFoodRestriction(
                $_POST['paziente_id'],
                $_POST['categoria'],
                $_POST['alimento']
            );
            echo json_encode(['success' => (bool)$result, 'id' => $result]);
            break;
            
        case 'get_food_restrictions':
            $foods = $foodManager->getFoodRestrictions($_GET['paziente_id']);
            echo json_encode(['success' => true, 'data' => $foods]);
            break;
            
        case 'get_food_restrictions_by_category':
            $foods = $foodManager->getFoodRestrictionsByCategory($_GET['paziente_id']);
            echo json_encode(['success' => true, 'data' => $foods]);
            break;
            
        case 'remove_food_restriction':
            $result = $foodManager->removeFoodRestriction($_POST['id']);
            echo json_encode(['success' => (bool)$result]);
            break;
            
        case 'delete_food_restriction':
            $result = $foodManager->deleteFoodRestriction($_POST['id']);
            echo json_encode(['success' => (bool)$result]);
            break;
            
        case 'get_all_categories':
            $categories = $foodManager->getAllCategories();
            echo json_encode(['success' => true, 'data' => $categories]);
            break;
            
        // ============================================
        // MEDICINALI
        // ============================================
        case 'get_all_medicines':
            $medicines = $medicineManager->getAllMedicines();
            echo json_encode(['success' => true, 'data' => $medicines]);
            break;
            
        case 'get_medicine':
            $medicine = $medicineManager->getMedicineById($_GET['id']);
            echo json_encode(['success' => (bool)$medicine, 'data' => $medicine]);
            break;
            
        case 'search_medicines':
            $medicines = $medicineManager->searchMedicines($_GET['query'] ?? '');
            echo json_encode(['success' => true, 'data' => $medicines]);
            break;
            
        case 'add_medicine':
            $result = $medicineManager->addMedicine($_POST);
            echo json_encode(['success' => (bool)$result, 'id' => $result]);
            break;
            
        case 'update_medicine':
            $result = $medicineManager->updateMedicine($_POST['id'], $_POST);
            echo json_encode(['success' => (bool)$result]);
            break;
            
        case 'delete_medicine':
            $result = $medicineManager->deleteMedicine($_POST['id']);
            echo json_encode(['success' => (bool)$result]);
            break;
            
        case 'reactivate_medicine':
            $result = $medicineManager->reactivateMedicine($_POST['id']);
            echo json_encode(['success' => (bool)$result]);
            break;
            
        // ============================================
        // PRESCRIZIONI
        // ============================================
        case 'get_prescriptions_by_patient':
            $activeOnly = ($_GET['active_only'] ?? '0') === '1';
            $prescriptions = $prescriptionManager->getPrescriptionsByPatient(
                $_GET['paziente_id'],
                $activeOnly
            );
            echo json_encode(['success' => true, 'data' => $prescriptions]);
            break;
            
        case 'get_prescriptions_by_visit':
            $prescriptions = $prescriptionManager->getPrescriptionsByVisit($_GET['visita_id']);
            echo json_encode(['success' => true, 'data' => $prescriptions]);
            break;
            
        case 'add_prescription':
            $result = $prescriptionManager->addPrescription($_POST);
            echo json_encode(['success' => (bool)$result, 'id' => $result]);
            break;
            
        case 'update_prescription':
            $result = $prescriptionManager->updatePrescription($_POST['id'], $_POST);
            echo json_encode(['success' => (bool)$result]);
            break;
            
        case 'end_prescription':
            $result = $prescriptionManager->endPrescription(
                $_POST['id'],
                $_POST['data_fine'] ?? null
            );
            echo json_encode(['success' => (bool)$result]);
            break;
            
        case 'delete_prescription':
            $result = $prescriptionManager->deletePrescription($_POST['id']);
            echo json_encode(['success' => (bool)$result]);
            break;
            
        case 'reactivate_prescription':
            $result = $prescriptionManager->reactivatePrescription($_POST['id']);
            echo json_encode(['success' => (bool)$result]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Azione non valida']);
            break;
    }
} catch (Exception $e) {
    error_log("Errore AJAX: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

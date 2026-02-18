<?php
/**
 * AJAX Handlers - Semplificato
 */
header('Content-Type: application/json');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Patient.php';
require_once __DIR__ . '/includes/Visit.php';
require_once __DIR__ . '/includes/Anamnesis.php';
require_once __DIR__ . '/includes/FoodRestrictions.php';
require_once __DIR__ . '/includes/Medicine.php';
require_once __DIR__ . '/includes/Prescription.php';

$patientManager = new Patient();
$visitManager = new Visit();
$anamnesisManager = new Anamnesis();
$foodManager = new FoodRestrictions();
$medicineManager = new Medicine();
$prescriptionManager = new Prescription();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        // PAZIENTI
        case 'create_patient':
            $result = $patientManager->createPatient($_POST);
            echo json_encode(['success' => (bool) $result, 'id' => $result]);
            break;
        case 'get_patient':
            echo json_encode(['success' => true, 'data' => $patientManager->getPatient($_GET['id'])]);
            break;
        case 'update_patient':
            echo json_encode(['success' => $patientManager->updatePatient($_POST['id'], $_POST)]);
            break;
        case 'delete_patient':
            echo json_encode(['success' => $patientManager->deletePatient($_POST['id'])]);
            break;
        case 'search_patients':
            echo json_encode(['success' => true, 'data' => $patientManager->searchPatients($_GET['query'] ?? '')]);
            break;
        case 'get_recent_patients':
            echo json_encode(['success' => true, 'data' => $patientManager->getRecentPatients()]);
            break;

        // VISITE E ANAMNESI
        case 'create_visit':
            $id = $visitManager->createVisit($_POST['paziente_id']);
            echo json_encode(['success' => (bool) $id, 'id' => $id]);
            break;
        case 'get_visit':
            echo json_encode(['success' => true, 'data' => $visitManager->getVisit($_GET['id'])]);
            break;
        case 'update_visit': // Generic update for visit data
            echo json_encode(['success' => $visitManager->updateVisit($_POST['id'], $_POST)]);
            break;
        case 'get_visit_history':
            echo json_encode(['success' => true, 'data' => $visitManager->getVisitHistory($_GET['paziente_id'])]);
            break;

        // ANAMNESI GENERALE
        case 'save_general_anamnesis':
            $result = $anamnesisManager->saveAnamnesis($_POST['paziente_id'], $_POST);
            echo json_encode(['success' => $result]);
            break;
        case 'get_general_anamnesis':
            echo json_encode(['success' => true, 'data' => $anamnesisManager->getAnamnesis($_GET['paziente_id'])]);
            break;

        // MEDICINALI
        case 'get_all_medicines':
            echo json_encode(['success' => true, 'data' => $medicineManager->getAllMedicines()]);
            break;
        case 'add_medicine':
            echo json_encode(['success' => (bool) $medicineManager->createMedicine($_POST)]);
            break;
        case 'update_medicine':
            echo json_encode(['success' => $medicineManager->updateMedicine($_POST['id'], $_POST)]);
            break;
        case 'delete_medicine':
            echo json_encode(['success' => $medicineManager->deleteMedicine($_POST['id'])]);
            break;
        case 'search_medicines':
            // Implement if needed, or just use get_all in frontend filtering
            echo json_encode(['success' => true, 'data' => []]);
            break;

        // PRESCRIZIONI
        case 'get_prescriptions_by_patient':
            $active = ($_GET['active_only'] ?? '0') == '1';
            echo json_encode(['success' => true, 'data' => $prescriptionManager->getPrescriptionsByPatient($_GET['paziente_id'], $active)]);
            break;
        case 'add_prescription':
            echo json_encode(['success' => $prescriptionManager->createPrescription($_POST)]);
            break;
        case 'end_prescription':
            echo json_encode(['success' => $prescriptionManager->stopPrescription($_POST['id'])]);
            break;

        // ALIMENTI
        case 'get_food_restrictions':
            echo json_encode(['success' => true, 'data' => $foodManager->getRestrictions($_GET['paziente_id'])]);
            break;
        case 'get_all_foods':
            echo json_encode(['success' => true, 'data' => $foodManager->getAllFoods()]);
            break;
        case 'add_food_restriction':
            echo json_encode(['success' => $foodManager->addRestriction($_POST['paziente_id'], $_POST['alimento_id'])]);
            break;
        case 'remove_food_restriction':
            echo json_encode(['success' => $foodManager->removeRestriction($_POST['id'])]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Azione non valida']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

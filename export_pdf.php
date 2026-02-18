<?php
/**
 * Controller per esportazioni PDF
 */

require_once __DIR__ . '/includes/PDFExporter.php';

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? 0;
$patientId = $_GET['patient_id'] ?? 0;

$exporter = new PDFExporter();

switch ($type) {
    case 'patient':
        if ($id) {
            echo $exporter->generatePatientReport($id);
        } else {
            echo "ID paziente mancante";
        }
        break;
        
    case 'visit':
        if ($id) {
            echo $exporter->generateVisitReport($id);
        } else {
            echo "ID visita mancante";
        }
        break;
        
    case 'prescriptions':
        if ($patientId) {
            echo $exporter->generatePrescriptionList($patientId);
        } else {
            echo "ID paziente mancante";
        }
        break;
        
    default:
        echo "Tipo di export non valido";
        break;
}

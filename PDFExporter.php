<?php
/**
 * Classe PDFExporter - Generazione PDF dei report
 * Usa una soluzione HTML semplice che può essere stampata come PDF dal browser
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Patient.php';
require_once __DIR__ . '/Visit.php';
require_once __DIR__ . '/Anamnesis.php';
require_once __DIR__ . '/FoodRestrictions.php';
require_once __DIR__ . '/Prescription.php';

class PDFExporter {
    private $patient;
    private $visit;
    private $anamnesis;
    private $food;
    private $prescription;
    
    public function __construct() {
        $this->patient = new Patient();
        $this->visit = new Visit();
        $this->anamnesis = new Anamnesis();
        $this->food = new FoodRestrictions();
        $this->prescription = new Prescription();
    }
    
    /**
     * Genera HTML ottimizzato per la stampa in PDF
     */
    private function generatePrintableHTML($title, $content) {
        return <<<HTML
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        @media print {
            @page { margin: 2cm; }
            body { margin: 0; }
            .no-print { display: none; }
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2c7a4f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2c7a4f;
            margin: 0;
            font-size: 28px;
        }
        
        .header p {
            color: #666;
            margin: 5px 0;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: #2c7a4f;
            color: white;
            padding: 8px 12px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .info-item {
            padding: 8px;
            background: #f5f5f5;
        }
        
        .info-label {
            font-weight: bold;
            color: #2c7a4f;
            display: block;
            margin-bottom: 3px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #2c7a4f;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .badge-active {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #2c7a4f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .print-button:hover {
            background: #235c3d;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Stampa / Salva PDF</button>
    {$content}
    <div class="footer">
        <p>Documento generato il {$this->formatDate(date('Y-m-d'))} - TerraNova Gestionale</p>
    </div>
    <script>
        // Auto-apri dialogo di stampa se richiesto
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('auto_print') === '1') {
            window.onload = function() {
                setTimeout(() => window.print(), 500);
            };
        }
    </script>
</body>
</html>
HTML;
    }
    
    /**
     * Formatta una data in formato italiano
     */
    private function formatDate($date) {
        if (!$date) return 'N/D';
        return date('d/m/Y', strtotime($date));
    }
    
    /**
     * Genera report completo paziente
     */
    public function generatePatientReport($patientId) {
        $patient = $this->patient->getPatient($patientId);
        if (!$patient) {
            return "Paziente non trovato";
        }
        
        $visits = $this->visit->getVisitHistory($patientId);
        $prescriptions = $this->prescription->getPrescriptionsByPatient($patientId);
        $foodRestrictions = $this->food->getFoodRestrictionsByCategory($patientId);
        
        $content = '<div class="header">';
        $content .= '<h1>Report Paziente</h1>';
        $content .= '<p><strong>' . htmlspecialchars($patient['nome_cognome']) . '</strong></p>';
        $content .= '</div>';
        
        // Anagrafica
        $content .= '<div class="section">';
        $content .= '<div class="section-title">📋 Dati Anagrafici</div>';
        $content .= '<div class="info-grid">';
        $content .= '<div class="info-item"><span class="info-label">Nome e Cognome:</span> ' . htmlspecialchars($patient['nome_cognome']) . '</div>';
        $content .= '<div class="info-item"><span class="info-label">Data di Nascita:</span> ' . $this->formatDate($patient['data_nascita']) . '</div>';
        $content .= '<div class="info-item"><span class="info-label">Età:</span> ' . ($patient['eta'] ?? 'N/D') . ' anni</div>';
        $content .= '<div class="info-item"><span class="info-label">Telefono:</span> ' . htmlspecialchars($patient['telefono'] ?? 'Non specificato') . '</div>';
        $content .= '<div class="info-item"><span class="info-label">Email:</span> ' . htmlspecialchars($patient['email'] ?? 'Non specificata') . '</div>';
        $content .= '<div class="info-item"><span class="info-label">Professione:</span> ' . htmlspecialchars($patient['professione'] ?? 'Non specificata') . '</div>';
        $content .= '</div>';
        if ($patient['indirizzo']) {
            $content .= '<div class="info-item" style="margin-top: 10px;"><span class="info-label">Indirizzo:</span> ' . htmlspecialchars($patient['indirizzo']) . '</div>';
        }
        $content .= '</div>';
        
        // Prescrizioni Attive
        $activePrescriptions = array_filter($prescriptions, fn($p) => $p['attivo']);
        if (!empty($activePrescriptions)) {
            $content .= '<div class="section">';
            $content .= '<div class="section-title">💊 Prescrizioni Attive (' . count($activePrescriptions) . ')</div>';
            $content .= '<table>';
            $content .= '<tr><th>Medicinale</th><th>Tipologia</th><th>Dosaggio</th><th>Frequenza</th><th>Dal</th></tr>';
            foreach ($activePrescriptions as $pr) {
                $content .= '<tr>';
                $content .= '<td><strong>' . htmlspecialchars($pr['medicinale_nome']) . '</strong></td>';
                $content .= '<td>' . htmlspecialchars($pr['medicinale_tipologia']) . '</td>';
                $content .= '<td>' . htmlspecialchars($pr['dosaggio'] ?? '-') . '</td>';
                $content .= '<td>' . htmlspecialchars($pr['frequenza'] ?? '-') . '</td>';
                $content .= '<td>' . $this->formatDate($pr['data_inizio']) . '</td>';
                $content .= '</tr>';
            }
            $content .= '</table>';
            $content .= '</div>';
        }
        
        // Storico Visite
        if (!empty($visits)) {
            $content .= '<div class="section">';
            $content .= '<div class="section-title">📅 Storico Visite (' . count($visits) . ')</div>';
            $content .= '<table>';
            $content .= '<tr><th>Data</th><th>Anamnesi</th><th>Note</th></tr>';
            foreach ($visits as $visit) {
                $content .= '<tr>';
                $content .= '<td>' . $this->formatDate($visit['data_visita']) . '</td>';
                $content .= '<td>';
                if ($visit['ha_anamnesi']) {
                    $content .= '<span class="badge badge-active">Compilata</span>';
                } else {
                    $content .= '<span class="badge badge-inactive">Non compilata</span>';
                }
                $content .= '</td>';
                $content .= '<td>' . htmlspecialchars(substr($visit['note_finali'] ?? 'Nessuna nota', 0, 100)) . '</td>';
                $content .= '</tr>';
            }
            $content .= '</table>';
            $content .= '</div>';
        }
        
        // Alimenti da Evitare
        if (!empty($foodRestrictions)) {
            $content .= '<div class="section">';
            $totalFoods = 0;
            foreach ($foodRestrictions as $foods) {
                $totalFoods += count($foods);
            }
            $content .= '<div class="section-title">Alimenti da Evitare (' . $totalFoods . ')</div>';
            foreach ($foodRestrictions as $category => $foods) {
                if (!empty($foods)) {
                    $content .= '<p><strong>' . htmlspecialchars($category) . ':</strong> ';
                    $foodNames = array_map(fn($f) => htmlspecialchars($f['alimento']), $foods);
                    $content .= implode(', ', $foodNames);
                    $content .= '</p>';
                }
            }
            $content .= '</div>';
        }
        
        return $this->generatePrintableHTML(
            'Report ' . $patient['nome_cognome'],
            $content
        );
    }
    
    /**
     * Genera report singola visita
     */
    public function generateVisitReport($visitId) {
        $visit = $this->visit->getVisit($visitId);
        if (!$visit) {
            return "Visita non trovata";
        }
        
        $patient = $this->patient->getPatient($visit['paziente_id']);
        $anamnesis = $this->anamnesis->getAnamnesis($visitId);
        $prescriptions = $this->prescription->getPrescriptionsByVisit($visitId);
        
        $content = '<div class="header">';
        $content .= '<h1>📋 Report Visita</h1>';
        $content .= '<p><strong>' . htmlspecialchars($patient['nome_cognome']) . '</strong></p>';
        $content .= '<p>Visita del ' . $this->formatDate($visit['data_visita']) . '</p>';
        $content .= '</div>';
        
        // Informazioni Visita
        $content .= '<div class="section">';
        $content .= '<div class="section-title">ℹ️ Informazioni Visita</div>';
        $content .= '<div class="info-grid">';
        $content .= '<div class="info-item"><span class="info-label">Data Visita:</span> ' . $this->formatDate($visit['data_visita']) . '</div>';
        $content .= '<div class="info-item"><span class="info-label">Paziente:</span> ' . htmlspecialchars($patient['nome_cognome']) . '</div>';
        $content .= '</div>';
        if ($visit['note_finali']) {
            $content .= '<div class="info-item" style="margin-top: 10px;"><span class="info-label">Note Finali:</span><br>' . nl2br(htmlspecialchars($visit['note_finali'])) . '</div>';
        }
        $content .= '</div>';
        
        // Anamnesi (se presente)
        if ($anamnesis) {
            $content .= '<div class="section">';
            $content .= '<div class="section-title">🩺 Anamnesi</div>';
            
            if ($anamnesis['livello_stress']) {
                $content .= '<p><strong>Livello Stress:</strong> ' . $anamnesis['livello_stress'] . '/10</p>';
            }
            
            if ($anamnesis['qualita_sonno'] || $anamnesis['ore_sonno']) {
                $content .= '<p><strong>Sonno:</strong> Qualità: ' . htmlspecialchars($anamnesis['qualita_sonno'] ?? 'N/D') . ' - Ore: ' . ($anamnesis['ore_sonno'] ?? 'N/D') . '</p>';
            }
            
            if ($anamnesis['alimentazione_generale']) {
                $content .= '<p><strong>Alimentazione:</strong> ' . nl2br(htmlspecialchars($anamnesis['alimentazione_generale'])) . '</p>';
            }
            
            if ($anamnesis['osservazioni_finali']) {
                $content .= '<p><strong>Osservazioni:</strong> ' . nl2br(htmlspecialchars($anamnesis['osservazioni_finali'])) . '</p>';
            }
            
            $content .= '</div>';
        }
        
        // Prescrizioni della visita
        if (!empty($prescriptions)) {
            $content .= '<div class="section">';
            $content .= '<div class="section-title">Prescrizioni (' . count($prescriptions) . ')</div>';
            $content .= '<table>';
            $content .= '<tr><th>Medicinale</th><th>Dosaggio</th><th>Frequenza</th><th>Durata</th></tr>';
            foreach ($prescriptions as $pr) {
                $content .= '<tr>';
                $content .= '<td><strong>' . htmlspecialchars($pr['medicinale_nome']) . '</strong><br><small>' . htmlspecialchars($pr['medicinale_tipologia']) . '</small></td>';
                $content .= '<td>' . htmlspecialchars($pr['dosaggio'] ?? '-') . '</td>';
                $content .= '<td>' . htmlspecialchars($pr['frequenza'] ?? '-') . '</td>';
                $content .= '<td>' . htmlspecialchars($pr['durata'] ?? '-') . '</td>';
                $content .= '</tr>';
            }
            $content .= '</table>';
            $content .= '</div>';
        }
        
        return $this->generatePrintableHTML(
            'Visita ' . $this->formatDate($visit['data_visita']),
            $content
        );
    }
    
    /**
     * Genera lista prescrizioni attive di un paziente
     */
    public function generatePrescriptionList($patientId) {
        $patient = $this->patient->getPatient($patientId);
        if (!$patient) {
            return "Paziente non trovato";
        }
        
        $prescriptions = $this->prescription->getPrescriptionsByPatient($patientId, true);
        
        $content = '<div class="header">';
        $content .= '<h1>Piano Terapeutico</h1>';
        $content .= '<p><strong>' . htmlspecialchars($patient['nome_cognome']) . '</strong></p>';
        $content .= '</div>';
        
        if (empty($prescriptions)) {
            $content .= '<p style="text-align: center; padding: 40px; color: #666;">Nessuna prescrizione attiva</p>';
        } else {
            $content .= '<div class="section">';
            $content .= '<div class="section-title">Prescrizioni Attive (' . count($prescriptions) . ')</div>';
            $content .= '<table>';
            $content .= '<tr><th>Medicinale</th><th>Tipologia / Formato</th><th>Dosaggio</th><th>Frequenza</th><th>Durata</th><th>Note</th></tr>';
            foreach ($prescriptions as $pr) {
                $content .= '<tr>';
                $content .= '<td><strong>' . htmlspecialchars($pr['medicinale_nome']) . '</strong></td>';
                $content .= '<td>' . htmlspecialchars($pr['medicinale_tipologia']) . '<br><small>' . htmlspecialchars($pr['medicinale_formato'] ?? '') . '</small></td>';
                $content .= '<td>' . htmlspecialchars($pr['dosaggio'] ?? $pr['dosaggio_standard'] ?? '-') . '</td>';
                $content .= '<td>' . htmlspecialchars($pr['frequenza'] ?? '-') . '</td>';
                $content .= '<td>' . htmlspecialchars($pr['durata'] ?? '-') . '</td>';
                $content .= '<td>' . htmlspecialchars($pr['note_prescrizione'] ?? '-') . '</td>';
                $content .= '</tr>';
            }
            $content .= '</table>';
            $content .= '</div>';
        }
        
        return $this->generatePrintableHTML(
            'Piano Terapeutico ' . $patient['nome_cognome'],
            $content
        );
    }
}

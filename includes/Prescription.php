<?php
/**
 * Classe Prescription - Gestione prescrizioni
 */

require_once __DIR__ . '/../config/database.php';

class Prescription {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Ottiene le prescrizioni di un paziente
     */
    public function getPrescriptionsByPatient($patientId, $activeOnly = false) {
        try {
            $sql = "SELECT * FROM vista_prescrizioni_complete 
                    WHERE paziente_id = :paziente_id";
            
            if ($activeOnly) {
                $sql .= " AND attivo = 1";
            }
            
            $sql .= " ORDER BY attivo DESC, data_inizio DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':paziente_id' => $patientId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore recupero prescrizioni paziente: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Ottiene le prescrizioni di una visita specifica
     */
    public function getPrescriptionsByVisit($visitId) {
        try {
            $sql = "SELECT * FROM vista_prescrizioni_complete 
                    WHERE visita_id = :visita_id
                    ORDER BY data_inizio DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':visita_id' => $visitId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore recupero prescrizioni visita: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Ottiene una singola prescrizione
     */
    public function getPrescriptionById($id) {
        try {
            $sql = "SELECT * FROM vista_prescrizioni_complete WHERE prescrizione_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Errore recupero prescrizione: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Aggiunge una nuova prescrizione
     */
    public function addPrescription($data) {
        try {
            $sql = "INSERT INTO prescrizioni 
                    (paziente_id, visita_id, medicinale_id, dosaggio, frequenza, durata, 
                     note_prescrizione, data_inizio, data_fine, attivo) 
                    VALUES 
                    (:paziente_id, :visita_id, :medicinale_id, :dosaggio, :frequenza, :durata,
                     :note_prescrizione, :data_inizio, :data_fine, :attivo)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':paziente_id' => $data['paziente_id'],
                ':visita_id' => $data['visita_id'] ?? null,
                ':medicinale_id' => $data['medicinale_id'],
                ':dosaggio' => $data['dosaggio'] ?? null,
                ':frequenza' => $data['frequenza'] ?? null,
                ':durata' => $data['durata'] ?? null,
                ':note_prescrizione' => $data['note_prescrizione'] ?? null,
                ':data_inizio' => $data['data_inizio'] ?? date('Y-m-d'),
                ':data_fine' => $data['data_fine'] ?? null,
                ':attivo' => $data['attivo'] ?? 1
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Errore aggiunta prescrizione: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Aggiorna una prescrizione esistente
     */
    public function updatePrescription($id, $data) {
        try {
            $sql = "UPDATE prescrizioni 
                    SET medicinale_id = :medicinale_id,
                        dosaggio = :dosaggio,
                        frequenza = :frequenza,
                        durata = :durata,
                        note_prescrizione = :note_prescrizione,
                        data_inizio = :data_inizio,
                        data_fine = :data_fine,
                        attivo = :attivo
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':medicinale_id' => $data['medicinale_id'],
                ':dosaggio' => $data['dosaggio'] ?? null,
                ':frequenza' => $data['frequenza'] ?? null,
                ':durata' => $data['durata'] ?? null,
                ':note_prescrizione' => $data['note_prescrizione'] ?? null,
                ':data_inizio' => $data['data_inizio'],
                ':data_fine' => $data['data_fine'] ?? null,
                ':attivo' => $data['attivo'] ?? 1
            ]);
        } catch (PDOException $e) {
            error_log("Errore aggiornamento prescrizione: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Termina una prescrizione (imposta data fine e disattiva)
     */
    public function endPrescription($id, $endDate = null) {
        try {
            $sql = "UPDATE prescrizioni 
                    SET data_fine = :data_fine, attivo = 0 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':data_fine' => $endDate ?? date('Y-m-d')
            ]);
        } catch (PDOException $e) {
            error_log("Errore terminazione prescrizione: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina una prescrizione
     */
    public function deletePrescription($id) {
        try {
            $sql = "DELETE FROM prescrizioni WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Errore eliminazione prescrizione: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Conta le prescrizioni attive di un paziente
     */
    public function countActivePrescriptions($patientId) {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM prescrizioni 
                    WHERE paziente_id = :paziente_id AND attivo = 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':paziente_id' => $patientId]);
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Errore conteggio prescrizioni: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Riattiva una prescrizione
     */
    public function reactivatePrescription($id) {
        try {
            $sql = "UPDATE prescrizioni 
                    SET attivo = 1, data_fine = NULL 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Errore riattivazione prescrizione: " . $e->getMessage());
            return false;
        }
    }
}

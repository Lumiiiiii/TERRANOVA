<?php
/**
 * Classe Visit - Gestione visite
 */

require_once __DIR__ . '/../config/database.php';

class Visit {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Crea una nuova visita
     */
    public function createVisit($paziente_id, $data_visita = null, $note_finali = null) {
        try {
            // Se non viene specificata una data, usa la data odierna
            if ($data_visita === null) {
                $data_visita = date('Y-m-d');
            }
            
            $sql = "INSERT INTO visite (paziente_id, data_visita, note_finali) 
                    VALUES (:paziente_id, :data_visita, :note_finali)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':paziente_id' => $paziente_id,
                ':data_visita' => $data_visita,
                ':note_finali' => $note_finali
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Errore creazione visita: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ottiene i dettagli di una visita
     */
    public function getVisit($id) {
        try {
            $sql = "SELECT v.*, p.nome_cognome, p.data_nascita, p.telefono, p.email
                    FROM visite v
                    JOIN pazienti p ON v.paziente_id = p.id
                    WHERE v.id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Errore recupero visita: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Aggiorna una visita
     */
    public function updateVisit($id, $data_visita, $note_finali) {
        try {
            $sql = "UPDATE visite 
                    SET data_visita = :data_visita,
                        note_finali = :note_finali
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':data_visita' => $data_visita,
                ':note_finali' => $note_finali
            ]);
        } catch (PDOException $e) {
            error_log("Errore aggiornamento visita: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina una visita
     */
    public function deleteVisit($id) {
        try {
            $sql = "DELETE FROM visite WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Errore eliminazione visita: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ottiene lo storico delle visite di un paziente
     */
    public function getVisitHistory($paziente_id) {
        try {
            $sql = "SELECT v.id, v.data_visita, v.note_finali, v.data_creazione,
                           CASE WHEN a.id IS NOT NULL THEN 1 ELSE 0 END as ha_anamnesi
                    FROM visite v
                    LEFT JOIN anamnesi a ON v.id = a.visita_id
                    WHERE v.paziente_id = :paziente_id
                    ORDER BY v.data_visita DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':paziente_id' => $paziente_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore recupero storico visite: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Conta le visite di un paziente
     */
    public function countVisits($paziente_id) {
        try {
            $sql = "SELECT COUNT(*) as total FROM visite WHERE paziente_id = :paziente_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':paziente_id' => $paziente_id]);
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Errore conteggio visite: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Ottiene l'ultima visita di un paziente
     */
    public function getLastVisit($paziente_id) {
        try {
            $sql = "SELECT v.*, 
                           CASE WHEN a.id IS NOT NULL THEN 1 ELSE 0 END as ha_anamnesi
                    FROM visite v
                    LEFT JOIN anamnesi a ON v.id = a.visita_id
                    WHERE v.paziente_id = :paziente_id
                    ORDER BY v.data_visita DESC
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':paziente_id' => $paziente_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Errore recupero ultima visita: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ottiene tutte le visite recenti (tutte le pazienti)
     */
    public function getAllRecentVisits($limit = 20) {
        try {
            $sql = "SELECT v.id, v.data_visita, v.paziente_id,
                           p.nome_cognome,
                           CASE WHEN a.id IS NOT NULL THEN 1 ELSE 0 END as ha_anamnesi
                    FROM visite v
                    JOIN pazienti p ON v.paziente_id = p.id
                    LEFT JOIN anamnesi a ON v.id = a.visita_id
                    ORDER BY v.data_visita DESC
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore recupero visite recenti: " . $e->getMessage());
            return [];
        }
    }
}

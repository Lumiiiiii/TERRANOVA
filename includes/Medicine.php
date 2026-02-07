<?php
/**
 * Classe Medicine - Gestione medicinali
 */

require_once __DIR__ . '/../config/database.php';

class Medicine {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Ottiene tutti i medicinali attivi
     */
    public function getAllMedicines($includeInactive = false) {
        try {
            $sql = "SELECT * FROM medicinali";
            if (!$includeInactive) {
                $sql .= " WHERE attivo = 1";
            }
            $sql .= " ORDER BY tipologia, nome ASC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore recupero medicinali: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Ottiene medicinali filtrati per tipologia
     */
    public function getMedicinesByType($type) {
        try {
            $sql = "SELECT * FROM medicinali 
                    WHERE tipologia = :type AND attivo = 1
                    ORDER BY nome ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':type' => $type]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore recupero medicinali per tipo: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Ottiene un singolo medicinale per ID
     */
    public function getMedicineById($id) {
        try {
            $sql = "SELECT * FROM medicinali WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Errore recupero medicinale: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ricerca medicinali per nome
     */
    public function searchMedicines($query) {
        try {
            $sql = "SELECT * FROM medicinali 
                    WHERE (nome LIKE :query OR note LIKE :query) 
                      AND attivo = 1
                    ORDER BY nome ASC
                    LIMIT 50";
            
            $stmt = $this->db->prepare($sql);
            $searchTerm = '%' . $query . '%';
            $stmt->execute([':query' => $searchTerm]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore ricerca medicinali: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Aggiunge un nuovo medicinale
     */
    public function addMedicine($data) {
        try {
            $sql = "INSERT INTO medicinali (nome, tipologia, formato, dosaggio_standard, note, attivo) 
                    VALUES (:nome, :tipologia, :formato, :dosaggio_standard, :note, :attivo)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nome' => $data['nome'],
                ':tipologia' => $data['tipologia'] ?? 'Altro',
                ':formato' => $data['formato'] ?? null,
                ':dosaggio_standard' => $data['dosaggio_standard'] ?? null,
                ':note' => $data['note'] ?? null,
                ':attivo' => $data['attivo'] ?? 1
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Errore aggiunta medicinale: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Aggiorna un medicinale esistente
     */
    public function updateMedicine($id, $data) {
        try {
            $sql = "UPDATE medicinali 
                    SET nome = :nome,
                        tipologia = :tipologia,
                        formato = :formato,
                        dosaggio_standard = :dosaggio_standard,
                        note = :note,
                        attivo = :attivo
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nome' => $data['nome'],
                ':tipologia' => $data['tipologia'],
                ':formato' => $data['formato'] ?? null,
                ':dosaggio_standard' => $data['dosaggio_standard'] ?? null,
                ':note' => $data['note'] ?? null,
                ':attivo' => $data['attivo'] ?? 1
            ]);
        } catch (PDOException $e) {
            error_log("Errore aggiornamento medicinale: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Disattiva un medicinale (soft delete)
     */
    public function deleteMedicine($id) {
        try {
            $sql = "UPDATE medicinali SET attivo = 0 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Errore eliminazione medicinale: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Riattiva un medicinale
     */
    public function reactivateMedicine($id) {
        try {
            $sql = "UPDATE medicinali SET attivo = 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Errore riattivazione medicinale: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Conta i medicinali per tipologia
     */
    public function countByType() {
        try {
            $sql = "SELECT tipologia, COUNT(*) as count 
                    FROM medicinali 
                    WHERE attivo = 1
                    GROUP BY tipologia
                    ORDER BY tipologia";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore conteggio medicinali: " . $e->getMessage());
            return [];
        }
    }
}

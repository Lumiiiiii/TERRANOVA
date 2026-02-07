<?php
/**
 * Classe FoodRestrictions - Gestione alimenti da evitare
 */

require_once __DIR__ . '/../config/database.php';

class FoodRestrictions {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Aggiunge un alimento da evitare per un paziente
     */
    public function addFoodRestriction($paziente_id, $categoria, $alimento) {
        try {
            $sql = "INSERT INTO alimenti_evitare (paziente_id, categoria, alimento) 
                    VALUES (:paziente_id, :categoria, :alimento)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':paziente_id' => $paziente_id,
                ':categoria' => $categoria,
                ':alimento' => $alimento
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Errore aggiunta alimento da evitare: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ottiene tutti gli alimenti da evitare per un paziente
     */
    public function getFoodRestrictions($paziente_id, $only_active = true) {
        try {
            $sql = "SELECT * FROM alimenti_evitare 
                    WHERE paziente_id = :paziente_id";
            
            if ($only_active) {
                $sql .= " AND attivo = 1";
            }
            
            $sql .= " ORDER BY categoria, alimento";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':paziente_id' => $paziente_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore recupero alimenti da evitare: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Ottiene alimenti raggruppati per categoria
     */
    public function getFoodRestrictionsByCategory($paziente_id) {
        try {
            $restrictions = $this->getFoodRestrictions($paziente_id);
            $grouped = [];
            
            foreach ($restrictions as $restriction) {
                $categoria = $restriction['categoria'];
                if (!isset($grouped[$categoria])) {
                    $grouped[$categoria] = [];
                }
                $grouped[$categoria][] = $restriction;
            }
            
            return $grouped;
        } catch (Exception $e) {
            error_log("Errore raggruppamento alimenti: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Rimuove (disattiva) un alimento da evitare
     */
    public function removeFoodRestriction($id) {
        try {
            $sql = "UPDATE alimenti_evitare SET attivo = 0 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Errore rimozione alimento: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina definitivamente un alimento da evitare
     */
    public function deleteFoodRestriction($id) {
        try {
            $sql = "DELETE FROM alimenti_evitare WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Errore eliminazione alimento: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Riattiva un alimento precedentemente disattivato
     */
    public function reactivateFoodRestriction($id) {
        try {
            $sql = "UPDATE alimenti_evitare SET attivo = 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Errore riattivazione alimento: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ottiene tutte le categorie disponibili
     */
    public function getAllCategories() {
        try {
            $sql = "SELECT * FROM categorie_alimenti ORDER BY ordine, nome";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore recupero categorie: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Aggiunge più alimenti in una volta (utile per import)
     */
    public function addMultipleFoodRestrictions($paziente_id, $foods) {
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO alimenti_evitare (paziente_id, categoria, alimento) 
                    VALUES (:paziente_id, :categoria, :alimento)";
            $stmt = $this->db->prepare($sql);
            
            foreach ($foods as $food) {
                $stmt->execute([
                    ':paziente_id' => $paziente_id,
                    ':categoria' => $food['categoria'],
                    ':alimento' => $food['alimento']
                ]);
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Errore aggiunta multipla alimenti: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Conta gli alimenti da evitare per un paziente
     */
    public function countFoodRestrictions($paziente_id, $only_active = true) {
        try {
            $sql = "SELECT COUNT(*) as total FROM alimenti_evitare 
                    WHERE paziente_id = :paziente_id";
            
            if ($only_active) {
                $sql .= " AND attivo = 1";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':paziente_id' => $paziente_id]);
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Errore conteggio alimenti: " . $e->getMessage());
            return 0;
        }
    }
}

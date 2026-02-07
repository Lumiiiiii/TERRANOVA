<?php
/**
 * Classe Patient - Gestione pazienti
 */

require_once __DIR__ . '/../config/database.php';

class Patient {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Crea un nuovo paziente
     */
    public function createPatient($data) {
        try {
            $sql = "INSERT INTO pazienti (nome_cognome, data_nascita, indirizzo, telefono, email, professione) 
                    VALUES (:nome_cognome, :data_nascita, :indirizzo, :telefono, :email, :professione)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nome_cognome' => $data['nome_cognome'],
                ':data_nascita' => $data['data_nascita'] ?? null,
                ':indirizzo' => $data['indirizzo'] ?? null,
                ':telefono' => $data['telefono'] ?? null,
                ':email' => $data['email'] ?? null,
                ':professione' => $data['professione'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Errore creazione paziente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ottiene i dati di un paziente
     */
    public function getPatient($id) {
        try {
            $sql = "SELECT *, YEAR(CURDATE()) - YEAR(data_nascita) AS eta 
                    FROM pazienti WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Errore recupero paziente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Aggiorna i dati di un paziente
     */
    public function updatePatient($id, $data) {
        try {
            $sql = "UPDATE pazienti 
                    SET nome_cognome = :nome_cognome,
                        data_nascita = :data_nascita,
                        indirizzo = :indirizzo,
                        telefono = :telefono,
                        email = :email,
                        professione = :professione
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nome_cognome' => $data['nome_cognome'],
                ':data_nascita' => $data['data_nascita'] ?? null,
                ':indirizzo' => $data['indirizzo'] ?? null,
                ':telefono' => $data['telefono'] ?? null,
                ':email' => $data['email'] ?? null,
                ':professione' => $data['professione'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Errore aggiornamento paziente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina un paziente
     */
    public function deletePatient($id) {
        try {
            $sql = "DELETE FROM pazienti WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Errore eliminazione paziente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ottiene tutti i pazienti
     */
    public function getAllPatients($limit = 100, $offset = 0) {
        try {
            $sql = "SELECT id, nome_cognome, data_nascita, telefono, email, 
                           YEAR(CURDATE()) - YEAR(data_nascita) AS eta,
                           data_creazione
                    FROM pazienti 
                    ORDER BY nome_cognome ASC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore recupero pazienti: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Ricerca pazienti per nome
     */
    public function searchPatients($query) {
        try {
            $sql = "SELECT id, nome_cognome, data_nascita, telefono, email,
                           YEAR(CURDATE()) - YEAR(data_nascita) AS eta
                    FROM pazienti 
                    WHERE nome_cognome LIKE :query 
                       OR telefono LIKE :query
                       OR email LIKE :query
                    ORDER BY nome_cognome ASC
                    LIMIT 50";
            
            $stmt = $this->db->prepare($sql);
            $searchTerm = '%' . $query . '%';
            $stmt->execute([':query' => $searchTerm]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore ricerca pazienti: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Conta il numero totale di pazienti
     */
    public function countPatients() {
        try {
            $sql = "SELECT COUNT(*) as total FROM pazienti";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Errore conteggio pazienti: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Ottiene i pazienti recenti (ultimi 10)
     */
    public function getRecentPatients($limit = 10) {
        try {
            $sql = "SELECT id, nome_cognome, data_nascita, telefono,
                           YEAR(CURDATE()) - YEAR(data_nascita) AS eta,
                           data_creazione
                    FROM pazienti 
                    ORDER BY data_creazione DESC
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore recupero pazienti recenti: " . $e->getMessage());
            return [];
        }
    }
}

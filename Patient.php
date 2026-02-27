<?php
/**
 * Classe Patient - Gestione pazienti
 * Semplificata per il nuovo schema DB
 */

require_once __DIR__ . '/../config/database.php';

class Patient
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    // Crea nuovo paziente
    public function createPatient($data)
    {
        try {
            $sql = "INSERT INTO pazienti (nome_cognome, data_nascita, telefono, indirizzo, email, professione) 
                    VALUES (:nome_cognome, :data_nascita, :telefono, :indirizzo, :email, :professione)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nome_cognome' => $data['nome_cognome'],
                ':data_nascita' => !empty($data['data_nascita']) ? $data['data_nascita'] : null,
                ':telefono' => $data['telefono'] ?? null,
                ':indirizzo' => $data['indirizzo'] ?? null,
                ':email' => $data['email'] ?? null,
                ':professione' => $data['professione'] ?? null
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Errore crea paziente: " . $e->getMessage());
            return false;
        }
    }

    // Ottieni dati paziente
    public function getPatient($id)
    {
        try {
            $sql = "SELECT *, TIMESTAMPDIFF(YEAR, data_nascita, CURDATE()) AS eta 
                    FROM pazienti WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Aggiorna dati paziente
    public function updatePatient($id, $data)
    {
        try {
            $sql = "UPDATE pazienti 
                    SET nome_cognome = :nome_cognome,
                        data_nascita = :data_nascita,
                        telefono = :telefono,
                        indirizzo = :indirizzo,
                        email = :email,
                        professione = :professione
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nome_cognome' => $data['nome_cognome'],
                ':data_nascita' => !empty($data['data_nascita']) ? $data['data_nascita'] : null,
                ':telefono' => $data['telefono'] ?? null,
                ':indirizzo' => $data['indirizzo'] ?? null,
                ':email' => $data['email'] ?? null,
                ':professione' => $data['professione'] ?? null
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Elimina paziente
    public function deletePatient($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM pazienti WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Lista pazienti recenti
    public function getRecentPatients($limit = 10)
    {
        try {
            $sql = "SELECT *, TIMESTAMPDIFF(YEAR, data_nascita, CURDATE()) AS eta 
                    FROM pazienti 
                    ORDER BY data_creazione DESC 
                    LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // Conta totale
    public function countPatients()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM pazienti");
        return $stmt->fetch()['total'];
    }

    // Cerca pazienti (semplificato)
    public function searchPatients($query)
    {
        $term = "%$query%";
        $sql = "SELECT *, TIMESTAMPDIFF(YEAR, data_nascita, CURDATE()) AS eta 
                FROM pazienti 
                WHERE nome_cognome LIKE ? OR telefono LIKE ? OR email LIKE ?
                LIMIT 20";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$term, $term, $term]);
        return $stmt->fetchAll();
    }
}


<?php
/**
 * Classe Medicine - Gestione Medicinali
 */
require_once __DIR__ . '/../config/database.php';

class Medicine
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAllMedicines($includeInactive = false)
    {
        $sql = "SELECT * FROM medicinali";
        if (!$includeInactive) {
            $sql .= " WHERE attivo = 1";
        }
        $sql .= " ORDER BY nome ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getMedicineById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM medicinali WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createMedicine($data)
    {
        $sql = "INSERT INTO medicinali (nome, tipologia, formato, dosaggio_standard, note, attivo) 
                VALUES (:nome, :tipologia, :formato, :dosaggio, :note, 1)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome' => $data['nome'],
            ':tipologia' => $data['tipologia'] ?? '',
            ':formato' => $data['formato'] ?? '',
            ':dosaggio' => $data['dosaggio_standard'] ?? '',
            ':note' => $data['note'] ?? ''
        ]);
    }

    public function updateMedicine($id, $data)
    {
        $sql = "UPDATE medicinali SET nome=:nome, tipologia=:tipologia, formato=:formato, 
                dosaggio_standard=:dosaggio, note=:note WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nome' => $data['nome'],
            ':tipologia' => $data['tipologia'] ?? '',
            ':formato' => $data['formato'] ?? '',
            ':dosaggio' => $data['dosaggio_standard'] ?? '',
            ':note' => $data['note'] ?? ''
        ]);
    }

    // Soft Delete
    public function deleteMedicine($id)
    {
        $stmt = $this->db->prepare("UPDATE medicinali SET attivo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function reactivateMedicine($id)
    {
        $stmt = $this->db->prepare("UPDATE medicinali SET attivo = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countByType()
    {
        $stmt = $this->db->query("SELECT tipologia, COUNT(*) as count FROM medicinali WHERE attivo = 1 GROUP BY tipologia");
        return $stmt->fetchAll();
    }

    public function searchMedicines($query)
    {
        $term = "%$query%";
        $stmt = $this->db->prepare("SELECT * FROM medicinali WHERE nome LIKE ? AND attivo = 1 ORDER BY nome ASC");
        $stmt->execute([$term]);
        return $stmt->fetchAll();
    }
}

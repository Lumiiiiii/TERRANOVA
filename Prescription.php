<?php
/**
 * Classe Prescription - Gestione Prescrizioni
 */
require_once __DIR__ . '/../config/database.php';

class Prescription
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getPrescriptionsByPatient($paziente_id, $onlyActive = false)
    {
        $sql = "SELECT p.*, p.id as prescrizione_id, m.nome as medicinale_nome, m.tipologia as medicinale_tipologia 
                FROM prescrizioni p
                JOIN medicinali m ON p.medicinale_id = m.id
                WHERE p.paziente_id = ?";

        if ($onlyActive) {
            $sql .= " AND p.attivo = 1";
        }

        $sql .= " ORDER BY p.attivo DESC, p.data_inizio DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$paziente_id]);
        return $stmt->fetchAll();
    }

    public function getPrescriptionsByVisit($visita_id)
    {
        $sql = "SELECT p.*, m.nome as medicinale_nome 
                FROM prescrizioni p
                JOIN medicinali m ON p.medicinale_id = m.id
                WHERE p.visita_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$visita_id]);
        return $stmt->fetchAll();
    }

    public function createPrescription($data)
    {
        $sql = "INSERT INTO prescrizioni (paziente_id, medicinale_id, visita_id, dosaggio, frequenza, durata, note_prescrizione, data_inizio, data_fine, attivo)
                VALUES (:pid, :mid, :vid, :dosaggio, :freq, :durata, :note, :start, :end, 1)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':pid' => $data['paziente_id'],
            ':mid' => $data['medicinale_id'],
            ':vid' => !empty($data['visita_id']) ? $data['visita_id'] : null,
            ':dosaggio' => $data['dosaggio'] ?? '',
            ':freq' => $data['frequenza'] ?? '',
            ':durata' => $data['durata'] ?? '',
            ':note' => $data['note_prescrizione'] ?? '',
            ':start' => !empty($data['data_inizio']) ? $data['data_inizio'] : date('Y-m-d'),
            ':end' => !empty($data['data_fine']) ? $data['data_fine'] : null
        ]);
    }

    public function updatePrescription($id, $data)
    {
        $sql = "UPDATE prescrizioni SET 
                dosaggio = :dosaggio, 
                frequenza = :frequenza, 
                durata = :durata, 
                note_prescrizione = :note,
                data_fine = :end
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':dosaggio' => $data['dosaggio'] ?? '',
            ':frequenza' => $data['frequenza'] ?? '',
            ':durata' => $data['durata'] ?? '',
            ':note' => $data['note_prescrizione'] ?? '',
            ':end' => $data['data_fine'] ?? null
        ]);
    }

    public function stopPrescription($id)
    {
        $stmt = $this->db->prepare("UPDATE prescrizioni SET attivo = 0, data_fine = CURDATE() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deletePrescription($id)
    {
        $stmt = $this->db->prepare("DELETE FROM prescrizioni WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function reactivatePrescription($id)
    {
        $stmt = $this->db->prepare("UPDATE prescrizioni SET attivo = 1, data_fine = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

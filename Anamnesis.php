<?php
/**
 * Classe Anamnesi - Gestione Anamnesi Generale (Storia clinica fissa)
 */
require_once __DIR__ . '/../config/database.php';

class Anamnesis
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    // Ottieni anamnesi generale paziente
    public function getAnamnesis($paziente_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM anamnesi WHERE paziente_id = ?");
        $stmt->execute([$paziente_id]);
        return $stmt->fetch() ?: [];
    }

    // Salva o Aggiorna Anamnesi Generale
    public function saveAnamnesis($paziente_id, $data)
    {
        // Verifica se esiste già
        $exists = $this->getAnamnesis($paziente_id);

        if ($exists) {
            $sql = "UPDATE anamnesi SET 
                    allergie_intolleranze = :allergie,
                    patologie_pregresse = :patologie,
                    interventi_chirurgici = :interventi,
                    esami_clinici_recenti = :esami,
                    terapie_farmacologiche_croniche = :terapie,
                    alcol = :alcol,
                    fumo = :fumo,
                    traumi_o_fratture = :traumi
                    WHERE paziente_id = :pid";
        } else {
            $sql = "INSERT INTO anamnesi (paziente_id, allergie_intolleranze, patologie_pregresse, interventi_chirurgici, esami_clinici_recenti, terapie_farmacologiche_croniche, alcol, fumo, traumi_o_fratture)
                    VALUES (:pid, :allergie, :patologie, :interventi, :esami, :terapie, :alcol, :fumo, :traumi)";
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':pid' => $paziente_id,
            ':allergie' => $data['allergie_intolleranze'] ?? '',
            ':patologie' => $data['patologie_pregresse'] ?? '',
            ':interventi' => $data['interventi_chirurgici'] ?? '',
            ':esami' => $data['esami_clinici_recenti'] ?? '',
            ':terapie' => $data['terapie_farmacologiche_croniche'] ?? '',
            ':alcol' => $data['alcol'] ?? '',
            ':fumo' => $data['fumo'] ?? '',
            ':traumi' => $data['traumi_o_fratture'] ?? ''
        ]);
    }
}

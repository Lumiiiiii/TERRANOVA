<?php
/**
 * Classe Anamnesis - Gestione anamnesi delle visite
 */

require_once __DIR__ . '/../config/database.php';

class Anamnesis {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Salva una nuova anamnesi per una visita
     */
    public function saveAnamnesis($visita_id, $data) {
        try {
            $sql = "INSERT INTO anamnesi (
                        visita_id, 
                        vomito, vomito_dettagli, 
                        febbre, febbre_dettagli,
                        flusso, flusso_dettagli, 
                        alcol, alcol_dettagli,
                        patologie, interventi_chirurgici, fratture_traumi,
                        qualita_sonno, ore_sonno, risvegli_notturni, 
                        difficolta_addormentarsi, qualita_risveglio,
                        livello_stress,
                        appetito, ansia, umore, motivazione, concentrazione,
                        attivita_fisica_frequenza, attivita_fisica_tipo,
                        alimentazione_generale,
                        farmaci_categoria, farmaci_specifiche,
                        integratori_categoria, integratori_specifiche,
                        rimedi_naturali, terapie_corso,
                        osservazioni_finali
                    ) VALUES (
                        :visita_id,
                        :vomito, :vomito_dettagli,
                        :febbre, :febbre_dettagli,
                        :flusso, :flusso_dettagli,
                        :alcol, :alcol_dettagli,
                        :patologie, :interventi_chirurgici, :fratture_traumi,
                        :qualita_sonno, :ore_sonno, :risvegli_notturni,
                        :difficolta_addormentarsi, :qualita_risveglio,
                        :livello_stress,
                        :appetito, :ansia, :umore, :motivazione, :concentrazione,
                        :attivita_fisica_frequenza, :attivita_fisica_tipo,
                        :alimentazione_generale,
                        :farmaci_categoria, :farmaci_specifiche,
                        :integratori_categoria, :integratori_specifiche,
                        :rimedi_naturali, :terapie_corso,
                        :osservazioni_finali
                    )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':visita_id' => $visita_id,
                ':vomito' => $data['vomito'] ?? 'No',
                ':vomito_dettagli' => $data['vomito_dettagli'] ?? null,
                ':febbre' => $data['febbre'] ?? 'No',
                ':febbre_dettagli' => $data['febbre_dettagli'] ?? null,
                ':flusso' => $data['flusso'] ?? 'No',
                ':flusso_dettagli' => $data['flusso_dettagli'] ?? null,
                ':alcol' => $data['alcol'] ?? 'No',
                ':alcol_dettagli' => $data['alcol_dettagli'] ?? null,
                ':patologie' => $data['patologie'] ?? null,
                ':interventi_chirurgici' => $data['interventi_chirurgici'] ?? null,
                ':fratture_traumi' => $data['fratture_traumi'] ?? null,
                ':qualita_sonno' => $data['qualita_sonno'] ?? null,
                ':ore_sonno' => $data['ore_sonno'] ?? null,
                ':risvegli_notturni' => isset($data['risvegli_notturni']) ? 1 : 0,
                ':difficolta_addormentarsi' => isset($data['difficolta_addormentarsi']) ? 1 : 0,
                ':qualita_risveglio' => $data['qualita_risveglio'] ?? null,
                ':livello_stress' => $data['livello_stress'] ?? null,
                ':appetito' => $data['appetito'] ?? null,
                ':ansia' => $data['ansia'] ?? null,
                ':umore' => $data['umore'] ?? null,
                ':motivazione' => $data['motivazione'] ?? null,
                ':concentrazione' => $data['concentrazione'] ?? null,
                ':attivita_fisica_frequenza' => $data['attivita_fisica_frequenza'] ?? null,
                ':attivita_fisica_tipo' => $data['attivita_fisica_tipo'] ?? null,
                ':alimentazione_generale' => $data['alimentazione_generale'] ?? null,
                ':farmaci_categoria' => $data['farmaci_categoria'] ?? null,
                ':farmaci_specifiche' => $data['farmaci_specifiche'] ?? null,
                ':integratori_categoria' => $data['integratori_categoria'] ?? null,
                ':integratori_specifiche' => $data['integratori_specifiche'] ?? null,
                ':rimedi_naturali' => $data['rimedi_naturali'] ?? null,
                ':terapie_corso' => $data['terapie_corso'] ?? null,
                ':osservazioni_finali' => $data['osservazioni_finali'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Errore salvataggio anamnesi: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ottiene l'anamnesi di una visita
     */
    public function getAnamnesis($visita_id) {
        try {
            $sql = "SELECT * FROM anamnesi WHERE visita_id = :visita_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':visita_id' => $visita_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Errore recupero anamnesi: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Aggiorna un'anamnesi esistente
     */
    public function updateAnamnesis($visita_id, $data) {
        try {
            $sql = "UPDATE anamnesi SET
                        vomito = :vomito, vomito_dettagli = :vomito_dettagli,
                        febbre = :febbre, febbre_dettagli = :febbre_dettagli,
                        flusso = :flusso, flusso_dettagli = :flusso_dettagli,
                        alcol = :alcol, alcol_dettagli = :alcol_dettagli,
                        patologie = :patologie,
                        interventi_chirurgici = :interventi_chirurgici,
                        fratture_traumi = :fratture_traumi,
                        qualita_sonno = :qualita_sonno,
                        ore_sonno = :ore_sonno,
                        risvegli_notturni = :risvegli_notturni,
                        difficolta_addormentarsi = :difficolta_addormentarsi,
                        qualita_risveglio = :qualita_risveglio,
                        livello_stress = :livello_stress,
                        appetito = :appetito,
                        ansia = :ansia,
                        umore = :umore,
                        motivazione = :motivazione,
                        concentrazione = :concentrazione,
                        attivita_fisica_frequenza = :attivita_fisica_frequenza,
                        attivita_fisica_tipo = :attivita_fisica_tipo,
                        alimentazione_generale = :alimentazione_generale,
                        farmaci_categoria = :farmaci_categoria,
                        farmaci_specifiche = :farmaci_specifiche,
                        integratori_categoria = :integratori_categoria,
                        integratori_specifiche = :integratori_specifiche,
                        rimedi_naturali = :rimedi_naturali,
                        terapie_corso = :terapie_corso,
                        osservazioni_finali = :osservazioni_finali
                    WHERE visita_id = :visita_id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':visita_id' => $visita_id,
                ':vomito' => $data['vomito'] ?? 'No',
                ':vomito_dettagli' => $data['vomito_dettagli'] ?? null,
                ':febbre' => $data['febbre'] ?? 'No',
                ':febbre_dettagli' => $data['febbre_dettagli'] ?? null,
                ':flusso' => $data['flusso'] ?? 'No',
                ':flusso_dettagli' => $data['flusso_dettagli'] ?? null,
                ':alcol' => $data['alcol'] ?? 'No',
                ':alcol_dettagli' => $data['alcol_dettagli'] ?? null,
                ':patologie' => $data['patologie'] ?? null,
                ':interventi_chirurgici' => $data['interventi_chirurgici'] ?? null,
                ':fratture_traumi' => $data['fratture_traumi'] ?? null,
                ':qualita_sonno' => $data['qualita_sonno'] ?? null,
                ':ore_sonno' => $data['ore_sonno'] ?? null,
                ':risvegli_notturni' => isset($data['risvegli_notturni']) ? 1 : 0,
                ':difficolta_addormentarsi' => isset($data['difficolta_addormentarsi']) ? 1 : 0,
                ':qualita_risveglio' => $data['qualita_risveglio'] ?? null,
                ':livello_stress' => $data['livello_stress'] ?? null,
                ':appetito' => $data['appetito'] ?? null,
                ':ansia' => $data['ansia'] ?? null,
                ':umore' => $data['umore'] ?? null,
                ':motivazione' => $data['motivazione'] ?? null,
                ':concentrazione' => $data['concentrazione'] ?? null,
                ':attivita_fisica_frequenza' => $data['attivita_fisica_frequenza'] ?? null,
                ':attivita_fisica_tipo' => $data['attivita_fisica_tipo'] ?? null,
                ':alimentazione_generale' => $data['alimentazione_generale'] ?? null,
                ':farmaci_categoria' => $data['farmaci_categoria'] ?? null,
                ':farmaci_specifiche' => $data['farmaci_specifiche'] ?? null,
                ':integratori_categoria' => $data['integratori_categoria'] ?? null,
                ':integratori_specifiche' => $data['integratori_specifiche'] ?? null,
                ':rimedi_naturali' => $data['rimedi_naturali'] ?? null,
                ':terapie_corso' => $data['terapie_corso'] ?? null,
                ':osservazioni_finali' => $data['osservazioni_finali'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Errore aggiornamento anamnesi: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina un'anamnesi
     */
    public function deleteAnamnesis($visita_id) {
        try {
            $sql = "DELETE FROM anamnesi WHERE visita_id = :visita_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':visita_id' => $visita_id]);
        } catch (PDOException $e) {
            error_log("Errore eliminazione anamnesi: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica se una visita ha già un'anamnesi
     */
    public function hasAnamnesis($visita_id) {
        try {
            $sql = "SELECT COUNT(*) as count FROM anamnesi WHERE visita_id = :visita_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':visita_id' => $visita_id]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Errore verifica anamnesi: " . $e->getMessage());
            return false;
        }
    }
}

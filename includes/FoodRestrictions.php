<?php
/**
 * Classe FoodRestrictions - Gestione Alimenti da Evitare
 */
require_once __DIR__ . '/../config/database.php';

class FoodRestrictions
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getRestrictions($paziente_id)
    {
        $sql = "SELECT ae.*, la.nome as alimento_nome, la.categoria 
                FROM alimenti_evitare ae
                JOIN lista_alimenti la ON ae.lista_alimenti_id = la.id
                WHERE ae.paziente_id = ? AND ae.attivo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$paziente_id]);
        return $stmt->fetchAll();
    }

    public function getFoodRestrictionsByCategory($paziente_id)
    {
        $restrictions = $this->getRestrictions($paziente_id);
        $grouped = [];
        foreach ($restrictions as $r) {
            $cat = $r['categoria'] ?: 'Altro';
            $grouped[$cat][] = [
                'id' => $r['id'],
                'alimento' => $r['alimento_nome'],
                'categoria' => $cat
            ];
        }
        return $grouped;
    }

    public function getAllFoods()
    {
        $stmt = $this->db->query("SELECT * FROM lista_alimenti ORDER BY nome");
        return $stmt->fetchAll();
    }

    public function getAllCategories()
    {
        $stmt = $this->db->query("SELECT DISTINCT categoria as nome FROM lista_alimenti WHERE categoria IS NOT NULL ORDER BY categoria");
        return $stmt->fetchAll();
    }

    public function addRestriction($paziente_id, $alimento)
    {
        // Cerca ID alimento o crealo se non esiste (se stiamo passando una stringa)
        // Se $alimento è ID (numeric), usalo direttamente.
        // Se è stringa (nuovo alimento), logicamente dovremmo gestirlo, ma il frontend manda 'alimento' e 'categoria'.

        // Controlliamo se riceviamo ID o nome
        if (is_numeric($alimento)) {
            $alimento_id = $alimento;
        } else {
            // Cerca
            $stmt = $this->db->prepare("SELECT id FROM lista_alimenti WHERE nome = ?");
            $stmt->execute([$alimento]);
            $row = $stmt->fetch();
            if ($row) {
                $alimento_id = $row['id'];
            } else {
                // Crea
                // Nota: addRestriction nel frontend passava 'categoria' nel POST, ma qui non la vedo nell'argomento.
                // Modifico addRestriction per accettare categoria opzionale se creo.
                // Ma per semplicità, se non esiste, lo creo come 'Altro' o cerco di recuperarlo dal POST se possibile?
                // Meglio: il controller (ajax_handlers) riceve POST['categoria'] e POST['alimento'].
                // Qui modifico la firma.
                return false; // See specialized method below
            }
        }

        $stmt = $this->db->prepare("INSERT INTO alimenti_evitare (paziente_id, lista_alimenti_id) VALUES (?, ?)");
        return $stmt->execute([$paziente_id, $alimento_id]);
    }

    public function addFoodRestriction($paziente_id, $categoria, $nome_alimento)
    {
        // Cerca se esiste nella lista globale
        $stmt = $this->db->prepare("SELECT id FROM lista_alimenti WHERE nome = ?");
        $stmt->execute([$nome_alimento]);
        $row = $stmt->fetch();

        if ($row) {
            $alimento_id = $row['id'];
        } else {
            // Crea nuovo alimento in lista
            $stmt = $this->db->prepare("INSERT INTO lista_alimenti (nome, categoria) VALUES (?, ?)");
            $stmt->execute([$nome_alimento, $categoria]);
            $alimento_id = $this->db->lastInsertId();
        }

        // Associa al paziente
        return $this->addRestriction($paziente_id, $alimento_id);
    }

    public function removeRestriction($id)
    {
        $stmt = $this->db->prepare("DELETE FROM alimenti_evitare WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Alias for removeRestriction if manager calls removeFoodRestriction
    public function removeFoodRestriction($id)
    {
        return $this->removeRestriction($id);
    }

    public function countRestrictions($paziente_id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM alimenti_evitare WHERE paziente_id = ? AND attivo = 1");
        $stmt->execute([$paziente_id]);
        return $stmt->fetch()['total'];
    }

    public function countFoodRestrictions($paziente_id)
    {
        return $this->countRestrictions($paziente_id);
    }
}

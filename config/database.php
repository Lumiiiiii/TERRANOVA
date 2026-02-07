<?php
/**
 * Database Configuration
 * Configurazione connessione MySQL per il gestionale naturologa
 */

// Configurazione database
define('DB_HOST', 'localhost');
define('DB_NAME', 'naturologa_db');
define('DB_USER', 'root');  // Modifica con il tuo username MySQL
define('DB_PASS', '');      // Modifica con la tua password MySQL
define('DB_CHARSET', 'utf8mb4');

/**
 * Classe Database - Gestione connessione PDO
 */
class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Costruttore privato (Singleton pattern)
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Errore di connessione al database: " . $e->getMessage());
        }
    }
    
    /**
     * Ottiene l'istanza singleton del database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Ottiene la connessione PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Previene la clonazione dell'oggetto
     */
    private function __clone() {}
    
    /**
     * Previene l'unserialize dell'oggetto
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Funzione helper per ottenere la connessione database
 */
function getDB() {
    return Database::getInstance()->getConnection();
}
